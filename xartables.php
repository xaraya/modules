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
 *
 * Table information
 *
 */

function payments_xartables()
{
    // Initialise table array
    $xartable = array();

    $xartable['payments_gateways'] = xarDB::getPrefix() . '_payments_gateways';
    $xartable['payments_paymentmethods'] = xarDB::getPrefix() . '_payments_paymentmethods';
    $xartable['payments_ccpayments'] = xarDB::getPrefix() . '_payments_ccpayments';
    $xartable['payments_gateways_config'] = xarDB::getPrefix() . '_payments_gateways_config';
    $xartable['payments_relation'] = xarDB::getPrefix() . '_payments_relation';
    $xartable['payments_transactions'] = xarDB::getPrefix() . 'payments_transactions';
    $xartable['payments_debit_account'] = xarDB::getPrefix() . 'payments_debit_account';

    // Return the table information
    return $xartable;
}
?>
