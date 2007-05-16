<?php
/**
 * File: $Id$
 *
 * Purpose of file:  Table information for roles module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage Roles module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

/**
 * specifies module tables namees
 *
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 * @access public
 * @param none $
 * @return $xartable array
 * @throws no exceptions
 * @todo nothing
 */
function carts_xartables()
{
// Initialise table array
    $xartable = array();

    $carts_configuration = xarDB::getPrefix() . '_carts_configuration';
    $carts_configuration_group = xarDB::getPrefix() . '_carts_configuration_group';
    $carts_counter = xarDB::getPrefix() . '_carts_counter';
    $carts_counter_history = xarDB::getPrefix() . '_carts_counter_history';
    $carts_customers_basket = xarDB::getPrefix() . '_carts_customers_basket';
    $carts_customers_basket_attributes = xarDB::getPrefix() . '_carts_customers_basket_attributes';

    $xartable['carts_configuration'] = $carts_configuration;
    $xartable['carts_configuration_group'] = $carts_configuration_group;
    $xartable['carts_counter'] = $carts_counter;
    $xartable['carts_counter_history'] = $carts_counter_history;
    $xartable['carts_customers_basket'] = $carts_customers_basket;
    $xartable['carts_customers_basket_attributes'] = $carts_customers_basket_attributes;

    // Return the table information
    return $xartable;
}

?>