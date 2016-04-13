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
 * Modify an item of the payments_transaction object
 *
 */
    
function payments_user_modify_transaction()
{
    if (!xarSecurityCheck('EditPayments')) return;

    if (!xarVarFetch('name',       'str',      $name,            'payments_transactions', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,    'int',      $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('payment_type', 'str',    $data['payment_type'], '827',  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'checkbox', $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

# --------------------------------------------------------
#
# Get the payment transactions object
#
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));
    $data['tplmodule'] = 'payments';
    $data['authid'] = xarSecGenAuthKey('payments');

# --------------------------------------------------------
#
# Check if we are passing an api item
#
    if (!xarVarFetch('api',          'str',    $api,            '', XARVAR_NOT_REQUIRED)) return;
    
    if (!empty($api)) {
        $function = rawurldecode($api);
        eval("\$info = $function;");
        
        foreach ($info as $key => $value) {
            if (isset($data['object']->properties[$key]))
                $data['object']->properties[$key]->value = $value;
        }

# --------------------------------------------------------
#
# Get the debit account information
#
        $data['debit_account'] = DataObjectMaster::getObjectList(array('name' => 'payments_debit_account'));
        $q = $data['debit_account']->dataquery;
        $q->eq('sender_object', $info['sender_object']);
        $q->eq('sender_itemid', $info['sender_itemid']);
        $items = $data['debit_account']->getItems();

        if(!empty($items)) {
            $item = current($items);
            // The debtor name
            $data['object']->properties['sender_account']->value = $item['account_holder'];
            // The debtor address
            $lines = xarMod::apiFunc('payments', 'admin', 'unpack_address', array('address' => $item['address']));
            if (!empty($lines[3])) $lines[2] .= " " . $lines[3];
            if (isset($lines[1])) $data['object']->properties['sender_line_2']->value  = $lines[1];
            if (isset($lines[2])) $data['object']->properties['sender_line_3']->value  = $lines[2];
            if (isset($lines[4])) $data['object']->properties['sender_line_4']->value  = $lines[4];
        }
    }
    
# --------------------------------------------------------
#
# The create button was clicked
#
    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;

        // Disable fields we are not using and don't want to check
        if ($data['object']->properties['payment_type']->value == 1) {
            $data['object']->properties['iban']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
        }
        
        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('payments','user','modify_transaction', $data);        
        } else {
            // Good data: create the item
            $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
            
            // Jump to the next page
            xarController::redirect(xarModURL('payments','user','view_transactions'));
            return true;
        }
    }
    return $data;
}
?>