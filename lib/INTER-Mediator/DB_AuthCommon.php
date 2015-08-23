<?php

/*
* INTER-Mediator Ver.5.2 Released 2015-08-24
*
*   Copyright (c) 2010-2015 INTER-Mediator Directive Committee, All rights reserved.
*
*   This project started at the end of 2009 by Masayuki Nii  msyk@msyk.net.
*   INTER-Mediator is supplied under MIT License.
*/

abstract class DB_AuthCommon extends DB_UseSharedObjects implements Auth_Interface_CommonDB
{

    private function getOperationSeries($operation)    {
        $operations = array();
        if (($operation === 'select') || ($operation === 'load') || ($operation === 'read')) {
            $operations = array('read', 'select', 'load');
        } else if (($operation === 'update') || ($operation === 'edit')) {
            $operations = array('update', 'edit');
        } else if (($operation === 'create') || ($operation === 'new')) {
            $operations = array('create', 'new');
        } else if ($operation === 'delete') {
            $operations = array('delete');
        }
        return $operations;
    }

    function getFieldForAuthorization($operation)
    {
        $operations = $this->getOperationSeries($operation);
        $tableInfo = $this->dbSettings->getDataSourceTargetArray();
        $authInfoField = null;
        if (isset($tableInfo['authentication']['all']['field'])) {
            $authInfoField = $tableInfo['authentication']['all']['field'];
        }
        foreach ($operations as $op) {
            if (isset($tableInfo['authentication'][$op]['field'])) {
                $authInfoField = $tableInfo['authentication'][$op]['field'];
                break;
            }
        }
        return $authInfoField;
    }

    function getTargetForAuthorization($operation)
    {
        $operations = $this->getOperationSeries($operation);
        $tableInfo = $this->dbSettings->getDataSourceTargetArray();
        $authInfoTarget = null;
        if (isset($tableInfo['authentication']['all']['target'])) {
            $authInfoTarget = $tableInfo['authentication']['all']['target'];
        }
        foreach ($operations as $op) {
            if (isset($tableInfo['authentication'][$op]['field'])) {
                $authInfoTarget = $tableInfo['authentication'][$op]['field'];
                break;
            }
        }
        return $authInfoTarget;
    }

    function getAuthorizedUsers($operation = null)
    {
        $operations = $this->getOperationSeries($operation);
        $tableInfo = $this->dbSettings->getDataSourceTargetArray();
        $usersArray = array();
        if ($this->dbSettings->getAuthenticationItem('user')) {
            $usersArray = array_merge($usersArray, $this->dbSettings->getAuthenticationItem('user'));
        }
        if (isset($tableInfo['authentication']['all']['user'])) {
            $usersArray = array_merge($usersArray, $tableInfo['authentication']['all']['user']);
        }
        foreach ($operations as $op) {
            if (isset($tableInfo['authentication'][$op]['user'])) {
                $usersArray = array_merge($usersArray, $tableInfo['authentication'][$op]['user']);
                break;
            }
        }
        return $usersArray;
    }

    function getAuthorizedGroups($operation = null)
    {
        $operations = $this->getOperationSeries($operation);
        $tableInfo = $this->dbSettings->getDataSourceTargetArray();
        $groupsArray = array();
        if ($this->dbSettings->getAuthenticationItem('group')) {
            $groupsArray = array_merge($groupsArray, $this->dbSettings->getAuthenticationItem('group'));
        }
        if (isset($tableInfo['authentication']['all']['group'])) {
            $groupsArray = array_merge($groupsArray, $tableInfo['authentication']['all']['group']);
        }
        foreach ($operations as $op) {
            if (isset($tableInfo['authentication'][$op]['group'])) {
                $groupsArray = array_merge($groupsArray, $tableInfo['authentication'][$op]['group']);
                break;
            }
        }
        return $groupsArray;
    }

}
