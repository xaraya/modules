<?php
/**
 * File: $Id$
 *
 * Sniffer Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Sniffer Module
 * @author Frank Besler
 *
 * Using phpSniffer by Roger Raymond
 * Purpose of file: find out the browser and OS of the visitor
*/


/**
 * Delete a sniff
 *
 * @public
 * @author Richard Cave 
 * @param $args['id'] ID of the sniff
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, MISSING_DATA
 */
function sniffer_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)',
                     'id', 'adminapi', 'delete', 'sniffer');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // The user API function is called
    $sniff = xarModAPIFunc('sniffer',
                           'user',
                           'getsniff',
                           array('id' => $id));

    if ($sniff == false) {
        $msg = xarML('No Such Sniff Present', 'sniffer');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; 
    }

    // Security Check
	if(!xarSecurityCheck('DeleteSniffer')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $snifferTable = $xartable['sniffer'];

    // Delete the tag
    $query = "DELETE FROM $snifferTable
              WHERE xar_ua_id = " . xarVarPrepForStore($id);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a sniff 
    xarModCallHooks('item', 'delete', $id, '');

    // Let the calling process know that we have finished successfully
    return true;
}

?>
