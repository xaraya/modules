<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

/**
 * Delete a transaction
 */
 
function payments_user_delete_transaction()
{
    // Xaraya security
    if (!xarSecurity::check('ManagePayments')) return;
    xarTpl::setPageTitle('Delete Payment');

    if(!xarVar::fetch('confirmed',  'bool', $confirmed,  false, xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('itemid',     'str',  $itemid,     NULL,  xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('idlist',     'str',  $idlist,     NULL,  xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('returnurl',  'str',  $returnurl,  NULL,  xarVar::DONT_SET)) {return;}

    if (!empty($itemid)) $idlist = $itemid;
    $ids = explode(',',trim($idlist,','));
    
    if (empty($idlist)) {
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarController::URL('payments', 'user','view_transactions'));
        }
    }

    $data['message'] = '';
    $data['itemid']  = $itemid;

/*------------- Ask for Confirmation.  If yes, action ----------------------------*/

    sys::import('modules.dynamicdata.class.objects.master');
    $course_item = DataObjectMaster::getObject(array('name' => 'payments_transactions'));
    if (!$confirmed) {
        $data['idlist'] = $idlist;
        if (is_array($ids)) {
            $data['lang_title'] = xarML("Delete Payments");
        } else {
            $ids = array($ids);
            $data['lang_title'] = xarML("Delete Payment");
        }
        $data['authid'] = xarSec::genAuthKey();
        if (count($ids) == 1) {
            $course_item->getItem(array('itemid' => current($ids)));
            $data['object'] = $course_item;
        } else {
            $items = array();
            foreach ($ids as $i) {
                $course_item->getItem(array('itemid' => $i));
                $item = $course_item->getFieldValues();
                $item['name'] = $item['name'];
                $items[] = $item;
            }
            $data['items'] = $items;
        }
        $data['yes_action'] = xarController::URL('payments','user','delete_payment',array('idlist' => $idlist));
        return $data;        
    } else {
        if (!xarSec::confirmAuthKey()) return;
        $script = implode('_', xarController::$request->getInfo());
        foreach ($ids as $id) {
            $itemid = $course_item->deleteItem(array('itemid' => $id, 'script' => $script));
            $data['message'] = "Course item deleted [ID $id]";
        }

        // Jump to the next page
        xarController::redirect(xarController::URL('payments','user','view_transactions'));
        return true;
    }
}
?>
