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

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    $query = "SELECT xar_rid,
                     xar_uid,
                     xar_name,
                     xar_desc,
                     xar_type,
                     xar_certified,
                     xar_approved
            FROM $releasetable
            ORDER BY xar_rid";
    if (!empty($certified)) {
        $query .= " WHERE xar_certified = '" . xarVarPrepForStore($certified). "'";
    }

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rid, $uid, $name, $desc, $type, $certified, $approved) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rid'        => $rid,
                                   'uid'        => $uid,
                                   'name'       => $name,
                                   'desc'       => $desc,
                                   'type'       => $type,
                                   'certified'  => $certified,
                                   'approved'   => $approved);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

function release_userapi_getthemeids($args)
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

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    $query = "SELECT xar_rid,
                     xar_uid,
                     xar_name,
                     xar_desc,
                     xar_type,
                     xar_certified,
                     xar_approved
            FROM $releasetable
            WHERE xar_type = 'theme'
            ORDER BY xar_rid";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rid, $uid, $name, $desc, $type, $certified, $approved) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rid'        => $rid,
                                   'uid'        => $uid,
                                   'name'       => $name,
                                   'desc'       => $desc,
                                   'type'       => $type,
                                   'certified'  => $certified,
                                   'approved'   => $approved);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

function release_userapi_getmoduleids($args)
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

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    $query = "SELECT xar_rid,
                     xar_uid,
                     xar_name,
                     xar_desc,
                     xar_type,
                     xar_certified,
                     xar_approved
            FROM $releasetable
            WHERE xar_type = 'module'
            ORDER BY xar_rid";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rid, $uid, $name, $desc, $type, $certified, $approved) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rid'        => $rid,
                                   'uid'        => $uid,
                                   'name'       => $name,
                                   'desc'       => $desc,
                                   'type'       => $type,
                                   'certified'  => $certified,
                                   'approved'   => $approved);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

function release_userapi_getid($args)
{
    extract($args);

    if (!isset($rid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Get link
    $query = "SELECT xar_rid,
                     xar_uid,
                     xar_name,
                     xar_desc,
                     xar_type,
                     xar_certified,
                     xar_approved
            FROM $releasetable
            WHERE xar_rid = " . xarVarPrepForStore($rid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($rid, $uid, $name, $desc, $type, $certified, $approved) = $result->fields;
    $result->Close();

    if (!xarSecurityCheck('OverviewRelease', 0)) {
        return false;
    }

    $releaseinfo = array('rid'        => $rid,
                         'uid'        => $uid,
                         'name'       => $name,
                         'desc'       => $desc,
                         'type'       => $type,
                         'certified'  => $certified,
                         'approved'   => $approved);

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
              xar_uid,
              xar_name,
              xar_desc,
              xar_type,
              xar_certified,
              xar_approved
              )
            VALUES (
              '" . xarVarPrepForStore($rid) . "',
              '" . xarVarPrepForStore($uid) . "',
              '" . xarVarPrepForStore($name) . "',
              '" . xarVarPrepForStore($desc) . "',
              '" . xarVarPrepForStore($type) . "',
              '" . xarVarPrepForStore($certified) . "',
              '" . xarVarPrepForStore($approved) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'create', $rid, 'rid');

    // Return the id of the newly created user to the calling process
    return $rid;

}

function release_userapi_updateid($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($rid)) ||
        (!isset($uid)) ||
        (!isset($name)) ||
        (!isset($type))) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'update', 'Autolinks');
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
        $msg = xarML('No Such Release ID Present',
                    'release');
        xarExceptionSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    if (empty($approved)){
        $approved = '1';
    }

    $releasetable = $xartable['release_id'];

    // Update the link
    $query = "UPDATE $releasetable
            SET xar_uid = '" . xarVarPrepForStore($uid) . "',
                xar_name = '" . xarVarPrepForStore($name) . "',
                xar_type = '" . xarVarPrepForStore($type) . "',
                xar_desc = '" . xarVarPrepForStore($desc) . "',
                xar_certified = '" . xarVarPrepForStore($certified) . "',
                xar_approved = '" . xarVarPrepForStore($approved) . "'
            WHERE xar_rid = " . xarVarPrepForStore($rid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'update', $rid, 'rid');

    // Return the id of the newly created user to the calling process
    return $rid;
}

