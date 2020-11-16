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
 * @param $args['cid'] cid of event
 * @param $args['itemtype'] itemtype of event
 * @returns output
 * @return output with pubsub information
 */
function pubsub_user_unsubscribe($args)
{
    // do nothing if user not logged in otherwise unsubscribe
    // the currently logged in user
    if (!xarUser::isLoggedIn()) {
        return;
    }

    if (!xarVar::fetch('modid', 'int:1:', $modid, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('cid', 'int:1:', $cid, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemtype', 'int:1:', $itemtype, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('returnurl', 'str:1:', $returnurl, '', xarVar::NOT_REQUIRED)) {
        return;
    }


    $returnurl = rawurldecode($returnurl);

    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($returnurl) || !is_string($returnurl)) {
        $invalid[] = 'returnurl';
    }
    if (!isset($modid) || !is_numeric($modid)) {
        $invalid[] = 'modid';
    }
    if (!isset($cid) || !is_numeric($cid)) {
        $invalid[] = 'cid';
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $invalid[] = 'itemtype';
    }

    if (count($invalid) > 0) {
        $msg = xarML(
            'Invalid #(1) in function #(3)() in module #(4)',
            join(', ', $invalid),
            'unsubscribe',
            'Pubsub'
        );
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
                 AND $pubsubeventstable.itemtype = ?
                 AND $pubsubsubscriptionstable.eventid = $pubsubeventstable.eventid
                 AND $pubsubsubscriptionstable.userid = ?
                 AND $pubsubeventstable.cid = ?";

    $bindvars = array((int)$modid, $itemtype, (int)$userid, $cid);
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result || $result->EOF) {
        return;
    }

    list($pubsubid) = $result->fields;

    if (!xarMod::apiFunc(
        'pubsub',
        'user',
        'deluser',
        array('pubsubid' => $pubsubid)
    )) {
        $msg = xarML(
            'Bad return from #(1) in function #(2)() in module #(3)',
            'deluser',
            'unsubscribe',
            'Pubsub'
        );
        throw new Exception($msg);
    }

    xarController::redirect($returnurl);
    return true;
}
