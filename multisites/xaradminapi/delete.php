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
 * delete a subsite
 * @return true on success, false on failure
 */
function multisites_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($msid)) {
      $msg = xarML('Invalid Parameter',
            'item ID', 'admin', 'delete', 'Multisites');
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
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

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
