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
 * Modify an item of the payments_debit_account object
 *
 */
    
function payments_user_modify_debit_account()
{
    if (!xarSecurity::check('EditPayments')) return;

    if (!xarVar::fetch('name',       'str',      $name,            'payments_debit_account', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('itemid' ,    'int',      $data['itemid'] , 0 ,          xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('confirm',    'checkbox', $data['confirm'], false,       xarVar::NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'payments';
    $data['authid'] = xarSec::genAuthKey('payments');

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if(!xarSec::confirmAuthKey()) return;

        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTpl::module('payments','user','modify_debit_account', $data);        
        } else {
            // Good data: create the item
            $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
            
            // Jump to the next page
            xarController::redirect(xarController::URL('payments','user','view_debit_accounts'));
            return true;
        }
    }
    return $data;
}
?>