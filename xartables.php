<?php
/**
 * Table definition file
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Calendar Team
 * @link http://www.xarcalendar.com
 *
 * @author Roger Raymond <roger@asphyxia.com>
 */

/**
 * This function is called internally by the core
 * whenever the module is loaded.
 */
function julian_xartables()
{
    // Initialise table array
    $xartable = array();

    // set up the events table
    $events = xarDBGetSiteTablePrefix() . '_julian_events';
    $xartable['julian_events'] = $events;

    // set up the tasks table
    //$attendee = xarDBGetSiteTablePrefix() . '_julian_attendees';
    //$xartable['julian_attendees'] = $attendee;

    // set up the calendar share table
    //$shares = xarDBGetSiteTablePrefix() . '_julian_shares';
    //$xartable['julian_shares'] = $shares;

    // set up the calendar alarms table
    //$alarms = xarDBGetSiteTablePrefix() . '_julian_alarms';
    //$xartable['julian_alarms'] = $alarms;

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
