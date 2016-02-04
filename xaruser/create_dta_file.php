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
 * Create a new item of the paymnts_dta object
 *
 */

function payments_user_create_dta_file()
{
    if (!xarSecurityCheck('AddPayments')) return;

    if (!xarVarFetch('name',       'str',    $name,            'payments_dta', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    // Get the payments object
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['tplmodule'] = 'payments';
    
    // Get the debit account information
    $data['debit_account'] = DataObjectMaster::getObject(array('name' => 'payments_debit_account'));
    $data['debit_account']->getItem('itemid' => 1);
    $debit_fields = $data['debit_account']->getFieldValues(array(), 1);

    sys::import('modules.payments.class.dta_TA827');
    $dta = new DTA_TA827();
    
    $fields = $data['object']->getFieldValues(array(), 1);
    echo "<pre>";var_dump($fields);
//    exit;

    $dta->setRecipientClearingNr(292);
    $dta->setCreationDate((int)$fields['transaction_date']);
    $dta->setClientClearingNr($debit_fields['clearing']);
    $dta->setPaymentAmount((float)$fields['amount'], $fields['currency'], $fields['transaction_date']);
    $dta->setClient($debit_fields['address_1'], $debit_fields['address_2'], $debit_fields['address_3'], $debit_fields['address_4']);
    $dta->setRecipient($fields['post_account'], $fields['address_1'], $fields['address_2'], $fields['address_3'], $fields['address_4']);
    $lines = explode('\n', $fields['reason']);
    $dta->setPaymentReason($lines);

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;
        
        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('payments','user','new_dta', $data);        
        } else {
            // Good data: create the item
            $itemid = $data['object']->createItem();
            
            // Jump to the next page
            xarController::redirect(xarModURL('payments','user','view_dta'));
            return true;
        }
    }
    return $data;
}
?>