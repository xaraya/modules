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
    if (!xarSecurityCheck('ProcessPayments') || !xarUserIsLoggedIn()) return;
    xarTplSetPageTitle('View ISO20022 Payments');

    // Load the user's daemon
    sys::import('modules.payments.class.daemon');
    $daemon = Daemon::getInstance();
    $data = $daemon->checkInput();

#------------------------------------------------------------
#  Set the time frame
#
    sys::import('modules.dynamicdata.class.properties.master');
    $timeframe = DataPropertyMaster::getProperty(array('name' => 'timeframe'));
    
    // The period gets saved for user convenience
    if(!xarVarFetch('refresh',     'int',   $data['refresh'], 0, XARVAR_NOT_REQUIRED)) {return;}
    $data['period'] = $daemon->getCurrentPeriod('gl');
    if ($data['refresh']) {
        $timeframe->checkInput('period');
        $data['period'] = $timeframe->getValue();
        $daemon->setCurrentPeriod('gl', $data['period']);
    }
#------------------------------------------------------------

    $data['object'] = DataObjectMaster::getObjectList(array('name' => 'payments_transactions'));
    $q = $data['object']->dataquery;
    
    // Only active payments
//    $q->eq('state', 3);
    
    // Only payments within the chosen period
    // Add 60 days to the future, which ISO20022 payments allow
    $q->ge('transaction_date', $data['period'][0]);
    $q->le('transaction_date', $data['period'][1] + 3600*24*60);
    $q->setorder('transaction_date', 'DESC');

    return $data;
}
?>
