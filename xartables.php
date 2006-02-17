<?php
/**
 * Julian table definitions
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module development team
 */
/**
 * This function is called internally by the core
 * whenever the module is loaded.
 * @author Roger Raymond <roger@asphyxia.com>
 * @author MichelV <michelv@xaraya.com>
 * @return array with all tables in this module
 */
function julian_xartables()
{
    // Initialise table array
    $xartable = array();

    // set up the events table
    $events = xarDBGetSiteTablePrefix() . '_julian_events';
    $xartable['julian_events'] = $events;

    // set up the categories table for the upgrade function
    $categories = xarDBGetSiteTablePrefix() . '_julian_categories';
    $xartable['julian_categories'] = $categories;

    // set up the category_properties table
    $category_properties = xarDBGetSiteTablePrefix() . '_julian_category_properties';
    $xartable['julian_category_properties'] = $category_properties;

    // set up the categories table for the upgrade function
    $alerts = xarDBGetSiteTablePrefix() . '_julian_alerts';
    $xartable['julian_alerts'] = $alerts;

    // set up the category_properties table
    $linkage = xarDBGetSiteTablePrefix() . '_julian_events_linkage';
    $xartable['julian_events_linkage'] = $linkage;

    return $xartable;
}

?>
