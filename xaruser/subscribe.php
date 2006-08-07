<?php
/**
 * Pubsub User Interface
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
 * subscribe user to a pubsub element
 * @param $args['modid'] module ID of event
 * @param $args['cid'] cid of event
 * @param $args['itemtype'] itemtype of event
 * @returns output
 * @return output with pubsub information
 */
function pubsub_user_subscribe()
{
    // do nothing if user not logged in otherwise subscribe 
    // the currently logged in user
    if (xarUserIsLoggedIn()) {
        $userid = xarUserGetVar('uid');
    } else {
        return;
    }
    
    if (!xarVarFetch('modid', 'id', $modid, $modid, XARVAR_NOT_REQUIRED)) return;    
    if (!xarVarFetch('cid', 'id', $cid, $cid, XARVAR_NOT_REQUIRED)) return;    
    if (!xarVarFetch('itemtype', 'id', $itemtype, $itemtype, XARVAR_NOT_REQUIRED)) return;    
    if (!xarVarFetch('returnurl', 'str', $returnurl, $returnurl, XARVAR_NOT_REQUIRED)) return;    

    $returnurl = rawurldecode($returnurl);

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
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
        join(', ',$invalid), 'subscribe', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // What is groupdescr???
    if (!isset($groupdescr))
        $groupdescr = 'Subscribe';

    // check if we already have an event for this, or create it if necessary
    $eventid = xarModAPIFunc('pubsub','admin','checkevent',
                             array('modid' => $modid,
                                   'itemtype' => $itemtype,
                                   'cid' => $cid,
                                   'groupdescr' => $groupdescr));
    if (empty($eventid)) return; // throw back

// TODO: fill in eventid *and* actionid (wherever that is supposed to come from)
// AM hardcoding actionid to 1 for now, will have to work out options for htmlmail etc. later
    if (!xarModAPIFunc('pubsub',
                       'user',
                       'adduser',
                        array('eventid' => $eventid,
                              'actionid' => 1,
                              'userid' => $userid))) {
        $msg = xarML('Bad return from #(1) in function #(2)() in module #(3)',
                     'adduser', 'subscribe', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
    }

    xarResponseRedirect($returnurl);
    return true;

} // END subscribe

?>
