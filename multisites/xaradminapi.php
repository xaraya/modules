<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Sebastien Bernard
// Purpose of file:  Administration of the multisites system.
// based on the templates developped by Jim McDee.
// ----------------------------------------------------------------------

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function multisites_adminapi_getmenulinks()
{

    if (xarSecurityCheck('AddMultisites',0)) {

        $menulinks[] = Array('url'   => xarModURL('multisites',
                                                   'admin',
                                                   'addsite'),
                              'title' => xarML('Add a new site into the system'),
                              'label' => xarML('Add Site'));
    }

    if (xarSecurityCheck('ReadMultisites',0)) {

        $menulinks[] = Array('url'   => xarModURL('multisites',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('View and Edit Sites'),
                              'label' => xarML('View Sites'));
    }

    if (xarSecurityCheck('AdminMultisites',0)) {
        $menulinks[] = Array('url'   => xarModURL('multisites',
                                                   'admin',
                                                   'adminconfig'),
                              'title' => xarML('Modify the admin settings for Multisites'),
                              'label' => xarML('Admin Config'));
    }
    if (xarSecurityCheck('AdminMultisites',0)) {
        $menulinks[] = Array('url'   => xarModURL('multisites',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Configure Master Site'),
                              'label' => xarML('Master Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

/**
 * create a new subsite
 * @return subsite ID on success, false on failure
 */
function multisites_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);

    // Optional arguments
    if (!isset($msstatus)) {
        $msstatus = '0';
    }
    if (!isset($msshare)) {
        $msshare = '';
    }

    // Make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($mssite))   ||
        (!isset($msprefix)) ||
        (!isset($msdb)))   {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'create', 'multisites');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if(!xarSecurityCheck('AddMultisites')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $multisitestable = $xartable['multisites'];

    // Check if the subsite already exists
    $query = "SELECT xar_msid FROM $multisitestable
            WHERE xar_mssite='".xarVarPrepForStore($mssite)."';";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if ($result->RecordCount() > 0) {
        $msg = xarML('The subsite already exists!');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get next subsite ID in table
    $nextId = $dbconn->GenId($multisitestable);

    // Add a subsite
    $query = "INSERT INTO $multisitestable (
              xar_msid,
              xar_mssite,
              xar_msprefix,
              xar_msdb,
              xar_msshare,
              xar_msstatus)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($mssite) . "',
              '" . xarVarPrepForStore($msprefix) . "',
              '" . xarVarPrepForStore($msdb) . "',
              '" . xarVarPrepForStore($msshare) . "',
              '" . xarVarPrepForStore($msstatus) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the subsite
    $msid = $dbconn->PO_Insert_ID($multisitestable, 'xar_msid');

    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $msid, 'msid');

    // Return the id of the newly created subsite
    return $msid;
}

/**
 * update a subsite
 */
function multisites_adminapi_update($args)
{
    // Get arguments
    extract($args);

    // Array of column set statements.
    $set = array();

    // String parameters.
    foreach(array('mssite', 'msprefix', 'msdb', 'msshare') as $parameter)
    {
        if (isset($$parameter))
        {
            $set[] = "xar_$parameter = '" . xarVarPrepForStore($$parameter) . "'";
        }
    }
  // Numeric parameters.
    foreach(array('msstatus'=>0) as $parameter => $default)
    {
        if (isset($$parameter))
        {
            if ($$parameter == "0" || $$parameter == "1")
            {
                $set[] = "xar_$parameter = " . xarVarPrepForStore($$parameter) . "";
            } else {
                $set[] = "xar_$parameter = " . xarVarPrepForStore($default) . "";
            }
        }
    }
    // Argument check
    if (!isset($msid) || empty($set)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'update', 'multisites');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Create the 'set' statement.
    $set = implode(', ', $set);

    // The user API function is called
    $subsite = xarModAPIFunc('multisites',
                             'user',
                             'get',
                          array('msid' => $msid));

    if ($subsite == false) {
        $msg = xarML('No Such Subsite Exists',
                    'multisites');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return;
    }

    if (!xarSecurityCheck('EditMultisites')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $multisitestable = $xartable['multisites'];

    // Update the subsite
    $query = 'UPDATE ' . $multisitestable . ' SET ' . $set
            . ' WHERE xar_msid = ' . xarVarPrepForStore($msid);
    $result =& $dbconn->Execute($query);
    if (!$result) {return;}

    // Let the calling process know that we have finished successfully
    return true;
}

/**
 * delete a subsite
 * @return true on success, false on failure
 */
function multisites_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($msid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'delete', 'multisites');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $subsite = xarModAPIFunc('multisites',
                             'user',
                             'get',
                             array('msid' => $msid));

    if ($subsite == false) {
        $msg = xarML('No Such Subsite Exists',
                    'multisites');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return;
    }

    // Security check
    if(!xarSecurityCheck('DeleteMultisites')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $multisitestable = $xartable['multisites'];

    // Delete the subsite
    $query = "DELETE FROM $multisitestable
            WHERE xar_msid = " . xarVarPrepForStore($msid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $msid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

?>
