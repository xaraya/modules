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

        $params['startdayofweek'] = 1;
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