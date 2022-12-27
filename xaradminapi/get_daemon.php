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
 * Return the ouser's daemon
 *
 * If we have a LedgerBA module installed, we'll assume the ledger daemon should be used
 */

function payments_adminapi_get_daemon()
{
    if (xarMod::isAvailable('ledgerba')) {
        sys::import('modules.ledgerba.class.daemon');
        $daemon = LedgerDaemon::getInstance();
    } else {
        sys::import('modules.payments.class.daemon');
        $daemon = PaymentsDaemon::getInstance();
    }
    return $daemon;
}