function release_userapi_getallnotes($args)
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

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasenotes = $xartable['release_notes'];

    $query = "SELECT xar_rnid,
                     xar_rid,
                     xar_version,
                     xar_price,
                     xar_priceterms,
                     xar_demo,
                     xar_demolink,
                     xar_dllink,
                     xar_supported,
                     xar_supportlink,
                     xar_changelog,
                     xar_notes,
                     xar_time,
                     xar_enotes,
                     xar_certified,
                     xar_approved
            FROM $releasenotes";
    if (!empty($approved)) {
        $query .= " WHERE xar_approved = '" . xarVarPrepForStore($approved). "'
                    ORDER by xar_time DESC";
    } elseif (!empty($certified)) {
        $query .= " WHERE xar_certified = '" . xarVarPrepForStore($certified) . "'
                    AND xar_approved = 2
                    ORDER by xar_time DESC";
    } elseif (!empty($supported)) {
        $query .= " WHERE xar_supported = '" . xarVarPrepForStore($supported) . "'
                    AND xar_approved = 2
                    ORDER by xar_time DESC";
    } elseif (!empty($price)) {
        $query .= " WHERE xar_price = '" . xarVarPrepForStore($price) . "'
                    AND xar_approved = 2
                    ORDER by xar_time DESC";
    } elseif (!empty($rid)) {
        $query .= " WHERE xar_rid = '" . xarVarPrepForStore($rid) . "'
                    AND xar_approved = 2
                    ORDER by xar_time DESC";
    }

            //ORDER BY xar_rnid";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rnid, $rid, $version, $price, $priceterms, $demo, $demolink, $dllink, $supported, $supportlink, $changelog, $notes, $time,  $enotes, $certified, $approved) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rnid'       => $rnid,
                                   'rid'        => $rid,
                                   'version'    => $version,
                                   'price'      => $price,
                                   'priceterms' => $priceterms,
                                   'demo'       => $demo,
                                   'demolink'   => $demolink,
                                   'dllink'     => $dllink,
                                   'supported'  => $supported,
                                   'supportlink'=> $supportlink,
                                   'changelog'  => $changelog,
                                   'notes'      => $notes,
                                   'time'       => $time,
                                   'enotes'     => $enotes,
                                   'certified'  => $certified,
                                   'approved'   => $approved);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

function release_userapi_getnote($args)
{
    extract($args);

    if (!isset($rnid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_notes'];

    // Get link
    $query = "SELECT xar_rnid,
                     xar_rid,
                     xar_version,
                     xar_price,
                     xar_priceterms,
                     xar_demo,
                     xar_demolink,
                     xar_dllink,
                     xar_supported,
                     xar_supportlink,
                     xar_changelog,
                     xar_notes,
                     xar_time,
                     xar_enotes,
                     xar_certified,
                     xar_approved
            FROM $releasetable
            WHERE xar_rnid = " . xarVarPrepForStore($rnid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($rnid, $rid, $version, $price, $priceterms, $demo, $demolink, $dllink, $supported, $supportlink, $changelog, $notes, $time, $enotes, $certified, $approved) = $result->fields;
    $result->Close();

    if (!xarSecurityCheck('OverviewRelease', 0)) {
        return false;
    }

    $releaseinfo = array('rnid'       => $rnid,
                         'rid'        => $rid,
                         'version'    => $version,
                         'price'      => $price,
                         'priceterms' => $priceterms,
                         'demo'       => $demo,
                         'demolink'   => $demolink,
                         'dllink'     => $dllink,
                         'supported'  => $supported,
                         'supportlink'=> $supportlink,
                         'changelog'  => $changelog,
                         'notes'      => $notes,
                         'time'       => $time,
                         'enotes'     => $enotes,
                         'certified'  => $certified,
                         'approved'   => $approved);

    return $releaseinfo;
}

function release_userapi_createnote($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if ((!isset($rid)) ||
        (!isset($version))) {

        $msg = xarML('Wrong arguments to release_userapi_create.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_notes'];

    if (empty($approved)){
        $approved = 1;
    }

    if (empty($certified)){
        $certified = 1;
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($releasetable);
    $time = date('Y-m-d G:i:s');
    $query = "INSERT INTO $releasetable (
                     xar_rnid,
                     xar_rid,
                     xar_version,
                     xar_price,
                     xar_priceterms,
                     xar_demo,
                     xar_demolink,
                     xar_dllink,
                     xar_supported,
                     xar_supportlink,
                     xar_changelog,
                     xar_notes,
                     xar_time,
                     xar_certified,
                     xar_approved
              )
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($rid) . "',
              '" . xarVarPrepForStore($version) . "',
              '" . xarVarPrepForStore($price) . "',
              '" . xarVarPrepForStore($priceterms) . "',
              '" . xarVarPrepForStore($demo) . "',
              '" . xarVarPrepForStore($demolink) . "',
              '" . xarVarPrepForStore($dllink) . "',
              '" . xarVarPrepForStore($supported) . "',
              '" . xarVarPrepForStore($supportlink) . "',
              '" . xarVarPrepForStore($changelog) . "',
              '" . xarVarPrepForStore($notes) . "',
              '$time',
              '" . xarVarPrepForStore($certified) . "',
              '" . xarVarPrepForStore($approved) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $rnid = $dbconn->PO_Insert_ID($releasetable, 'xar_rnid');

    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'create', $rnid, 'rnid');

    // Return the id of the newly created user to the calling process
    return $rnid;

}

function release_userapi_getdocs($args)
{
    extract($args);

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $releasedocs = array();

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasedocstable = $xartable['release_docs'];

    $query = "SELECT xar_rdid,
                     xar_rid,
                     xar_title,
                     xar_docs,
                     xar_type,
                     xar_time,
                     xar_approved
            FROM $releasedocstable
                         
                     /*";
    if (!empty($apporved)) {
        $query .= " WHERE xar_rid = '" . xarVarPrepForStore($rid) . "'
                    AND xar_approved = '" . xarVarPrepForStore($approved) . "'
                    AND xar_type = '" . xarVarPrepForStore($type) . "'";
    } elseif(empty($type)) {
        $query .= " WHERE xar_approved = '" . xarVarPrepForStore($approved) . "'";
    } else {
        $query .= "*/ WHERE xar_rid = '" . xarVarPrepForStore($rid) . "'
                    AND xar_type = '" . xarVarPrepForStore($type) . "'";
    }

    $query .= "ORDER BY xar_rdid";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rdid, $rid, $title, $docs, $type, $time, $approved) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releasedocs[] = array('rdid'       => $rdid,
                                   'rid'        => $rid,
                                   'title'      => $title,
                                   'docs'       => $docs,
                                   'type'       => $type,
                                   'time'       => $time,
                                   'approved'   => $approved);
        }
    }

    $result->Close();

    // Return the users
    return $releasedocs;

}

