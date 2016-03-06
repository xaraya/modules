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

    if (!xarVarFetch('confirm',      'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('payment_type', 'str',    $data['payment_type'],'827',     XARVAR_NOT_REQUIRED)) return;
    
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
# Check if we are passing an object item
#
    if (!xarVarFetch('api',          'str',    $api,            '', XARVAR_NOT_REQUIRED)) return;
    
    if (!empty($api)) {
        $function = rawurldecode($api);var_dump();exit;
    }
    $sourceobject = DataObjectMaster::getObject(array('name' => $object));
    $sourceobject->getItem(array('itemid' => $itemid));
    
    // If we have data, transfer it to the new object
    $sourcefields = $sourceobject->getFieldValues(array(), 1);
    if (!empty($sourcefields)) {
        if (isset($sourcefields['amount'])) $data['object']->properties['amount']->value = $sourcefields['amount'];
        if (isset($sourcefields['currency'])) $data['object']->properties['currency']->value = $sourcefields['currency'];
        if (isset($sourcefields['transaction_date'])) $data['object']->properties['transaction_date']->value = $sourcefields['transaction_date'];
        if (isset($sourcefields['code'])) $data['object']->properties['sender_reference']->value = $sourcefields['code'];
    }

# --------------------------------------------------------
#
# Get the debit account information
#
    $data['debit_account'] = DataObjectMaster::getObject(array('name' => 'payments_debit_account'));
    $data['debit_account']->getItem(array('itemid' => 1));
    $debit_fields = $data['debit_account']->getFieldValues(array(), 1);
    
    $data['object']->properties['sender_account']->value = $debit_fields['account_holder'];
    $data['object']->properties['sender_line_1']->value = $debit_fields['address_1'];
    $data['object']->properties['sender_line_2']->value = $debit_fields['address_2'];
    $data['object']->properties['sender_line_3']->value = $debit_fields['address_3'];
    $data['object']->properties['sender_line_4']->value = $debit_fields['address_4'];

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
            xarController::redirect(xarModURL('payments','user','view_transaction'));
            return true;
        }
    }
    return $data;
}
?>