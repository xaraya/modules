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
 * Delete a subscription
 */
 
function pubsub_admin_delete_subscription()
{
    // Xaraya security
    if (!xarSecurity::check('ManagePubSub')) {
        return;
    }
    xarTpl::setPageTitle('Delete subscription');

    if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'str', $data['itemid'], null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('idlist', 'str', $idlist, null, xarVar::DONT_SET)) {
        return;
    }

    //print_r($data['confirm']);
    if (!empty($data['itemid'])) {
        $idlist = $data['itemid'];
    }
    $ids = explode(',', trim($idlist, ','));
    
    $data['message'] = '';
    $data['itemid']  = $data['itemid'];
    $data['tplmodule'] = 'pubsub';

    /*------------- Ask for Confirmation.  If yes, action ----------------------------*/

    sys::import('modules.dynamicdata.class.objects.master');
    $subscription = DataObjectMaster::getObject(array('name' => 'pubsub_subscriptions'));
    if (!$data['confirm']) {
        $data['idlist'] = $idlist;
        if (is_array($ids)) {
            $data['lang_title'] = xarML("Delete Subscriptions");
        } else {
            $ids = array($ids);
            $data['lang_title'] = xarML("Delete Subscription");
        }
        $data['authid'] = xarSec::genAuthKey();
        if (count($ids) == 1) {
            $subscription->getItem(array('itemid' => current($ids)));
            $data['object'] = $subscription;
        } else {
            $items = array();
            foreach ($ids as $i) {
                $subscription->getItem(array('itemid' => $i));
                $item = $subscription->getFieldValues();
                $item['name'] = $item['name'];
                $items[] = $item;
            }
            $data['items'] = $items;
        }
        $data['yes_action'] = xarController::URL('pubsub', 'admin', 'delete_subscription', array('idlist' => $idlist));

        return $data;
    } else {
        if (!xarSec::confirmAuthKey()) {
            return;
        }
        $script = implode('_', xarController::$request->getInfo());
        foreach ($ids as $id) {
            $itemid = $subscription->getItem(array('itemid' => $id));
            $itemid = $subscription->updateItem(array('itemid' => $id, 'state' => 0));
        }

        // Jump to the next page
        xarController::redirect(xarController::URL('pubsub', 'admin', 'view_subscriptions'));
        return true;
    }
}
