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
 * View items of the payments_transactions object
 *
 */
function payments_user_view_transactions($args)
{
    // Data Managers have access
    if (!xarSecurity::check('ProcessPayments') || !xarUser::isLoggedIn()) {
        return;
    }
    xarTpl::setPageTitle('View ISO20022 Payments');

    // Load the user's daemon
    $daemon = xarMod::apiFunc('payments', 'admin', 'get_daemon');
    $data = $daemon->checkInput();

    #------------------------------------------------------------
    #  Set the time frame
#
    sys::import('modules.dynamicdata.class.properties.master');
    $timeframe = DataPropertyMaster::getProperty(['name' => 'timeframe']);

    // The period gets saved for user convenience
    if (!xarVar::fetch('refresh', 'int', $data['refresh'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    $data['period'] = $daemon->getCurrentPeriod('gl');
    if ($data['refresh']) {
        $timeframe->checkInput('period');
        $data['period'] = $timeframe->getValue();
        $daemon->setCurrentPeriod('gl', $data['period']);
    }
    #------------------------------------------------------------

    $data['object'] = DataObjectMaster::getObjectList(['name' => 'payments_transactions']);
    $q = $data['object']->dataquery;

    // If we are using the ledger modules...
    if (xarMod::isAvailable('ledgerba')) {
        $q->like('sender_object', 'ledgerba_mandant');
        $q->eq('sender_itemid', $daemon->getCurrentMandant());
    /*
    // Add the debit_accounts table to the query
    $tables = xarDB::getTables();
    $q->addtable($tables['payments_debit_account'], 'da');
    $q->join('payments.sender_itemid', 'da.id');
    // Only accounts of this mandant
    $q->eq('da.sender_object', 'ledgerba_mandant');
    $q->eq('da.sender_itemid', $daemon->getCurrentMandant());
    */
    } else {
        // Object is a reserved word for now ...
        if (!empty($args['obj'])) {
            $q->eq('sender_object', $args['obj']);
        }
        if (!empty($args['itemid'])) {
            $q->eq('sender_itemid', $args['itemid']);
        }
    }

    // Only active payments
//    $q->eq('state', 3);

    // Only payments within the chosen period
    // Add 60 days to the future, which ISO20022 payments allow
    $q->ge('payments.transaction_date', $data['period'][0]);
    $q->le('payments.transaction_date', $data['period'][1] + 3600*24*60);
    $q->setorder('payments.transaction_date', 'DESC');
    $q->setorder('payments.time_created', 'DESC');

//    $q->qecho();

    return $data;
}
