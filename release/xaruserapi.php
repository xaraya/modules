<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: John Cox via phpMailer Team
// Purpose of file: standard mail output
// ----------------------------------------------------------------------

/**
 * get all users
 * @returns array
 * @return array of users, or false on failure
 */
function release_userapi_getallids($args)
{
    extract($args);

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $releaseinfo = array();

    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_OVERVIEW)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    $query = "SELECT xar_rid,
                   xar_name,
                   xar_desc,
                   xar_type,
                   xar_approved
            FROM $releasetable
            ORDER BY xar_rid";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rid, $name, $desc, $type, $approved) = $result->fields;
        if (xarSecAuthAction(0, 'release::', "::", ACCESS_OVERVIEW)) {
            $releaseinfo[] = array('rid'        => $rid,
                                   'name'       => $name,
                                   'desc'       => $desc,
                                   'type'       => $type,
                                   'approved'   => $approved);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

function release_userapi_createid($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if ((!isset($name)) ||
        (!isset($type))) {

        $msg = xarML('Wrong arguments to release_userapi_create.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Check if that username exists
    $query = "SELECT xar_rid FROM $releasetable
            WHERE xar_name='".xarVarPrepForStore($name)."';";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if ($result->RecordCount() > 0) {
        return; 
    }

    if (empty($approved)){
        $approved = 1;
    }

    $query = "INSERT INTO $releasetable (
              xar_rid,
              xar_name,
              xar_desc,
              xar_type,
              xar_approved
              )
            VALUES (
              '" . xarVarPrepForStore($rid) . "',
              '" . xarVarPrepForStore($name) . "',
              '" . xarVarPrepForStore($desc) . "',
              '" . xarVarPrepForStore($type) . "',
              '" . xarVarPrepForStore($approved) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

}

function release_userapi_getmenulinks()
{
    if (xarSecAuthAction(0, 'users::', '::', ACCESS_OVERVIEW)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'viewids'),
                             'title' => xarML('View all theme and module IDs'),
                             'label' => xarML('View IDs'));


        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'addid'),
                             'title' => xarML('Add a module or theme ID so it will not be duplicated'),
                             'label' => xarML('Add ID'));

    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>