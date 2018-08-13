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
 * Create a new item of the payments_transaction object
 *
 */

function payments_user_new_transaction()
{
    if (!xarSecurityCheck('AddPayments')) return;

    if (!xarVarFetch('confirm',       'bool',   $data['confirm'],       false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('type_changed',  'int',    $type_changed,          0,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('debit_account', 'int',    $data['debit_account'], 0,     XARVAR_NOT_REQUIRED)) return;
    
# --------------------------------------------------------
#
# Get the payment transactions object
#
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => 'payments_transactions'));
    $data['tplmodule'] = 'payments';

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
        // Adjust the execution date if the date passed is in the past
        if ($data['object']->properties['transaction_date']->value < time()) {
            $data['object']->properties['transaction_date']->value = time();
        }
    }

# --------------------------------------------------------
#
# Check if we already have a transaction created for this payment
#
    if (!empty($info['payment_object']) && !empty($info['payment_itemid'])) {
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
        
        // If we have a single item, it means we already created a payment slip. Go modify it
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
    // All the debit accounts we will display
    $data['debit_accounts'] = xarMod::apiFunc('payments', 'user', 'get_debit_accounts', array('itemid' => $data['object']->properties['sender_itemid']->value));
    
    if(empty($data['debit_accounts'])) {
        return xarTpl::module('payments','user','errors',array('layout' => 'no_sender'));
    }
    
    // Set the debit account for this transaction as the first one
    $debit_account = current($data['debit_accounts']);

    // Now check if we have a currency of the same type as the payment
    foreach ($data['debit_accounts'] as $id => $account) {
        if ($account['currency'] == $data['object']->properties['currency']->value) {
            $debit_account = $account;
            break;
        }
    }

    // Set the chosen debit account
    $data['object']->properties['sender_itemid']->value = $debit_account['id'];
        
    // Set the debtor name
    $data['object']->properties['sender_account']->value = $debit_account['account_holder'];
        
    // Set the debtor address
    $lines = xarMod::apiFunc('payments', 'admin', 'unpack_address', array('address' => $debit_account['address']));
    if (!empty($lines[3])) $lines[2] .= " " . $lines[3];
    if (isset($lines[1])) $data['object']->properties['sender_line_2']->value  = $lines[1];
    if (isset($lines[2])) $data['object']->properties['sender_line_3']->value  = $lines[2];
    if (isset($lines[4])) $data['object']->properties['sender_line_4']->value  = $lines[4];

    // Set the debtor bank information
    $data['object']->properties['sender_iban']->value = $debit_account['iban'];
    $data['object']->properties['sender_bic']->value = $debit_account['bic'];
    $data['object']->properties['sender_clearing']->value = $debit_account['clearing'];

    // We always need a sender reference of sorts for the payment
    if (empty($data['object']->properties['sender_reference']->value)) 
        $data['object']->properties['sender_reference']->value = xarML('Undefined');

# --------------------------------------------------------
#
# Get the beneficiary information of the last payment if one exists
#
    $previous_exists = false;
    if (!empty($info['payment_object']) && !empty($info['payment_itemid'])) {
        $payments = DataObjectMaster::getObjectList(array('name' => 'payments_transactions'));
        $q = $payments->dataquery;
        $q->eq('payment_object', $info['payment_object']);
        $q->eq('beneficiary_object', $info['beneficiary_object']);
        $q->eq('beneficiary_itemid', $info['beneficiary_itemid']);
        $q->eq('state', 3);
        $q->setorder('time_processed', 'DESC');
        $items = $payments->getItems();
        if (!empty($items)) {
            $previous_exists = true;
            $item = reset($items);
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
    
    // If we have no previous payment, check if a payment type was passed
    if (($previous_exists == false) && isset($info['payment_type'])) {
        $data['object']->properties['payment_type']->value = $info['payment_type'];
        $data['payment_type'] = $info['payment_type'];
    }

    // Let the payment type from the template override everything else
    if (!xarVarFetch('payment_type',  'str',    $data['payment_type'],  '1',   XARVAR_NOT_REQUIRED)) return;
    $data['object']->properties['payment_type']->setValue($data['payment_type']);

# --------------------------------------------------------
#
# The create button was clicked
#
    if ($data['confirm']) {
    
        // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
        if(!xarVarFetch('preview', 'str', $preview,  NULL, XARVAR_DONT_SET)) {return;}

        // Check for a valid confirmation key
//        if(!xarSecConfirmAuthKey()) return;
        
        // Disable fields we are not using and don't want to check
        switch ($data['payment_type']) {
            // Orange slip
            case 1:
                $data['object']->properties['iban']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
                $data['object']->properties['bic']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
            break;
            // Red slip
            case '2.2':
                $data['object']->properties['bic']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
                $data['object']->properties['reference_number']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
            break;
            // Bank transfer
            case '3':
                $data['object']->properties['reference_number']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
            break;
            // Salary payment
            case 6:
                $data['object']->properties['bic']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
                $data['object']->properties['reference_number']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
            break;
        }
        
        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('payments','user','new_transaction', $data);        
        } else {
            // Good data: create the item
            $itemid = $data['object']->createItem();
            
            // If we are matching payments to some other object, create an entry in the matchings table
            if (!empty($info['payment_object']) && !empty($info['payment_itemid'])) {
                $tables = xarDB::getTables();
                $q = new Query('INSERT', $tables['payments_matchings']);
                $q->addfield('payment_id', $itemid);
                $q->addfield('object', $info['payment_object']);
                $q->addfield('itemid', $info['payment_itemid']);
                $q->addfield('settled_amount', $data['object']->properties['amount']->value);
                $q->run();
            }
            
            // Jump to the next page
            xarController::redirect(xarModURL('payments','user','view_transactions'));
            return true;
        }
    }
    return $data;
}
?>