<?php

/**
 * Table definition file
 * @package calendar
 * @copyright (C) 2002 by the Xaraya Calendar Team
 */

/**
 * This function is called internally by the core
 * whenever the module is loaded.
 */
function calendar_xartables()
{
    $xartables = array();
    $prefix = xarDBGetSiteTablePrefix() . '_calendar';

    $xartables['calendar_calendar'] = $prefix . '_calendar';
    $xartables['calendar_event'] = $prefix . '_event';

    return $xartables;
} ?>
