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
 * Create a file to send to the bank
 *
 */

function payments_user_create_20022_file()
{
    if (!xarSecurity::check('AddPayments')) return;

    // Make sure comments in templates are switched off
    if (xarModVars::get('themes', 'ShowTemplates')) {die("Fix Me: HTML comments are on. Please turn them off in the themes module backend.");
        return xarTpl::module('payments','user','errors',array('layout' => 'no_comments'));
    }

    if (!xarVar::fetch('confirm',    'bool',   $data['confirm'], false,     xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('itemid' ,    'int',    $data['itemid'] , 0 ,        xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('idlist' ,    'str',    $data['idlist'] , array() ,  xarVar::NOT_REQUIRED)) return;

    // Cater to both arrays and strings
    if (!empty($data['idlist']) && !is_array($data['idlist'])) $data['idlist'] = explode(',', $data['idlist']);
    
    $data['tplmodule'] = 'payments';

# --------------------------------------------------------
#
# Define miscellaneous information
#
    $data['group_reference'] = time() . "-" . xarUser::getVar('id');
    $data['payment_method'] = "TRF";
    $data['batch_booking'] = "true";
    $data['message_identifier'] = xarMod::apiFunc('payments', 'admin', 'get_message_identifier');
    if(empty($data['message_identifier'])) {
        return xarTpl::module('payments','user','errors',array('layout' => 'bad_msg_identifier'));
    }

# --------------------------------------------------------
#
# Get the payments to be transmitted
#
    // Get the payments object
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObjectList(array('name' => 'payments_transactions'));
    $q = $data['object']->dataquery;
    
    if (!empty($data['itemid'])) {
        $q->eq('id', $data['itemid']);
    } elseif (!empty($data['idlist'])) {
        $q->in('id', $data['idlist']);
    } else {
        return xarTpl::module('payments','user','errors',array('layout' => 'no_payments_id'));
    }
    $data['items'] = $data['object']->getItems();

# --------------------------------------------------------
#
# Run through the payments and do validity checks
#
    sys::import('xaraya.structures.query');
    $tobject = new XarDateTime();
    $tobject->setTimestamp(time());
    $tobject->setSecond(0);
    $tobject->setMinute(0);
    $tobject->setHour(0);
    $today = $tobject->getTimestamp();
    
    foreach ($data['items'] as $key => $item) {
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

# --------------------------------------------------------
#
# Get the debit account object
#
    $data['debit_account'] = DataObjectMaster::getObject(array('name' => 'payments_debit_account'));

# --------------------------------------------------------
#
# Update fields of the payment items
#
    // Generate the number of payments
    $data['number_of_transactions'] = count($data['items']);
    
    $data['transaction'] = DataObjectMaster::getObject(array('name' => 'payments_transactions'));
    $data['control_sum'] = 0;

    foreach ($data['items'] as $key => $item) {
        // Get the debit account information for this payment
        $data['debit_account']->getItem(array('itemid' => $item['sender_itemid']));
        $debit_fields = $data['debit_account']->getFieldValues(array(), 1);
        $data['debit_address'] = xarMod::apiFunc('payments', 'admin', 'unpack_address', array('address' => $debit_fields['address']));

        // Add the debit fields to the corresponding properties in the DTA object
        $data['items'][$key]['sender_account']  = $debit_fields['account_holder'];
        $data['items'][$key]['sender_line_1']   = $debit_fields['address_1'];
        $data['items'][$key]['sender_line_2']   = $debit_fields['address_2'];
        $data['items'][$key]['sender_line_3']   = $debit_fields['address_3'];
        $data['items'][$key]['sender_line_4']   = $debit_fields['address_4'];
        $data['items'][$key]['sender_iban']     = $debit_fields['iban'];
        $data['items'][$key]['sender_bic']      = $debit_fields['bic'];
        $data['items'][$key]['sender_clearing'] = $debit_fields['clearing'];
        $data['items'][$key]['processed']       = time();

        // Add miscellaneous information
        $data['items'][$key]['payment_method']  = $data['payment_method'];
        $data['items'][$key]['batch_booking']   = $data['batch_booking'];
        $data['items'][$key]['group_reference'] = $data['group_reference'];
        $data['items'][$key]['message_id']      = $data['message_identifier'];
    
        switch ($item['currency']) {
            case 'USD':
            case 'EUR':
            case 'CHF':
            default:
                $decimals = 2;
            break;
        }
        $data['items'][$key]['amount'] = number_format($item['amount'], $decimals, '.', '');
        
        // Generate the control sum
        $data['control_sum'] += $item['amount'];
    }

    try {
        foreach ($data['items'] as $key => $item) {        
            // Get this transaction
            $data['transaction']->getItem(array('itemid' => $item['id']));
            // Add the data
            $data['transaction']->setFieldValues($data['items'][$key],1);
            // Update the database transaction
            $data['transaction']->updateItem(array('itemid' => $item['id']));
        }
    } catch (Exception $e) {
        return xarTpl::module('payments','user','errors',array('layout' => 'payment_slip_fail'));
    }
# --------------------------------------------------------
#
# Send the file to the browser
#
    // Create an XML declaration
    $output = '<?xml version="1.0" encoding="UTF-8"?>';
    // Add the file contents
    $output .= xarTpl::module('payments', 'user', 'create_20022_file', $data);

    $filename = 'ISO20022_' . $data['message_identifier'] . '_' . time() . ".xml";
    file_put_contents($filename, $output);
        
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $output;
    exit;
}
?>