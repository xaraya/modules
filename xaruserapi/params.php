<?php

/**
 * Fetch the global parameters and settings for the module.
 * @param name string The name of an individual parameter to return
 *
 * @todo Some of these parameters may have user overrides
 */

function ievents_userapi_params($args)
{
    static $params = array();

    extract($args);

    if (empty($params)) {
        // Initialise the parameter list.

        // First day of the week.
        // 0=Sunday; 1=Monday
        $params['startdayofweek'] = 1;

        // Time quanta.
        // The smallest chunk of time dealt with (minutes).
        // Make sure it is divisible into 60 by a whole number.
        // Range is 0 to 60, where '0' disables the quantisation feature.
        $params['quanta'] = 15;

    }

    if (!empty($name)) {
        // Return a single parameter
        if (isset($params[$name])) {
            return $params[$name];
        } else {
            return NULL;
        }
    } else {
        // Return all parameters
        return $params;
    }

    // Function will return before this point.
}

?>