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
 * unsubscribe user from a pubsub element
 * @param $args['modid'] module ID of event
 * @param $args['itemtype'] itemtype of event
 * @param $args['cid'] cid of event
 * @param $args['extra'] some extra group criteria
 * @param $args['userid'] the subscriber
 * @returns output
 * @return output with pubsub information
 */
function pubsub_userapi_unsubscribe($args)
{
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($modid))       { $invalid[] = 'modid'; }
    if (!isset($cid))         { $invalid[] = 'cid'; }
//    if (!isset($itemtype))  { $invalid[] = 'itemtype'; }
    if (!isset($userid))      { $invalid[] = 'userid'; }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(3)() in module #(4)',
        join(', ',$invalid), 'unsubscribe', 'Pubsub');
        throw new Exception($msg);
    }

    // Database information
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubsubscriptionstable = $xartable['pubsub_subscriptions'];

    // fetch pubsubid to unsubscribe from
    $query = "SELECT pubsubid
                FROM $pubsubeventstable, $pubsubsubscriptionstable
               WHERE $pubsubeventstable.modid = ?
                 AND $pubsubsubscriptionstable.eventid = $pubsubeventstable.eventid
                 AND $pubsubsubscriptionstable.userid = ?
                 AND $pubsubeventstable.cid = ?";

    $bindvars = array((int)$modid, (int)$userid, (int)$cid);
    if (isset($extra)) {
        $query .= " AND $pubsubeventstable.extra = ?";
        array_push($bindvars, $extra);
    }
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result || $result->EOF) return;

    list($pubsubid) = $result->fields;

    if (!xarMod::apiFunc('pubsub',
                       'user',
                       'deluser',
                        array('pubsubid' => $pubsubid))) {
        $msg = xarML('Bad return from #(1) in function #(2)() in module #(3)',
                     'deluser', 'unsubscribe', 'Pubsub');
        throw new Exception($msg);
    }

    return true;

} // END unsubscribe

?>
