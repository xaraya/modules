<?php

/**
 * Calculate the duration between two dates, in various ways.
 *
 * @param startdate integer Start date (Unix timestamp)
 * @param enddate integer End date (Unix timestamp)
 * @return array Array of duration elements, as listed below; array() if dates are invalid
 *
 * Duration elements returned:
 * - 
 *
 */

function ievents_userapi_duration($args)
{
    static $quanta = NULL;

    extract($args);
    $return = array();

    // Return if the dates are not valid.
    if (!isset($startdate) || !is_numeric($startdate) || !isset($enddate) || !is_numeric($enddate)) return $return;

    // We quanitise this duration a little, perhaps to the nearest ten or fifteen minutes.

    // Quanta of the hour divisions, in minutes.
    // Set to zero for no quantisation.
    if (!isset($quanta)) $quanta = xarModGetVar('ievents', 'quanta');

    // TODO: complete this
}

?>
