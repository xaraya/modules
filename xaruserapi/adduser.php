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
 * Add a user's subscription to an event
 * @param $args['eventid'] Event to subscribe to
 * @param $args['actionid'] Requested action for this subscription
 * @param $args['userid'] ID of User to subscribe OR
 * @param $args['email'] EMail address of anonymous user to subscribe
 * @return bool pubsub ID on success, false if not
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_adduser($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($eventid) || !is_numeric($eventid)) {
        $invalid[] = 'eventid';
    }
    if (!isset($actionid) || !is_numeric($actionid)) {
        $invalid[] = 'actionid';
    }
    if ( (!isset($userid) || !is_numeric($userid)) && (!isset($email) || empty($email)) )
    {
        $invalid[] = 'userid or email';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'subscribe', 'Pubsub');
        throw new Exception($msg);
    }

    // Check if we're subscribing an anonymous user, or a known userid
    if ( (!isset($userid) || !is_numeric($userid)) && (isset($email) || !empty($email)) )
    {
        $userid = -1;

        //TODO: EMail validation, is this a valid email address
    }

    // Security check
    if (!xarSecurityCheck('ReadPubSub', 1, 'item', 'All::$eventid')) return;

    // Database information
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubsubscriptionstable = $xartable['pubsub_subscriptions'];

    // check not already subscribed
    // TODO: Just noting that this doesn't actually do anything other then a useless query
    $query = "SELECT pubsubid FROM $pubsubsubscriptionstable";
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // Get next ID in table
    $nextId = $dbconn->GenID($pubsubsubscriptionstable);

    // Add item
    $query = "INSERT INTO $pubsubsubscriptionstable (
              pubsubid,
              eventid,
              userid,
              actionid,
              subdate,
              email)
            VALUES (?,?,?,?," . time() . ",?)";
    $bindvars = array($nextId, $eventid, $userid, $actionid, $email);
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // return pubsub ID
    $nextId = $dbconn->PO_Insert_ID($pubsubsubscriptionstable, 'pubsubid');

    return $nextId;
}

?>