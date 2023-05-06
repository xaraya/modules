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
 * subscribe user to a pubsub element
 * @param $args['modid'] module ID of event
 * @param $args['cid'] cid of event
 * @param $args['itemtype'] itemtype of event
 * @return bool true output with pubsub information
 */
function pubsub_user_subscribe()
{
    // do nothing if user not logged in otherwise subscribe
    // the currently logged in user
    if (!xarUser::isLoggedIn()) {
        return;
    }

    if (!xarVar::fetch('modid',      'isset', $modid,     false, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('cid',        'isset', $cid,       false, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('itemtype',   'isset', $itemtype,  false, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('returnurl',  'isset', $returnurl, false, xarVar::NOT_REQUIRED)) return;

    $returnurl = rawurldecode($returnurl);

    // Argument check
    $invalid = array();
    if (!isset($returnurl) || !is_string($returnurl)) $invalid[] = 'returnurl';
    if (!isset($modid) || !is_numeric($modid)) $invalid[] = 'modid';
    if (!isset($cid) || !is_numeric($cid)) $invalid[] = 'cid';
    if (!isset($itemtype) || !is_numeric($itemtype)) $invalid[] = 'itemtype';

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
        join(', ',$invalid), 'subscribe', 'Pubsub');
        throw new Exception($msg);
    }

    // What is groupdescr???
    if (!isset($groupdescr))
        $groupdescr = 'Subscribe';

    // check if we already have an event for this, or create it if necessary
    $eventid = xarMod::apiFunc('pubsub','admin','checkevent',
                             array('modid' => $modid,
                                   'itemtype' => $itemtype,
                                   'cid' => $cid,
                                   'groupdescr' => $groupdescr));
    if (empty($eventid)) return; // throw back

// TODO: fill in eventid *and* actionid (wherever that is supposed to come from)
// Am hardcoding actionid to 1 for now, will have to work out options for htmlmail etc. later
    if (!xarMod::apiFunc('pubsub',
                       'user',
                       'adduser',
                        array('eventid' => $eventid,
                              'actionid' => 1,
                              'userid' => $userid))) {
        $msg = xarML('Bad return from #(1) in function #(2)() in module #(3)',
                     'adduser', 'subscribe', 'Pubsub');
        throw new Exception($msg);
    }

    xarController::redirect($returnurl);
    return true;
}

?>