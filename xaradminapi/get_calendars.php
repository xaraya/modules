<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * File: $Id:
 *
 * Standard function to generate the common admin menu configuration
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage calendar
 * @author andrea.m
 */
/**
 * generate the common admin menu configuration
 */
function calendar_adminapi_get_calendars()
{
    // Initialise the array that will hold the menu configuration
    $cals = array();

    $curdir = sys::varpath() . '/calendar';

    $ics_array = array();

    if ($dir = @opendir($curdir)) {
        while(($file = @readdir($dir)) !== false) {
            if (preg_match('/\.(ics)$/',$file)) {
                $ics_array[] = $file;
            }
        }
    }

    $cals['icsfiles']=$ics_array;
    $cals['thereAreIcs']=sizeof($ics_array);

    // Return the array containing the menu configuration
    return $cals;
}

?>
