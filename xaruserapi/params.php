<?php

/**
 * Fetch the global parameters and settings for the module.
 * @param name string The name of an individual parameter to return
 * @param names string or array List of parameters to return as a numeric keyed array
 * @param knames string or array List of paremeters to return as a name keyed array
 *
 * @todo Some of these parameters may have user overrides, and may
 * ultimately be stored as module variables.
 * - Use 'names' with list() to assign values to variables.
 * - Use 'knames' with extract() to create variables.
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
        // Range is 0 to 60 (e.g. 5, 10, 15, 20, 30), where '0' disables the quantisation feature.
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
        $params['default_numitems'] = 20;
        $params['max_numitems'] = 200;

        // Default start and end dates, in 'strtotime' format.
        // TODO: allow a default 'daterange' name instead.
        $params['default_startdate'] = 'now';
        $params['default_enddate'] = '+6 months';
        $params['default_daterange'] = 'next6months';

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
        $params['text_fields'] = 'summary,location_address';

        // Summary max words (for text fields in summary mode).
        // This is really a display thing, and deserves to live in the templates.
        // TODO: it may not actually be used yet.
        $params['summary_max_words'] = 100;

        // Default year range in drop-downs
        // TODO: the year ranges should come from actual dates
        $params['year_range_min'] = -3;
        $params['year_range_max'] = +5;

        // Fields included in query-text searches
        $params['q_fields'] = 'title,summary,location_venue,location_postcode,external_ref';

        // Default group type.
        // Options are: none or '', day, week, month, year
        $params['default_group'] = 'week';

        // The complete list of options for grouping.
        $params['grouplist'] = array(
            'none' => xarML('No group'),
            'day' => xarML('Daily'),
            'week' => xarML('Weekly'),
            'month' => xarML('Monthly'),
            'year' => xarML('Annual'),
        );

        // Default date range for calendar subscriptions.
        $params['cal_subscribe_range'] = 'window4months';
        $params['cal_subscribe_numitems'] = 100;

        // Format of the full address.
        // Fields are surrounded by {curly brackets} with contents matching the DD property names.
        // {LB} is an optional line break (these will be collapsed)
        // {NL} is a forced line break (will always appear)
        $params['address_format'] = '{location_venue}{LB}{location_address}{LB}{location_postcode}{LB}{location_country}';

        // The prefixes of properties that will be grouped into arrays for ease
        // of use in the templates.
        $params['group_prefixes'] = 'location,contact';

        // Default listing sort order
        $params['default_listing_sort'] = 'startdate ASC';
        $params['listing_sort_options'] = array(
            'startdate ASC' => 'Date (earliest first)',
            'startdate DESC' => 'Date (latest first)',
        );

        // Date range list, used to provide a handy set of date ranges in various places
        $params['daterangelist'] = array(
            '' => xarML('-- Date range --'),
            'next4weeks' => xarML('Next four weeks'),
            'next6months' => xarML('Next six months'),
            'thisyear' => xarML('This year (Jan-Dec)'),
            'nextyear' => xarML('Next year (Jan-Dec)'),
            'thismonth' => xarML('This month'),
            'nextmonth' => xarML('Next month'),
            'thisweek' => xarML('This week'),
            'nextweek' => xarML('Next week'),
            'today' => xarML('Today'),
        );

        // Maximum category depth shown in the jump menu.
        // 1 is just a single level (the root cat, shown as an option group, and one level below that)
        $params['max_cat_depth'] = 2;

        // The various display formats.
        // Each of these maps to different summary and display templates.
        $params['default_display_format'] = 'list';
        $params['display_formats'] = array(
            'list' => xarML('Listings'),
            'cal' => xarML('Calendar'),
        );

        // If true, then all category searches are performed
        // as a tree search (i.e. selected category and all descendants).
        $params['category_tree_search'] = true;

        // Get locale data, so we can get month and day names.
        $localeData = xarMLSLoadLocaleData();
        $params['locale'] = array(
            'months' => array('short' => array(), 'long' => array()),
            'days' => array('short' => array(), 'long' => array()),
        );

        // Months are 1-indexed.
        for($i = 1; $i <= 12; $i+=1) {
            $params['locale']['months']['short'][$i] = $localeData["/dateSymbols/months/${i}/short"];
            $params['locale']['months']['long'][$i] = $localeData["/dateSymbols/months/${i}/full"];
        }

        // Days are zero-indexed. They will be rotated so the start day of the week comes first. Sunday is zero.
        for($i = 1; $i <= 7; $i+=1) {
            $params['locale']['days']['short'][($i+$params['startdayofweek']+6) % 7] =
                $localeData["/dateSymbols/weekdays/" . (($i+$params['startdayofweek']+6) % 7 + 1) . "/short"];
            $params['locale']['days']['long'][($i+$params['startdayofweek']+6) % 7] =
                $localeData["/dateSymbols/weekdays/" . (($i+$params['startdayofweek']+6) % 7 + 1) . "/full"];
        }

        //var_dump($localeData);
        //var_dump($params['locale']['days']['long']);
    }

    if (!empty($name)) {
        // Return a single parameter
        if (isset($params[$name])) {
            $return = $params[$name];
        } else {
            $return = NULL;
        }
    } elseif (!empty($names) || !empty($knames)) {
        // Multiple names as a comma-separated list
        $return = array();

        if (!empty($knames)) $names = $knames;

        if (is_string($names)) $names = explode(',', $names);

        // Loop for each name and look up its value.
        foreach($names as $name) {
            // Trim in case there are spaces in the list.
            $name = trim($name);

            if (!empty($knames)) {
                $return[$name] = ievents_userapi_params(array('name' => $name));
            } else {
                $return[] = ievents_userapi_params(array('name' => $name));
            }
        }
    } else {
        // Return all parameters
        $return = $params;
    }

    return $return;
}

?>
