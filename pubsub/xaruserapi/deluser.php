<?php
/**
 * File: $Id$
 *
 * Pubsub user delUser
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Pubsub Module
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
*/

/**
 * delete a user's pubsub subscription
 * @param $args['pubsubid'] ID of the subscription to delete
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_deluser($args)
{

    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($pubsubid) || !is_numeric($pubsubid)) {
        $invalid[] = 'pubsubid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'unsubscribe', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('DeletePubSub', 1, 'item', 'All::$pubsubid')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubregtable = $xartable['pubsub_reg'];

    // Delete item
    $query = "DELETE FROM $pubsubregtable
              WHERE xar_pubsubid = '" . xarVarPrepForStore($pubsubid) . "'";
    $dbconn->Execute($query);

    return true;
} // END delUser

?>
