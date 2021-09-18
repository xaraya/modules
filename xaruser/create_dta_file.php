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
    if (!xarSecurity::check('AddPayments')) {
        return;
    }

    if (!xarVar::fetch('name', 'str', $name, 'payments_transactions', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['tplmodule'] = 'payments';

    // Get the debit account information
    $data['debit_account'] = DataObjectMaster::getObject(['name' => 'payments_debit_account']);
    $data['debit_account']->getItem(['itemid' => 1]);
    $debit_fields = $data['debit_account']->getFieldValues([], 1);

    // Get the payments object
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObjectList(['name' => $name]);
    $q = $data['object']->dataquery;

    if (!empty($data['itemid'])) {
        $q->eq('id', $data['itemid']);
    } else {
    }
    $items = $data['object']->getItems();

    // Get the DTA class to create a file
    sys::import('modules.payments.class.dtafile');
    $dta = new DTA_File("NTN16", (int)$debit_fields['clearing']);

    foreach ($items as $item) {
        // Add the debit fields to the corresponding properties in the DTA object
        $item['sender_line_1'] = $debit_fields['address_1'];
        $item['sender_line_2'] = $debit_fields['address_2'];
        $item['sender_line_3'] = $debit_fields['address_3'];
        $item['sender_line_4'] = $debit_fields['address_4'];

        // Create a transaction
        $i = $dta->addTransaction($item['payment_type']);
        $thisTransaction = $dta->loadTransaction($i);

        // Add values
        $thisTransaction->setCreationDate((int)$item['transaction_date']);
        $thisTransaction->setRecipientClearingNr((int)$item['bic']);
        $thisTransaction->setDebitAccount($debit_fields['iban']);

        $thisTransaction->setClient($debit_fields['address_1'], $debit_fields['address_2'], $debit_fields['address_3'], $debit_fields['address_4']);

        $thisTransaction->setPaymentAmount((float)$item['amount'], $item['currency'], $item['transaction_date']);
        $thisTransaction->setRecipient($item['post_account'], $item['address_1'], $item['address_2'], $item['address_3'], $item['address_4']);
        $lines = explode(PHP_EOL, $item['reason']);
        $thisTransaction->setPaymentReason($lines);

        // Save it
        $dta->saveTransaction($i, $thisTransaction);
    }
    $dta->download();
}
