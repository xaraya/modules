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
 * Displays a list of subscribers to a given category. Provides an option
 * to manually remove a subscriber.
 */
function pubsub_admin_viewsubscribers()
{
    if (!xarVarFetch('eventid', 'int::', $eventid)) return;
    if (!xarVarFetch('pubsubid','int::', $pubsubid, FALSE)) return;
    if (!xarVarFetch('unsub',   'int::', $unsub, FALSE)) return;

    if (empty($eventid)) {
        $msg = xarML('Invalid #(1) for function #(2)() in module #(3)',
                    'event id', 'viewsubscribers', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if ($unsub && $pubsubid) {
        if (!xarModAPIFunc('pubsub',
                           'user',
                           'deluser',
                            array('pubsubid' => $pubsubid))) {
            $msg = xarML('Bad return from #(1) in function #(2)() in module #(3)',
                         'deluser', 'viewsubscribers', 'Pubsub');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                           new SystemException($msg));
            return;
        }
    }

    $info = xarModAPIFunc('pubsub','admin','getevent',
                          array('eventid' => $eventid));
    if (empty($info)) {
        $msg = xarML('Invalid #(1) for function #(2)() in module #(3)',
                    'event id', 'viewsubscribers', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $data['items'] = array();
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Publish / Subscribe Administration'));
    $data['catname'] = xarVarPrepForDisplay($info['catname']);
    $data['cid'] = $info['cid'];
    $data['modname'] = $info['modname'];
    if (!empty($info['itemtype'])) {
        $data['modname'] .= ' ' . $info['itemtype'];
    }
    $data['itemtype'] = $info['itemtype'];
    $data['eventid'] = $eventid;
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = '';

    if (!xarSecurityCheck('AdminPubSub')) return;

    // The user API function is called
    $subscribers = xarModAPIFunc('pubsub'
                                ,'admin'
                                ,'getsubscribers'
                                ,array('eventid'=>$eventid));

    $data['items'] = $subscribers;

    $data['returnurl'] = xarModURL('pubsub'
                                  ,'admin'
                                  ,'viewsubscribers'
                                  ,array('eventid'=>$eventid));

    // TODO: add a pager (once it exists in BL)
    $data['pager'] = '';

    // return the template variables defined in this template

    return $data;

} // END ViewSubscribers

?>
