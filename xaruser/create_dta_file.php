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
    if (!xarVarFetch('itemid' ,    'int',    $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;

    $data['tplmodule'] = 'payments';
    
    // Get the debit account information
    $data['debit_account'] = DataObjectMaster::getObject(array('name' => 'payments_debit_account'));
    $data['debit_account']->getItem(array('itemid' => 1));
    $debit_fields = $data['debit_account']->getFieldValues(array(), 1);
    
    // Get the payments object
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObjectList(array('name' => $name));
    $q = $data['object']->dataquery;
    $items = $data['object']->getItems();
    
 echo "<pre>";
 
    // Get the DTA class to create a file
    sys::import('modules.payments.class.dtafile');
    $dta = new DTA_File("LCL16", (int)$debit_fields['clearing']);
    
    // Get the DTA class
//    sys::import('modules.payments.class.dta_TA827');
//    $dta = new DTA_TA827();
//    $dta->setDataFileSender("LCL16");
//    $dta->setClientClearingNr((int)$debit_fields['clearing']);
//    $dta->setDebitAccount($debit_fields['iban']);
//    $dta->setClient($debit_fields['address_1'], $debit_fields['address_2'], $debit_fields['address_3'], $debit_fields['address_4']);
    
    $index = 1;
    $total_amount = 0;
    $dta_file_contents = '';
    foreach ($items as $item) {
        $dta->addtransaction($item['dta_type']);
        // Add the debit fields to the corresponding properties in the DTA object
        $item['sender_line_1'] = $debit_fields['address_1'];
        $item['sender_line_2'] = $debit_fields['address_2'];
        $item['sender_line_3'] = $debit_fields['address_3'];
        $item['sender_line_4'] = $debit_fields['address_4'];

        // Header information
//        $dta->setCreationDate((int)$item['transaction_date']);
//        $dta->setRecipientClearingNr((int)$item['bic']);
    
//        $dta->setPaymentAmount((float)$item['amount'], $item['currency'], $item['transaction_date']);
//        $dta->setRecipient($item['post_account'], $item['address_1'], $item['address_2'], $item['address_3'], $item['address_4']);
//        $lines = explode(PHP_EOL, $item['reason']);
//        $dta->setPaymentReason($lines);
    
//        $dta->setInputSequenceNr($index);
        $index++;
        $total_amount += $item['amount'];
        
        $dta->download();
//        var_dump($dta->getRecord());exit;
//        $dta_file_contents .= $dta->getRecord();
    }
    
//    var_dump($items);
/*   
    sys::import('modules.payments.class.dta_TA890');
    $dta = new DTA_TA890();

    // Header information
    $dta->setCreationDate();
    $dta->setRecipientClearingNr();
    $dta->setClientClearingNr((int)$debit_fields['clearing']);
    $dta->setDataFileSender("LCL16");
    $dta->setInputSequenceNr($index);

    // Record information
    $dta->setTotalAmount($total_amount);
    $dta->getRecord();
    $dta_file_contents .= $dta->getRecord();
*/
exit;
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