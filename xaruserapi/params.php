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
        $params = array();

        // First day of the week.
        // 0=Sunday; 1=Monday
        $params['startdayofweek'] = 1;

        // Time quanta.
        // The smallest chunk of time dealt with (minutes).
        // Make sure it is divisible into 60 by a whole number.
        // Range is 0 to 60, where '0' disables the quantisation feature.
        $params['quanta'] = 15;

        // The maximum number of categories that can be added to an event.
        $params['maxcats'] = 10;

        // Standard itemtypes.
        $params['itemtype_events'] = 1;
        $params['itemtype_calendars'] = 2;

        $module = 'ievents';
        $params['module'] = $module;
        $params['modid'] = xarModGetIDFromName($module);
    }

    if (!empty($name)) {
        // Return a single parameter
        if (isset($params[$name])) {
            $return = $params[$name];
        } else {
            $return = NULL;
        }
    } elseif (!empty($names)) {
        // Multiple names as a comma-separated list
        $return = array();

        if (is_string($names)) $names = explode(',', $names);

        // Loop for each name and look up its value.
        foreach($names as $name) {
            $return[] = ievents_userapi_params(array('name' => $name));
        }
    } else {
        // Return all parameters
        $return = $params;
    }

    return $return;
}

?>