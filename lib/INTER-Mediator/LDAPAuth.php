<?php

/*
* INTER-Mediator Ver.5.2 Released 2015-08-24
*
*   Copyright (c) 2010-2015 INTER-Mediator Directive Committee, All rights reserved.
*
*   This project started at the end of 2009 by Masayuki Nii  msyk@msyk.net.
*   INTER-Mediator is supplied under MIT License.
*/

class LDAPAuth
{
    public $errorString;
    public $isActive;

    private $server = "";
    private $port = 389;
    private $base = "";
    private $container = "";
    private $accountKey = "uid";

    public function __construct()
    {
        $ldapServer = "";
        $ldapPort = "";
        $ldapBase = "";
        $ldapContainer = "";
        $ldapAccountKey = "";

        $currentDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $currentDirParam = $currentDir . 'params.php';
        $parentDirParam = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'params.php';
        if (file_exists($parentDirParam)) {
            include($parentDirParam);
        } else if (file_exists($currentDirParam)) {
            include($currentDirParam);
        }
        $this->server = $ldapServer;
        $this->port = $ldapPort;
        $this->base = $ldapBase;
        $this->container = $ldapContainer;
        $this->accountKey = $ldapAccountKey;

        $this->isActive = (strlen($this->server) > 0);
    }

    function bindCheck($username, $password)
    {
        $this->errorString = "";
        if (! $this->isActive)  {
            $this->errorString = "LDAP Setting isn't supplied.";
            return false;
        }
        if (! $username || ! $password)  {
            $this->errorString = "Account Info isn't supplied.";
            return false;
        }
        $ds = ldap_connect($this->server, $this->port);
        if (!$ds) {
            $this->errorString = ldap_err2str(ldap_errno($ds));
            return false;
        }
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
        $rdn = "{$this->accountKey}={$username},{$this->container},{$this->base}";
        try {
            $currentErrorReporting = error_reporting();
            error_reporting(0);
            $r = ldap_bind($ds, $rdn, $password);
            error_reporting($currentErrorReporting);
        } catch (Exception $e) {
            $this->errorString = ldap_err2str(ldap_errno($ds)) . " by {$rdn}";
            $r = false;
        }
        ldap_close($ds);
        return $r;
    }
}
