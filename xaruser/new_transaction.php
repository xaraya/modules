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
 * Create a new item of the paymnts_transaction object
 *
 */

function payments_user_new_transaction()
{
    if (!xarSecurityCheck('AddPayments')) return;

    if (!xarVarFetch('confirm',      'bool',   $data['confirm'],      false,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('payment_type', 'str',    $data['payment_type'], '827',  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('type_changed', 'int',    $type_changed,         0,      XARVAR_NOT_REQUIRED)) return;
    
# --------------------------------------------------------
#
# Get the payment transactions object
#
    if (!xarVarFetch('name',         'str',    $name,            'payments_transactions', XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->properties['payment_type']->setValue($data['payment_type']);
    $data['tplmodule'] = 'payments';
    $data['authid'] = xarSecGenAuthKey('payments');

# --------------------------------------------------------
#
# Check if we are passing an api item
#
    if (!xarVarFetch('api',          'str',    $api,            '', XARVAR_NOT_REQUIRED)) return;
    
    $info = array();
    if (!empty($api)) {
        $function = rawurldecode($api);
        eval("\$info = $function;");
        
        foreach ($info as $key => $value) {
            if (isset($data['object']->properties[$key]))
                $data['object']->properties[$key]->value = $value;
        }
    }

# --------------------------------------------------------
#
# Check if we already have a transaction created
#
    if (!empty($info['beneficiary_object']) && !empty($info['beneficiary_itemid'])) {
        $payments = DataObjectMaster::getObjectList(array('name' => 'payments_transactions'));
        $q = $payments->dataquery;
        $q->eq('payment_object', $info['payment_object']);
        $q->eq('payment_itemid', $info['payment_itemid']);
        $q->eq('state', 3);
        $items = $payments->getItems();
        // Sanity check
        if (count($items) > 1) {
            return xarTpl::module('payments','user','errors',array('layout' => 'non_unique_source'));
        }
        if (count($items) == 1) {
            $item = current($items);
            xarController::redirect(xarModURL('payments', 'user', 'modify_transaction', array('itemid' => $item['id'], 'api' => $api)));
            return true;
        }
    }

# --------------------------------------------------------
#
# Get the debit account information
#
    if (!empty($info['sender_object']) && !empty($info['sender_itemid'])) {
        $data['debit_account'] = DataObjectMaster::getObjectList(array('name' => 'payments_debit_account'));
        $q = $data['debit_account']->dataquery;
        $q->eq('sender_object', $info['sender_object']);
        $q->eq('sender_itemid', $info['sender_itemid']);
        $items = $data['debit_account']->getItems();

        if(empty($items)) {
            return xarTpl::module('payments','user','errors',array('layout' => 'no_sender'));
        }
    
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

# --------------------------------------------------------
#
# Get the beneficiary information of the last payment if one exists
#
    $previous_exists = false;
    if (!empty($info['beneficiary_object']) && !empty($info['beneficiary_itemid'])) {
        $payments = DataObjectMaster::getObjectList(array('name' => 'payments_transactions'));
        $q = $payments->dataquery;
        $q->eq('beneficiary_object', $info['beneficiary_object']);
        $q->eq('beneficiary_itemid', $info['beneficiary_itemid']);
        $q->setorder('transaction_date', 'DESC');
        $items = $payments->getItems();
        if (!empty($items)) {
            $previous_exists = true;
            $item = current($items);
            if (!empty($item['payment_type']) && !$type_changed) {
                $data['object']->properties['payment_type']->value  = $item['payment_type'];
                $data['payment_type'] = $item['payment_type'];
            }
            if (!empty($item['reference_number'])) $data['object']->properties['reference_number']->value  = $item['reference_number'];
            if (!empty($item['post_account'])) $data['object']->properties['post_account']->value  = $item['post_account'];
            if (!empty($item['iban'])) $data['object']->properties['iban']->value  = $item['iban'];
            if (!empty($item['bic'])) $data['object']->properties['bic']->value  = $item['bic'];
        }
    }
    
    // If we have no previous payment, chekc if a paymnt type was passed
    if (($previous_exists == false) && isset($info['payment_type'])) {
        $data['object']->properties['payment_type']->value = $info['payment_type'];
    }

# --------------------------------------------------------
#
# The create button was clicked
#
    if ($data['confirm']) {
    
        // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
        if(!xarVarFetch('preview', 'str', $preview,  NULL, XARVAR_DONT_SET)) {return;}

        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;
        
        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('payments','user','new_transaction', $data);        
        } else {
            // Good data: create the item
            $itemid = $data['object']->createItem();
            
            // Jump to the next page
            xarController::redirect(xarModURL('payments','user','view_transactions'));
            return true;
        }
    }
    return $data;
}
?>