function release_userapi_getdoc($args)
{
    extract($args);

    if (!isset($rdid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_docs'];

    // Get link
    $query = "SELECT xar_rdid,
                     xar_rid,
                     xar_title,
                     xar_docs,
                     xar_type,
                     xar_time,
                     xar_approved
            FROM $releasetable
            WHERE xar_rdid = " . xarVarPrepForStore($rdid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($rdid, $rid, $title, $docs, $type, $time, $approved) = $result->fields;
    $result->Close();

    if (!xarSecurityCheck('OverviewRelease', 0)) {
        return false;
    }

    $releaseinfo = array('rdid'       => $rdid,
                         'rid'        => $rid,
                         'title'      => $title,
                         'docs'       => $docs,
                         'type'       => $type,
                         'time'       => $time,
                         'approved'   => $approved);

    return $releaseinfo;
}

function release_userapi_createdoc($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if ((!isset($rid)) ||
        (!isset($title)) ||
        (!isset($doc)) ||
        (!isset($type)) ||
        (!isset($approved))) {

        $msg = xarML('Wrong arguments to release_userapi_createdoc.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_docs'];

    if (empty($approved)){
        $approved = 1;
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($releasetable);
    $time = date('Y-m-d G:i:s');
    $query = "INSERT INTO $releasetable (
              xar_rdid,
              xar_rid,
              xar_title,
              xar_docs,
              xar_type,
              xar_time,
              xar_approved
              )
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($rid) . "',
              '" . xarVarPrepForStore($title) . "',
              '" . xarVarPrepForStore($doc) . "',
              '" . xarVarPrepForStore($type) . "',
              '$time',
              '" . xarVarPrepForStore($approved) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $rdid = $dbconn->PO_Insert_ID($releasetable, 'xar_rdid');

    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'create', $rdid, 'rdid');

    // Return the id of the newly created user to the calling process
    return $rdid;

}

/**
 * count the number of docs per item
 * @returns integer
 * @returns number of docs for rid
 */
function release_userapi_countdocs($args)
{
    extract ($args);

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_docs'];

    $query = "SELECT COUNT(1)
            FROM $releasetable
            WHERE xar_rid = " . xarVarPrepForStore($rid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

function release_userapi_getmenulinks()
{
    if (xarSecurityCheck('OverviewRelease', 0)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'viewids'),
                             'title' => xarML('View all theme and module IDs'),
                             'label' => xarML('View Registration'));
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'viewnotes'),
                             'title' => xarML('View all theme and module releases'),
                             'label' => xarML('Recent Releases'));
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'addid'),
                             'title' => xarML('Add a module or theme ID so it will not be duplicated'),
                             'label' => xarML('Add Registration'));
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'addnotes'),
                             'title' => xarML('Add a module or theme release note'),
                             'label' => xarML('Add Release Notes'));
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'adddocs'),
                             'title' => xarML('Add module or theme documentation'),
                             'label' => xarML('Add Documentation'));

    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>