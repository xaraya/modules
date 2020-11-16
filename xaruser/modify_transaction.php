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
    if (!xarSecurityCheck('EditPayments')) {
        return;
    }

    if (!xarVarFetch('itemid', 'int', $data['itemid'], 0, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('confirm', 'checkbox', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('debit_account', 'int', $data['debit_account'], 0, XARVAR_NOT_REQUIRED)) {
        return;
    }

    # --------------------------------------------------------
#
    # Get the payment transactions object
#
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => 'payments_transactions'));
    $data['object']->getItem(array('itemid' => $data['itemid']));
    $data['tplmodule'] = 'payments';

    // Allow overiding the payment type from the form
    $payment_type = $data['object']->properties['payment_type']->value;
    if (!xarVarFetch('payment_type', 'str', $data['payment_type'], $payment_type, XARVAR_NOT_REQUIRED)) {
        return;
    }
    $data['object']->properties['payment_type']->value = $data['payment_type'];
    
    # --------------------------------------------------------
#
    # Check if we are passing an api item
#
    if (!xarVarFetch('api', 'str', $api, '', XARVAR_NOT_REQUIRED)) {
        return;
    }

    $info = array();
    if (!empty($api)) {
        $function = rawurldecode($api);
        eval("\$info = $function;");
        
        foreach ($info as $key => $value) {
            if (isset($data['object']->properties[$key])) {
                $data['object']->properties[$key]->value = $value;
            }
        }
    }

    # --------------------------------------------------------
#
    # Get the debit account information
#
    // All the debit accounts we will display
    $data['debit_accounts'] = xarMod::apiFunc('payments', 'user', 'get_debit_accounts', array('sender_object' => $data['object']->properties['sender_object']->value,
                                                                                              'sender_itemid' => $data['object']->properties['sender_itemid']->value,
                                                                                        ));

    if (empty($data['debit_accounts'])) {
        return xarTpl::module('payments', 'user', 'errors', array('layout' => 'no_sender'));
    }
    
    // The debit account for this transaction
    $debit_account = array();
    foreach ($data['debit_accounts'] as $account) {
        if ($account['sender_itemid'] == $data['object']->properties['sender_itemid']->value) {
            $debit_account = $account;
        }
    }

    if (empty($debit_account)) {
        die(xarML('Debit account not found'));
    }
    
    // Set the debtor name
    $data['object']->properties['sender_account']->value = $debit_account['account_holder'];
        
    // Set the debtor address
    $lines = xarMod::apiFunc('payments', 'admin', 'unpack_address', array('address' => $debit_account['address']));
    if (!empty($lines[3])) {
        $lines[2] .= " " . $lines[3];
    }
    if (isset($lines[1])) {
        $data['object']->properties['sender_line_2']->value  = $lines[1];
    }
    if (isset($lines[2])) {
        $data['object']->properties['sender_line_3']->value  = $lines[2];
    }
    if (isset($lines[4])) {
        $data['object']->properties['sender_line_4']->value  = $lines[4];
    }

    // Set the debtor bank information
    $data['object']->properties['sender_iban']->value = $debit_account['iban'];
    $data['object']->properties['sender_bic']->value = $debit_account['bic'];
    $data['object']->properties['sender_clearing']->value = $debit_account['clearing'];

    // We always need a sender reference of sorts for the payment
    if (empty($data['object']->properties['sender_reference']->value)) {
        $data['object']->properties['sender_reference']->value = xarML('Undefined');
    }

    # --------------------------------------------------------
#
    # The update button was clicked
#
    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return;
        }

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
            return xarTplModule('payments', 'user', 'modify_transaction', $data);
        } else {
            // Good data: create the item
            $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
            
            // Update the entry in the matchings table
            $tables = xarDB::getTables();
            $q = new Query('UPDATE', $tables['payments_matchings']);
            $q->eq('payment_id', $itemid);
            $q->addfield('settled_amount', $data['object']->properties['amount']->value);
            $q->run();
            
            // Jump to the next page
            xarController::redirect(xarModURL('payments', 'user', 'view_transactions'));
            return true;
        }
    }
    return $data;
}
