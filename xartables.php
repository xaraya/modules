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
function products_xartables()
{
// Initialise table array
    $xartable = array();

    $products_categories = xarDBGetSiteTablePrefix() . '_products_categories';
    $products_categories_description = xarDBGetSiteTablePrefix() . '_products_categories_description';
    $products_configuration = xarDBGetSiteTablePrefix() . '_products_configuration';
    $products_configuration_group = xarDBGetSiteTablePrefix() . '_products_configuration_group';
    $products_products = xarDBGetSiteTablePrefix() . '_products_products';
    $products_products_attributes = xarDBGetSiteTablePrefix() . '_products_products_attributes';
    $products_products_attributes_download = xarDBGetSiteTablePrefix() . '_products_products_attributes_download';
    $products_products_description = xarDBGetSiteTablePrefix() . '_products_products_description';
    $products_products_notifications = xarDBGetSiteTablePrefix() . '_products_products_notifications';
    $products_products_options = xarDBGetSiteTablePrefix() . '_products_products_options';
    $products_products_options_values = xarDBGetSiteTablePrefix() . '_products_products_options_values';
    $products_products_options_values_to_products_options = xarDBGetSiteTablePrefix() . '_products_products_options_values_to_products_options';
    $products_products_graduated_prices = xarDBGetSiteTablePrefix() . '_products_products_graduated_prices';
    $products_products_to_categories = xarDBGetSiteTablePrefix() . '_products_products_to_categories';
    $products_products_content = xarDBGetSiteTablePrefix() . '_products_products_content';
    $products_content_manager = xarDBGetSiteTablePrefix() . '_products_content_manager';

    $xartable['products_categories'] = $products_categories;
    $xartable['products_categories_description'] = $products_categories_description;
    $xartable['products_configuration'] = $products_configuration;
    $xartable['products_configuration_group'] = $products_configuration_group;
    $xartable['products_products'] = $products_products;
    $xartable['products_products_attributes'] = $products_products_attributes;
    $xartable['products_products_attributes_download'] = $products_products_attributes_download;
    $xartable['products_products_description'] = $products_products_description;
    $xartable['products_products_notifications'] = $products_products_notifications;
    $xartable['products_products_options'] = $products_products_options;
    $xartable['products_products_options_values'] = $products_products_options_values;
    $xartable['products_products_options_values_to_products_options'] = $products_products_options_values_to_products_options;
    $xartable['products_products_graduated_prices'] = $products_products_graduated_prices;
    $xartable['products_products_to_categories'] = $products_products_to_categories;
    $xartable['products_products_content'] = $products_products_content;
    $xartable['products_content_manager'] = $products_content_manager;

    // Return the table information
    return $xartable;
}

?>