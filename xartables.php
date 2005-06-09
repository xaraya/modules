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

    $xartable['commerce_address_book'] = xarDBGetSiteTablePrefix() . '_commerce_address_book';
    $xartable['commerce_admin_access'] = xarDBGetSiteTablePrefix() . '_commerce_admin_access';
    $xartable['commerce_banktransfer'] = xarDBGetSiteTablePrefix() . '_commerce_banktransfer';
    $xartable['commerce_banners'] = xarDBGetSiteTablePrefix() . '_commerce_banners';
    $xartable['commerce_banners_history'] = xarDBGetSiteTablePrefix() . '_commerce_banners_history';
    $xartable['commerce_box_align'] = xarDBGetSiteTablePrefix() . '_commerce_box_align';
    $xartable['commerce_cm_file_flags'] = xarDBGetSiteTablePrefix() . '_commerce_cm_file_flags';
    $xartable['commerce_configuration'] = xarDBGetSiteTablePrefix() . '_commerce_configuration';
    $xartable['commerce_content_manager'] = xarDBGetSiteTablePrefix() . '_commerce_content_manager';
    $xartable['commerce_counter'] = xarDBGetSiteTablePrefix() . '_commerce_counter';
    $xartable['commerce_counter_history'] = xarDBGetSiteTablePrefix() . '_commerce_counter_history';
    $xartable['commerce_customers'] = xarDBGetSiteTablePrefix() . '_commerce_customers';
    $xartable['commerce_customers_basket'] = xarDBGetSiteTablePrefix() . '_commerce_customers_basket';
    $xartable['commerce_customers_basket_attributes'] = xarDBGetSiteTablePrefix() . '_commerce_customers_basket_attributes';
    $xartable['commerce_customers_info'] = xarDBGetSiteTablePrefix() . '_commerce_customers_info';
    $xartable['commerce_customers_ip'] = xarDBGetSiteTablePrefix() . '_commerce_customers_ip';
    $xartable['commerce_customers_memo'] = xarDBGetSiteTablePrefix() . '_commerce_customers_memo';
    $xartable['commerce_customers_status'] = xarDBGetSiteTablePrefix() . '_commerce_customers_status';
    $xartable['commerce_customers_status_history'] = xarDBGetSiteTablePrefix() . '_commerce_customers_status_history';
    $xartable['commerce_media_content'] = xarDBGetSiteTablePrefix() . '_commerce_media_content';
    $xartable['commerce_newsletters'] = xarDBGetSiteTablePrefix() . '_commerce_newsletters';
    $xartable['commerce_newsletters_history'] = xarDBGetSiteTablePrefix() . '_commerce_newsletters_history';
    $xartable['commerce_orders'] = xarDBGetSiteTablePrefix() . '_commerce_orders';
    $xartable['commerce_orders_products'] = xarDBGetSiteTablePrefix() . '_commerce_orders_products';
    $xartable['commerce_orders_products_attributes'] = xarDBGetSiteTablePrefix() . '_commerce_orders_products_attributes';
    $xartable['commerce_orders_products_download'] = xarDBGetSiteTablePrefix() . '_commerce_orders_products_download';
    $xartable['commerce_orders_status'] = xarDBGetSiteTablePrefix() . '_commerce_orders_status';
    $xartable['commerce_orders_status_history'] = xarDBGetSiteTablePrefix() . '_commerce_orders_status_history';
    $xartable['commerce_orders_total'] = xarDBGetSiteTablePrefix() . '_commerce_orders_total';
    $xartable['commerce_products'] = xarDBGetSiteTablePrefix() . '_commerce_products';
    $xartable['commerce_products_attributes'] = xarDBGetSiteTablePrefix() . '_commerce_products_attributes';
    $xartable['commerce_products_attributes_download'] = xarDBGetSiteTablePrefix() . '_commerce_products_attributes_download';
    $xartable['commerce_products_content'] = xarDBGetSiteTablePrefix() . '_commerce_products_content';
    $xartable['commerce_products_description'] = xarDBGetSiteTablePrefix() . '_commerce_products_description';
    $xartable['commerce_products_graduated_prices'] = xarDBGetSiteTablePrefix() . '_commerce_products_graduated_prices';
    $xartable['commerce_products_notifications'] = xarDBGetSiteTablePrefix() . '_commerce_products_notifications';
    $xartable['commerce_products_options'] = xarDBGetSiteTablePrefix() . '_commerce_products_options';
    $xartable['commerce_products_options_values'] = xarDBGetSiteTablePrefix() . '_commerce_products_options_values';
    $xartable['commerce_products_options_values_to_products_options'] = xarDBGetSiteTablePrefix() . '_commerce_products_options_values_to_products_options';
    $xartable['commerce_products_to_categories'] = xarDBGetSiteTablePrefix() . '_commerce_products_to_categories';
    $xartable['commerce_products_xsell'] = xarDBGetSiteTablePrefix() . '_commerce_products_xsell';
    $xartable['commerce_reviews'] = xarDBGetSiteTablePrefix() . '_commerce_reviews';
    $xartable['commerce_reviews_description'] = xarDBGetSiteTablePrefix() . '_commerce_reviews_description';
    $xartable['commerce_sessions'] = xarDBGetSiteTablePrefix() . '_commerce_sessions';
    $xartable['commerce_shipping_status'] = xarDBGetSiteTablePrefix() . '_commerce_shipping_status';
    $xartable['commerce_specials'] = xarDBGetSiteTablePrefix() . '_commerce_specials';
    $xartable['commerce_whos_online'] = xarDBGetSiteTablePrefix() . '_commerce_whos_online';


    // Return the table information
    return $xartable;
}

?>