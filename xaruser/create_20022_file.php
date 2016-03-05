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
 * Create a new item of the payments_dta object
 *
 */

function payments_user_create_20022_file()
{
    if (!xarSecurityCheck('AddPayments')) return;
    
    // Make sure comments in templates are switched off
    if (xarModVars::get('themes', 'ShowTemplates')) {
        return xarTpl::module('payments','user','errors',array('layout' => 'no_comments'));
    }

    if (!xarVarFetch('name',       'str',    $name,            'payments_transactions', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,    'int',    $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;

    $data['tplmodule'] = 'payments';

    // Get the debit account information
    $data['debit_account'] = DataObjectMaster::getObject(array('name' => 'payments_debit_account'));
    $data['debit_account']->getItem(array('itemid' => 1));
    $debit_fields = $data['debit_account']->getFieldValues(array(), 1);
    
    // Misc info
    $data['payment_method'] = "TRF";
    $data['batch_booking'] = "true";
    $data['group_reference'] = 1;
    $data['message_identifier'] = xarMod::apiFunc('payments', 'admin', 'get_message_identifier', array('id' => 1));
    if(empty($data['message_identifier'])) {
        return xarTpl::module('payments','user','errors',array('layout' => 'bad_msg_identifier'));
    }
    // Get the payments object
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObjectList(array('name' => $name));
    $q = $data['object']->dataquery;
    
    // Get the items to be transmitted
    if (!empty($data['itemid'])) {
        $q->eq('id', $data['itemid']);
    } else {
    }
    $data['items'] = $data['object']->getItems();

    // Generate the number of transactions
    $data['number_of_transactions'] = count($data['items']);
    
    $data['control_sum'] = 0;
    
    sys::import('xaraya.structures.query');
    $tobject = new XarDateTime();
    $tobject->setTimestamp(time());
    $tobject->setSecond(0);
    $tobject->setMinute(0);
    $tobject->setHour(0);
    $today = $tobject->getTimestamp();
    
    // Run through the transactions and do validity checks
    foreach ($data['items'] as $key => $item) {
        // Add the debit fields to the corresponding properties in the DTA object
        $data['items'][$key]['sender_account'] = $debit_fields['account_holder'];
        $data['items'][$key]['sender_line_1']  = $debit_fields['address_1'];
        $data['items'][$key]['sender_line_2']  = $debit_fields['address_2'];
        $data['items'][$key]['sender_line_3']  = $debit_fields['address_3'];
        $data['items'][$key]['sender_line_4']  = $debit_fields['address_4'];
        $data['items'][$key]['sender_iban']    = $debit_fields['iban'];
        $data['items'][$key]['sender_bic']     = $debit_fields['bic'];
        $data['items'][$key]['processed']      = time();

        // Generate the control sum
        $data['control_sum'] += $item['amount'];
        
        // Cannot send in the past
        $tobject->setTimestamp((int)$item['transaction_date']);
        $tobject->setSecond(0);
        $tobject->setMinute(0);
        $tobject->setHour(0);
        $send_date = $tobject->getTimestamp();
        if ($send_date < $today) {
            $data['items'][$key]['transaction_date'] = $today;
        }
    }
    
    // Create an XML declaration
    $output = '<?xml version="1.0" encoding="utf-8"?>';
    // Add the file contents
    $output .= xarTpl::module('payments', 'user', 'create_20022_file', $data);

    $filename = 'ISO20022Export_' . time() . ".xml";
    file_put_contents($filename, $output);
        
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $output;
    exit;
}
?>