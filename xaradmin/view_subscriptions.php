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
 * Displays a list of subscriptions to a given category. Provides an option
 * to manually remove a subscription.
 */
function pubsub_admin_view_subscriptions()
{
    if (!xarSecurityCheck('ManagePubSub')) return;
    xarTplSetPageTitle('View Subscribers');

    if (!xarVarFetch('eventid', 'int::', $eventid,  0,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pubsubid','int::', $pubsubid, FALSE, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('unsub',   'int::', $unsub,    FALSE, XARVAR_NOT_REQUIRED)) return;
/*
    if (empty($eventid)) {
        $msg = xarML('Invalid #(1) for function #(2)() in module #(3)',
                    'event id', 'view_subscriptions', 'Pubsub');
        throw new Exception($msg);
    }
*/
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObjectList(array('name' => 'pubsub_subscriptions'));
    $q = $data['object']->dataquery;
    
    // Only active domains
    $q->eq('subscriptions.state', 3);
    
    // If an event ID was passed, then filter on it
    if (!empty($eventid)) $q->eq('subscriptions.event', $eventid);
    
    return $data;

    $data['items'] = array();
    $data['authid'] = xarSecGenAuthKey();

    if ($unsub && $pubsubid) {
        if (!xarMod::apiFunc('pubsub', 'user', 'deluser', array('pubsubid' => $pubsubid))) {
            $msg = xarML('Bad return from #(1) in function #(2)() in module #(3)',
                         'deluser', 'view_subscriptions', 'Pubsub');
            throw new Exception($msg);
        }
    }

    $info = xarMod::apiFunc('pubsub','user','getevent', array('eventid' => $eventid));
    if (empty($info)) {
        $msg = xarML('Invalid #(1) for function #(2)() in module #(3)',
                    'event id', 'view_subscriptions', 'Pubsub');
        throw new Exception($msg);
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
    $subscriptions = xarMod::apiFunc('pubsub', 'user', 'getsubscribers', array('eventid'=>$eventid));

    $data['items'] = $subscriptions;

    $data['returnurl'] = xarModURL('pubsub', 'user', 'view_subscriptions', array('eventid'=>$eventid));

    // TODO: add a pager (once it exists in BL)
    $data['pager'] = '';

    // return the template variables defined in this template

    return $data;

}

?>
