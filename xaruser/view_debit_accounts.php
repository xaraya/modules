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
 * View items of the debit_account object
 *
 */
function payments_user_view_debit_accounts($args)
{
    // Data Managers have access
    if (!xarSecurityCheck('ProcessPayments') || !xarUserIsLoggedIn()) return;
    xarTplSetPageTitle('View Debit Accounts');

    // Load the user's daemon
    sys::import('modules.payments.class.daemon');
    $daemon = Daemon::getInstance();
    $data = $daemon->checkInput();

#------------------------------------------------------------

    $data['object'] = DataObjectMaster::getObjectList(array('name' => 'payments_debit_account'));
    $q = $data['object']->dataquery;
    
    // Only active payments
    $q->eq('state', 3);
    
    return $data;
}
?>
