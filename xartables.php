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
function commerce_xartables()
{
// Initialise table array
    $xartable = array();

    $commerce_address_book = xarDBGetSiteTablePrefix() . '_commerce_address_book';
    $commerce_customers_memo = xarDBGetSiteTablePrefix() . '_commerce_customers_memo';
    $commerce_products_xsell = xarDBGetSiteTablePrefix() . '_commerce_products_xsell';
    $commerce_address_format = xarDBGetSiteTablePrefix() . '_commerce_address_format';
    $commerce_admin_access = xarDBGetSiteTablePrefix() . '_commerce_admin_access';
    $commerce_banktransfer = xarDBGetSiteTablePrefix() . '_commerce_banktransfer';
    $commerce_banners = xarDBGetSiteTablePrefix() . '_commerce_banners';
    $commerce_banners_history = xarDBGetSiteTablePrefix() . '_commerce_banners_history';
    $commerce_categories = xarDBGetSiteTablePrefix() . '_commerce_categories';
    $commerce_categories_description = xarDBGetSiteTablePrefix() . '_commerce_categories_description';
    $commerce_configuration = xarDBGetSiteTablePrefix() . '_commerce_configuration';
    $commerce_configuration_group = xarDBGetSiteTablePrefix() . '_commerce_configuration_group';
    $commerce_counter = xarDBGetSiteTablePrefix() . '_commerce_counter';
    $commerce_counter_history = xarDBGetSiteTablePrefix() . '_commerce_counter_history';
    $commerce_countries = xarDBGetSiteTablePrefix() . '_commerce_countries';
    $commerce_currencies = xarDBGetSiteTablePrefix() . '_commerce_currencies';
    $commerce_customers = xarDBGetSiteTablePrefix() . '_commerce_customers';
    $commerce_customers_basket = xarDBGetSiteTablePrefix() . '_commerce_customers_basket';
    $commerce_customers_basket_attributes = xarDBGetSiteTablePrefix() . '_commerce_customers_basket_attributes';
    $commerce_customers_info = xarDBGetSiteTablePrefix() . '_commerce_customers_info';
    $commerce_customers_ip = xarDBGetSiteTablePrefix() . '_commerce_customers_ip';
    $commerce_customers_status = xarDBGetSiteTablePrefix() . '_commerce_customers_status';
    $commerce_customers_status_history = xarDBGetSiteTablePrefix() . '_commerce_customers_status_history';
    $commerce_languages = xarDBGetSiteTablePrefix() . '_commerce_languages';
    $commerce_manufacturers = xarDBGetSiteTablePrefix() . '_commerce_manufacturers';
    $commerce_manufacturers_info = xarDBGetSiteTablePrefix() . '_commerce_manufacturers_info';
    $commerce_newsletters = xarDBGetSiteTablePrefix() . '_commerce_newsletters';
    $commerce_newsletters_history = xarDBGetSiteTablePrefix() . '_commerce_newsletters_history';
    $commerce_orders = xarDBGetSiteTablePrefix() . '_commerce_orders';
    $commerce_orders_products = xarDBGetSiteTablePrefix() . '_commerce_orders_products';
    $commerce_orders_status = xarDBGetSiteTablePrefix() . '_commerce_orders_status';
    $commerce_orders_status_history = xarDBGetSiteTablePrefix() . '_commerce_orders_status_history';
    $commerce_orders_products_attributes = xarDBGetSiteTablePrefix() . '_commerce_orders_products_attributes';
    $commerce_orders_products_download = xarDBGetSiteTablePrefix() . '_commerce_orders_products_download';
    $commerce_orders_total = xarDBGetSiteTablePrefix() . '_commerce_orders_total';
    $commerce_products = xarDBGetSiteTablePrefix() . '_commerce_products';
    $commerce_products_attributes = xarDBGetSiteTablePrefix() . '_commerce_products_attributes';
    $commerce_products_attributes_download = xarDBGetSiteTablePrefix() . '_commerce_products_attributes_download';
    $commerce_products_description = xarDBGetSiteTablePrefix() . '_commerce_products_description';
    $commerce_products_notifications = xarDBGetSiteTablePrefix() . '_commerce_products_notifications';
    $commerce_products_options = xarDBGetSiteTablePrefix() . '_commerce_products_options';
    $commerce_products_options_values = xarDBGetSiteTablePrefix() . '_commerce_products_options_values';
    $commerce_products_options_values_to_products_options = xarDBGetSiteTablePrefix() . '_commerce_products_options_values_to_products_options';
    $commerce_products_graduated_prices = xarDBGetSiteTablePrefix() . '_commerce_products_graduated_prices';
    $commerce_products_to_categories = xarDBGetSiteTablePrefix() . '_commerce_products_to_categories';
    $commerce_reviews = xarDBGetSiteTablePrefix() . '_commerce_reviews';
    $commerce_reviews_description = xarDBGetSiteTablePrefix() . '_commerce_reviews_description';
    $commerce_sessions = xarDBGetSiteTablePrefix() . '_commerce_sessions';
    $commerce_shipping_status = xarDBGetSiteTablePrefix() . '_commerce_shipping_status';
    $commerce_specials = xarDBGetSiteTablePrefix() . '_commerce_specials';
    $commerce_tax_class = xarDBGetSiteTablePrefix() . '_commerce_tax_class';
    $commerce_tax_rates = xarDBGetSiteTablePrefix() . '_commerce_tax_rates';
    $commerce_geo_zones = xarDBGetSiteTablePrefix() . '_commerce_geo_zones';
    $commerce_whos_online = xarDBGetSiteTablePrefix() . '_commerce_whos_online';
    $commerce_zones = xarDBGetSiteTablePrefix() . '_commerce_zones';
    $commerce_zones_to_geo_zones = xarDBGetSiteTablePrefix() . '_commerce_zones_to_geo_zones';
    $commerce_box_align = xarDBGetSiteTablePrefix() . '_commerce_box_align';
    $commerce_content_manager = xarDBGetSiteTablePrefix() . '_commerce_content_manager';
    $commerce_media_content = xarDBGetSiteTablePrefix() . '_commerce_media_content';
    $commerce_products_content = xarDBGetSiteTablePrefix() . '_commerce_products_content';
    $commerce_module_newsletter = xarDBGetSiteTablePrefix() . '_commerce_module_newsletter';
    $commerce_cm_file_flags = xarDBGetSiteTablePrefix() . '_commerce_cm_file_flags';

    $xartable['commerce_address_book'] = $commerce_address_book;
    $xartable['commerce_customers_memo'] = $commerce_customers_memo;
    $xartable['commerce_products_xsell'] = $commerce_products_xsell;
    $xartable['commerce_address_format'] = $commerce_address_format;
    $xartable['commerce_admin_access'] = $commerce_admin_access;
    $xartable['commerce_banktransfer'] = $commerce_banktransfer;
    $xartable['commerce_banners'] = $commerce_banners;
    $xartable['commerce_banners_history'] = $commerce_banners_history;
    $xartable['commerce_categories'] = $commerce_categories;
    $xartable['commerce_categories_description'] = $commerce_categories_description;
    $xartable['commerce_configuration'] = $commerce_configuration;
    $xartable['commerce_configuration_group'] = $commerce_configuration_group;
    $xartable['commerce_counter'] = $commerce_counter;
    $xartable['commerce_counter_history'] = $commerce_counter_history;
    $xartable['commerce_countries'] = $commerce_countries;
    $xartable['commerce_currencies'] = $commerce_currencies;
    $xartable['commerce_customers'] = $commerce_customers;
    $xartable['commerce_customers_basket'] = $commerce_customers_basket;
    $xartable['commerce_customers_basket_attributes'] = $commerce_customers_basket_attributes;
    $xartable['commerce_customers_info'] = $commerce_customers_info;
    $xartable['commerce_customers_ip'] = $commerce_customers_ip;
    $xartable['commerce_customers_status'] = $commerce_customers_status;
    $xartable['commerce_customers_status_history'] = $commerce_customers_status_history;
    $xartable['commerce_languages'] = $commerce_languages;
    $xartable['commerce_manufacturers'] = $commerce_manufacturers;
    $xartable['commerce_manufacturers_info'] = $commerce_manufacturers_info;
    $xartable['commerce_newsletters'] = $commerce_newsletters;
    $xartable['commerce_newsletters_history'] = $commerce_newsletters_history;
    $xartable['commerce_orders'] = $commerce_orders;
    $xartable['commerce_orders_products'] = $commerce_orders_products;
    $xartable['commerce_orders_status'] = $commerce_orders_status;
    $xartable['commerce_orders_status_history'] = $commerce_orders_status_history;
    $xartable['commerce_orders_products_attributes'] = $commerce_orders_products_attributes;
    $xartable['commerce_orders_products_download'] = $commerce_orders_products_download;
    $xartable['commerce_orders_total'] = $commerce_orders_total;
    $xartable['commerce_products'] = $commerce_products;
    $xartable['commerce_products_attributes'] = $commerce_products_attributes;
    $xartable['commerce_products_attributes_download'] = $commerce_products_attributes_download;
    $xartable['commerce_products_description'] = $commerce_products_description;
    $xartable['commerce_products_notifications'] = $commerce_products_notifications;
    $xartable['commerce_products_options'] = $commerce_products_options;
    $xartable['commerce_products_options_values'] = $commerce_products_options_values;
    $xartable['commerce_products_options_values_to_products_options'] = $commerce_products_options_values_to_products_options;
    $xartable['commerce_products_graduated_prices'] = $commerce_products_graduated_prices;
    $xartable['commerce_products_to_categories'] = $commerce_products_to_categories;
    $xartable['commerce_reviews'] = $commerce_reviews;
    $xartable['commerce_reviews_description'] = $commerce_reviews_description;
    $xartable['commerce_sessions'] = $commerce_sessions;
    $xartable['commerce_shipping_status'] = $commerce_shipping_status;
    $xartable['commerce_specials'] = $commerce_specials;
    $xartable['commerce_tax_class'] = $commerce_tax_class;
    $xartable['commerce_tax_rates'] = $commerce_tax_rates;
    $xartable['commerce_geo_zones'] = $commerce_geo_zones;
    $xartable['commerce_whos_online'] = $commerce_whos_online;
    $xartable['commerce_zones'] = $commerce_zones;
    $xartable['commerce_zones_to_geo_zones'] = $commerce_zones_to_geo_zones;
    $xartable['commerce_box_align'] = $commerce_box_align;
    $xartable['commerce_content_manager'] = $commerce_content_manager;
    $xartable['commerce_media_content'] = $commerce_media_content;
    $xartable['commerce_products_content'] = $commerce_products_content;
    $xartable['commerce_module_newsletter'] = $commerce_module_newsletter;
    $xartable['commerce_cm_file_flags'] = $commerce_cm_file_flags;

    // Return the table information
    return $xartable;
}

?>