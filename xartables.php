<?php
/*/
 * shopping/xartables.php 1.00 July 25th 2003 jared_rich@excite.com
 *
 * Shopping Module Table Name Defs File
 *
 * copyright (C) 2003 by Jared Rich
 * license GPL <http://www.gnu.org/licenses/gpl.html>
 * author: Jared Rich
/*/

/*/
 * Return shopping table names to xaraya
 *
 * This function is called internally by the core whenever the module is loaded.
 *
 * @return: array of table names
/*/
function shopping_xartables()
{
    // Initialise table array
    $xartables = array();

    // Set the table names
    $prefix = xarDBGetSiteTablePrefix();
    $xartables['shopping_orders'] = $prefix . '_shopping_orders';
    $xartables['shopping_orders_details'] = $prefix . '_shopping_orders_details';
    $xartables['shopping_cart'] = $prefix . '_shopping_cart';
    $xartables['shopping_items'] = $prefix . '_shopping_items';
    $xartables['shopping_items_pics'] = $prefix . '_shopping_items_pics';
    $xartables['shopping_recommendations'] = $prefix . '_shopping_recommendations';
    $xartables['shopping_profiles'] =$prefix . '_shopping_profiles';

    // Return the table information
    return $xartables;
}

?>
