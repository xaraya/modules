<?php
// File: $Id$
/*
 * Xaraya Multisites
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Multisites Module
 * @author
 */
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
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

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
              xar_msstatus,
              xar_sitefolder)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($mssite) . "',
              '" . xarVarPrepForStore($msprefix) . "',
              '" . xarVarPrepForStore($msdb) . "',
              '" . xarVarPrepForStore($msshare) . "',
              '" . xarVarPrepForStore($msstatus) . "',
              '" . xarVarPrepForStore($sitefolder) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the subsite
    $msid = $dbconn->PO_Insert_ID($multisitestable, 'xar_msid');

    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $msid, 'msid');

    // Return the id of the newly created subsite
    return $msid;
}
?>
