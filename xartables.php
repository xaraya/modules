<?php

/**
 * File: $Id$
 *
 * Table registration for tasks module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 *
 * @subpackage tasks
 * @author Chad Kraeft
*/

/**
 * tasks table regitration
 */
function tasks_xartables()
{
    $xartable = array();

    $tasks = xarDBGetSiteTablePrefix() . '_tasks';

    // Set the table name
    $xartable['tasks'] = $tasks;

    // Return the table information
    return $xartable;
}
?>