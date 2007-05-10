<?php

/**
 * Fetch the global parameters and settings for the module.
 * @param name string The name of an individual parameter to return
 *
 * @todo Some of these parameters may have user overrides, and may
 * ultimately be stored as module variables,
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

        // The number of days that an event is flagged as 'new' from when it was created.
        $params['days_new'] = 5;

        // The number of days an avent is flagged as 'updated' from when it was last updated.
        $params['days_updated'] = 3;

        // Default events per page in listing.
        $params['default_numitems'] = 10;
        $params['max_numitems'] = 200;

        // Default start and end dates, in 'strtotime' format.
        $params['default_startdate'] = 'now';
        $params['default_enddate'] = '+1 month';

        // Output transform fields.
        // Only these fields will be passed through the output transform.
        // They will generally just be the HTML fields.
        // TODO: perhaps the output transforms and filters should converge, e.g. a field that
        // is declared 'html' will always have the 'html' filter applied, and will always be
        // passed through the output transforms. The 'html' filter should happen *before* the
        // transform and not afterwards in the template.
        // TODO: depracate
        //$params['output_transform'] = 'address,description';

        // See notes above. We are probably going to go with this one.
        $params['html_fields'] = 'description,contact_details';

        // Summary max words (for text fields in summary mode).
        // This is really a display thing, and deserves to live in the templates.
        // TODO: it may not actually be used yet.
        $params['summary_max_words'] = 100;

        // Default year range in drop-downs
        // TODO: the year ranges should come from actual dates
        $params['year_range_min'] = -3;
        $params['year_range_max'] = +5;
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