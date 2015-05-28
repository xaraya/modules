<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * update an existing pubsub subscription
 * @param int $args['pubsubid'] the ID of the item
 * @param int $args['actionid'] the new action id for the item
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
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
        throw new Exception($msg);
    }

    // Security check
    if (!xarSecurityCheck('EditPubSub', 1, 'item', 'All:$pubsubid')) return;

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubregtable = $xartable['pubsub_reg'];

    // Update the item
    $query = "UPDATE $pubsubregtable
              SET actionid = ?
              WHERE pubsubid = ?";
    $bindvars=array($actionid, $pubsubid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    return true;
}

/**
 * delete a pubsub user's subscriptions
 * this needs to be done when a user unregisters from the site.
 * @param int $args['userid'] ID of the user whose subscriptions to delete
 * @return bool true on success of deletion
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_delsubscriptions($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($userid) || !is_numeric($userid)) $invalid[] = 'userid';

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'delsubscriptions', 'Pubsub');
        throw new Exception($msg);
    }

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubregtable = $xartable['pubsub_reg'];

    // Delete item
    $query = "DELETE FROM $pubsubregtable
              WHERE userid = ?";
    $bindvars=array($userid);
    $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    return true;
}

?>
