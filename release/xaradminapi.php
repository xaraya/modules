<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: John Cox via phpMailer Team
// Purpose of file: srtandard mail output
// ----------------------------------------------------------------------

function release_adminapi_updatenote($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if  (!isset($rnid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'update', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('release',
                          'user',
                          'getnote',
                          array('rnid' => $rnid));

    if ($link == false) {
        $msg = xarML('No Such Release Note Present',
                    'release');
        xarExceptionSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    if (!xarSecAuthAction(0, 'Release::', "::", ACCESS_READ)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasenotetable = $xartable['release_notes'];

    // Update the link
    $query = "UPDATE $releasenotetable
            SET xar_version = '" . xarVarPrepForStore($version) . "',
                xar_priceterms = '" . xarVarPrepForStore($priceterms) . "',
                xar_demolink = '" . xarVarPrepForStore($demolink) . "',
                xar_priceterms = '" . xarVarPrepForStore($priceterms) . "',
                xar_dllink = '" . xarVarPrepForStore($dllink) . "',
                xar_supportlink = '" . xarVarPrepForStore($supportlink) . "',
                xar_changelog = '" . xarVarPrepForStore($changelog) . "',
                xar_notes = '" . xarVarPrepForStore($notes) . "',
                xar_enotes = '" . xarVarPrepForStore($enotes) . "',
                xar_certified = '" . xarVarPrepForStore($certified) . "',
                xar_approved = '" . xarVarPrepForStore($approved) . "'
            WHERE xar_rnid = " . xarVarPrepForStore($rnid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'update', $rnid, 'rnid');

    // Return the id of the newly created user to the calling process
    return $rnid;
}

function release_adminapi_deletenote($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($rnid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'delete', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('release',
                          'user',
                          'getnote',
                         array('rnid' => $rnid));

    if ($link == false) {
        $msg = xarML('No Such Link Present',
                    'autolinks');
        xarExceptionSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    // Security check
    if (!xarSecAuthAction(0, 'Release::', "$link[rid]::$rnid", ACCESS_DELETE)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasenotetable = $xartable['release_notes'];

    // Delete the item
    $query = "DELETE FROM $releasenotetable
            WHERE xar_rnid = " . xarVarPrepForStore($rnid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $rnid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

function release_adminapi_deleteid($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($rid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'delete', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('release',
                          'user',
                          'getid',
                         array('rid' => $rid));

    if ($link == false) {
        $msg = xarML('No Such Link Present',
                    'autolinks');
        xarExceptionSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    // Security check
    if (!xarSecAuthAction(0, 'Release::', "::", ACCESS_DELETE)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Delete the item
    $query = "DELETE FROM $releasetable
            WHERE xar_rid = " . xarVarPrepForStore($rid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $rid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

function release_adminapi_deletedoc($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($rdid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'delete', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('release',
                          'user',
                          'getdoc',
                         array('rdid' => $rdid));

    if ($link == false) {
        $msg = xarML('No Such Link Present',
                    'autolinks');
        xarExceptionSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    // Security check
    if (!xarSecAuthAction(0, 'Release::', "::", ACCESS_DELETE)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_docs'];

    // Delete the item
    $query = "DELETE FROM $releasetable
            WHERE xar_rnid = " . xarVarPrepForStore($rdid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $rdid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

function release_adminapi_getmenulinks()
{
    if (xarSecAuthAction(0, 'release::', '::', ACCESS_ADMIN)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'addcore'),
                             'title' => xarML('Add Core Notifications'),
                             'label' => xarML('Add Core Release'));

     }
    if (xarSecAuthAction(0, 'release::', '::', ACCESS_EDIT)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'viewids'),
                             'title' => xarML('View Registered IDs on the system'),
                             'label' => xarML('View IDs'));

     }
    if (xarSecAuthAction(0, 'release::', '::', ACCESS_EDIT)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'viewnotes'),
                             'title' => xarML('View Release Notifications'),
                             'label' => xarML('View Notifications'));

     }
    if (xarSecAuthAction(0, 'release::', '::', ACCESS_EDIT)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'admin',
                                                  'viewdocs'),
                             'title' => xarML('View Release Docs'),
                             'label' => xarML('View Documentation'));

     }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>