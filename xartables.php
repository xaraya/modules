<?php
/**
 * Return table names to Xaraya
 *
 * @package modules
 * @copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage products
 * @author marcinmilan
 *
 *  based on:
 * (c) 2003 XT-Commerce
 * (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
 * (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
 */

/**
 * Return table names to Xaraya
 *
 * @return array xartables
 */
function products_xartables()
{
    $xartables = array();
    $prefix = xarDBGetSiteTablePrefix();

    $xartable['products_categories'] = $prefix . '_products_categories';
    $xartable['products_categories_description'] = $prefix . '_products_categories_description';
    $xartable['products_configuration'] = $prefix . '_products_configuration';
    $xartable['products_configuration_group'] = $prefix . '_products_configuration_group';
    $xartable['products_manufacturers'] = $prefix . '_products_manufacturers';
    $xartable['products_manufacturers_info'] = $prefix . '_products_manufacturers_info';
    $xartable['products_product'] = $prefix . '_products_product';
    $xartable['products_product_attributes'] = $prefix . '_products_product_attributes';
    $xartable['products_product_attributes_download'] = $prefix . '_products_product_attributes_download';
    $xartable['products_product_description'] = $prefix . '_products_product_description';
    $xartable['products_product_notifications'] = $prefix . '_products_product_notifications';
    $xartable['products_product_options'] = $prefix . '_products_product_options';
    $xartable['products_product_options_values'] = $prefix . '_products_products_option_values';
    $xartable['products_product_options_values_to_products_options'] = $prefix . '_products_product_options_values_to_product_options';
    $xartable['products_product_graduated_prices'] = $prefix . '_products_product_graduated_prices';
    $xartable['products_product_to_categories'] = $prefix . '_products_product_to_categories';
    $xartable['products_product_content'] = $prefix . '_products_product_content';
    $xartable['products_content_manager'] = $prefix . '_products_content_manager';

    return $xartable;
}
?>