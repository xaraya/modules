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
 * Return a list of debit accounts
 *
 */

function payments_userapi_get_debit_accounts($args=array())
{
    if (!isset($args['sender_object'])) $args['sender_object'] = '';
    if (!isset($args['sender_itemid'])) $args['sender_object'] = 0;
    
    sys::import('modules.dynamicdata.class.objects.master');
    $debit_account_object = DataObjectMaster::getObjectList(array('name' => 'payments_debit_account'));
    $q = $debit_account_object->dataquery;
    
    // We need either a transaction ID or a sender object and itemid combination
    if (!isset($args['sender_object']) && !isset($args['sender_itemid']) && !isset($args['itemid'])) 
        die(xarML('We need either a transaction ID or a sender object and itemid combination'));

    if (!empty($args['sender_itemid'])) {
        // In this case we are passed a debit account ID. 
        // Get all the accounts that have the same sender object and itemid
        $tables = xarDB::getTables();
        $debit_q = new Query('SELECT', $tables['payments_debit_account']);
        $debit_q->addfield('sender_object');
        $debit_q->addfield('sender_itemid');
        $debit_q->eq('sender_itemid', $args['sender_itemid']);
        $debit_q->run();
        $transaction = $debit_q->row();
        if (empty($transaction)) {
            $args['sender_object'] = '';
            $args['sender_itemid'] = null;
        } else {
            $args['sender_object'] = $transaction['sender_object'];
            $args['sender_itemid'] = $transaction['sender_itemid'];
        }
    }

    // An empty sender object and itemid at this point means no constraints on the accounts to be shown
    if (!(empty($args['sender_object']) && empty($args['sender_itemid']))) {
        $q->eq('sender_object', $args['sender_object']);
        $q->eq('sender_itemid', $args['sender_itemid']);
    }
    $accounts = $debit_account_object->getItems();
    echo "<br/><br/>";
    $q->qecho();
    $debit_q->qecho();
    return $accounts;
}

?>