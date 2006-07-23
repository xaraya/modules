<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * update an existing pubsub subscription
 * @param $args['pubsubid'] the ID of the item
 * @param $args['actionid'] the new action id for the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_updatesubscription($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($pubsubid) || !is_numeric($pubsubid)) {
        $invalid[] = 'pubsubid';
    }
    if (!isset($objectid) || !is_numeric($objectid)) {
        $invalid[] = 'objectid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'updatesubscription', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('EditPubSub', 1, 'item', 'All:$pubsubid')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubregtable = $xartable['pubsub_reg'];

    // Update the item
    $query = "UPDATE $pubsubregtable
              SET xar_actionid = ?
              WHERE xar_pubsubid = ?";
    $bindvars=array($actionid, $pubsubid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    return true;
}

/**
 * delete a pubsub user's subscriptions
 * this needs to be done when a user unregisters from the site.
 * @param $args['userid'] ID of the user whose subscriptions to delete
 * @returns array
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_delsubscriptions($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($userid) || !is_numeric($userid)) {
        $invalid[] = 'userid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'delsubscriptions', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubregtable = $xartable['pubsub_reg'];

    // Delete item
    $query = "DELETE FROM $pubsubregtable
              WHERE xar_userid = ?";
    $bindvars=array($userid);
    $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    return true;
}

?>
