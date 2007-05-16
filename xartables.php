<?php
/**
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
function commerce_xartables()
{
// Initialise table array
    $xartable = array();

    $xartable['commerce_address_book'] = xarDB::getPrefix() . '_commerce_address_book';
    $xartable['commerce_banktransfer'] = xarDB::getPrefix() . '_commerce_banktransfer';
    $xartable['commerce_banners'] = xarDB::getPrefix() . '_commerce_banners';
    $xartable['commerce_banners_history'] = xarDB::getPrefix() . '_commerce_banners_history';
    $xartable['commerce_cm_file_flags'] = xarDB::getPrefix() . '_commerce_cm_file_flags';
    $xartable['commerce_configuration'] = xarDB::getPrefix() . '_commerce_configuration';
    $xartable['commerce_content_manager'] = xarDB::getPrefix() . '_commerce_content_manager';
    $xartable['commerce_counter'] = xarDB::getPrefix() . '_commerce_counter';
    $xartable['commerce_counter_history'] = xarDB::getPrefix() . '_commerce_counter_history';
    $xartable['commerce_customers'] = xarDB::getPrefix() . '_commerce_customers';
    $xartable['commerce_customers_basket'] = xarDB::getPrefix() . '_commerce_customers_basket';
    $xartable['commerce_customers_basket_attributes'] = xarDB::getPrefix() . '_commerce_customers_basket_attributes';
    $xartable['commerce_customers_info'] = xarDB::getPrefix() . '_commerce_customers_info';
    $xartable['commerce_customers_ip'] = xarDB::getPrefix() . '_commerce_customers_ip';
    $xartable['commerce_customers_memo'] = xarDB::getPrefix() . '_commerce_customers_memo';
    $xartable['commerce_customers_status'] = xarDB::getPrefix() . '_commerce_customers_status';
    $xartable['commerce_customers_status_history'] = xarDB::getPrefix() . '_commerce_customers_status_history';
    $xartable['commerce_media_content'] = xarDB::getPrefix() . '_commerce_media_content';
    $xartable['commerce_newsletters'] = xarDB::getPrefix() . '_commerce_newsletters';
    $xartable['commerce_newsletters_history'] = xarDB::getPrefix() . '_commerce_newsletters_history';
    $xartable['commerce_orders'] = xarDB::getPrefix() . '_commerce_orders';
    $xartable['commerce_orders_products'] = xarDB::getPrefix() . '_commerce_orders_products';
    $xartable['commerce_orders_products_attributes'] = xarDB::getPrefix() . '_commerce_orders_products_attributes';
    $xartable['commerce_orders_products_download'] = xarDB::getPrefix() . '_commerce_orders_products_download';
    $xartable['commerce_orders_status'] = xarDB::getPrefix() . '_commerce_orders_status';
    $xartable['commerce_orders_status_history'] = xarDB::getPrefix() . '_commerce_orders_status_history';
    $xartable['commerce_orders_total'] = xarDB::getPrefix() . '_commerce_orders_total';
    $xartable['commerce_products'] = xarDB::getPrefix() . '_commerce_products';
    $xartable['commerce_products_attributes'] = xarDB::getPrefix() . '_commerce_products_attributes';
    $xartable['commerce_products_attributes_download'] = xarDB::getPrefix() . '_commerce_products_attributes_download';
    $xartable['commerce_products_content'] = xarDB::getPrefix() . '_commerce_products_content';
    $xartable['commerce_products_description'] = xarDB::getPrefix() . '_commerce_products_description';
    $xartable['commerce_products_graduated_prices'] = xarDB::getPrefix() . '_commerce_products_graduated_prices';
    $xartable['commerce_products_notifications'] = xarDB::getPrefix() . '_commerce_products_notifications';
    $xartable['commerce_products_options'] = xarDB::getPrefix() . '_commerce_products_options';
    $xartable['commerce_products_options_values'] = xarDB::getPrefix() . '_commerce_products_options_values';
    $xartable['commerce_products_options_values_to_products_options'] = xarDB::getPrefix() . '_commerce_products_options_values_to_products_options';
    $xartable['commerce_products_to_categories'] = xarDB::getPrefix() . '_commerce_products_to_categories';
    $xartable['commerce_products_xsell'] = xarDB::getPrefix() . '_commerce_products_xsell';
    $xartable['commerce_reviews'] = xarDB::getPrefix() . '_commerce_reviews';
    $xartable['commerce_reviews_description'] = xarDB::getPrefix() . '_commerce_reviews_description';
    $xartable['commerce_sessions'] = xarDB::getPrefix() . '_commerce_sessions';
    $xartable['commerce_shipping_status'] = xarDB::getPrefix() . '_commerce_shipping_status';
    $xartable['commerce_specials'] = xarDB::getPrefix() . '_commerce_specials';
    $xartable['commerce_whos_online'] = xarDB::getPrefix() . '_commerce_whos_online';


    // Return the table information
    return $xartable;
}

?>