<?php

/**
 * Get multiple events.
 *
 * @param cid int Calendar ID
 * @param cids int Calendar IDs (array)
 * @param sort string Order-by clause for the query
 * @param startdate int Inclusive start date (unix timestamp)
 * @param enddate int Inclusive end date (unix timestamp)
 * @param drule string The date range rule to use ('start' or 'overlap')
 * @param catid int Category ID to match
 * @param catids array Multiple category IDs to match
 * @param crule string Rule used to bind multiple categories ('and' or 'or', defaulting to 'and')
 * @param docount boolean If set, then just return a count of matching events
 * @param created_by string Either the user ID or teh string 'myself' to match only events created by that user
 * @param external_source string Matches the extrnal source
 * @param external_ref string Matches the external reference
 * @param flags string or array Various flags to match on (flags are 'OR'ed). Flags are single letters.
 * @param q string Query text, accepts space-separated keywords
 *
 * @todo Fetch matching categories into each event.
 * @todo Zero is a valid date - need to ensure it is treated that way throughout. NULL is an empty date (for the DB too)
 * @todo Further date rules: single/multiple-day, fully-enclosed, overlapping etc. (perhaps as an output flag too)
 * @todo Group fields with 'location' and 'contact' prefixes. [done]
 * @todo Fix the calendar lookup: if we ask for a calendar, then we must stick to it
 * @todo Support date range "extends into, but starts before" to display events that range into the selected period
 *
 * Need to ensure the cid and cids don't interfere with the category IDs
 * ('cid' was probably a bad choice)
 */

function ievents_userapi_getevents($args)
{
    extract($args);

    static $static_object = NULL;

    list($module, $modid, $itemtype, $q_fields, $group_prefixes, $address_format, $default_listing_sort, $category_tree_search) =
        xarModAPIfunc('ievents', 'user', 'params', array('names' => 'module,modid,itemtype_events,q_fields,group_prefixes,address_format,default_listing_sort,category_tree_search'));

    // Default return value (array or 0, depending on whether doing a count).
    if (empty($docount)) {
        $return = array();
    } else {
        $return = 0;
    }

    // Only used for some text escaping methods.
    $dbconn =& xarDBGetConn();

    // Get the calendars the user can access.
    // These are the calendars the user has at least OVERVIEW privilege on.
    // (more accurately, the calendars with which the user has at least overview privileges on its events)
    if (!isset($cids)) $cids = array();
    if (!empty($cid)) $cids = array($cid);
    $calendars = xarModAPIfunc('ievents', 'user', 'getcalendars', array('event_priv' => 'OVERVIEW', 'cids' => $cids));

    $cids = array_keys($calendars);

    // Initialise the item fetch array.
    // Further details are added to it below.
    $params = array(
        'module' => $module,
        'itemtype' => $itemtype,
    );

    $where_arr = array();

    // Include the calendars in the query.
    // If user is an admin, just select them all.
    // If not an admin, and cids is empty, then there are no valid calendars, so don't go any further.
    if (empty($cids) && !xarSecurityCheck('AdminIEvent', 0, 'IEvent')) {
        // We are not an administrator and we have no calendars.
        // Return immediately.
        // When doing a count, this will be numeric (zero).

        return $return;
    }

    // Put the calendar IDs into the where-clause
    // We are assuming there will be a couple of dozen calendars max, and
    // not one for each user. Not everything needs to be scaleable, but it
    // is good to know what is and is not in advance.
    if (!empty($cids) && xarVarValidate('list:id', $cids, true)) {
        $where_arr[] = 'calendar_id in (' . implode(', ', $cids) . ')';
    }

    // Start and end dates.
    // There are two rules we could apply:
    // - fetch any event that starts within a given date range ('start' - the default)
    // - fetch any event that falls anywhere within a given date range ('overlap')
    // The 'overlap' method would be used in recurring events (when and if they get supported)
    if (!xarVarValidate('pre:lower:passthru:enum:start:overlap', $drule, true)) $drule = 'start';

    // Make sure they are both dates integers (unix timestamps).
    // Resolution for fetching is a full day.
    if (!xarVarValidate('int', $startdate, true)) {
        unset($startdate);
    } else {
        $startdate = strtotime(date('Y-m-d', $startdate));
    }
    if (!xarVarValidate('int', $enddate, true)) {
        unset($enddate);
    } else {
        $enddate = strtotime(date('Y-m-d 23:59:59', $enddate));
    }

    if ($drule == 'start') {
        if ((!empty($startdate)) && (!empty($enddate))) {
            $where_arr[] = "start_date BETWEEN $startdate AND $enddate";
        } elseif (!empty($startdate)) {
            $where_arr[] = "start_date ge $startdate";
        } elseif (!empty($enddate)) {
            $where_arr[] = "start_date le $enddate";
        }
    } else {
        // TODO: this bit probably needs a bit of a look at.
        // Is there a portable version of IFNULL() or NVL()?
        if ((!empty($startdate)) && (!empty($enddate))) {
            $where_arr[] = "(start_date BETWEEN $startdate AND $enddate OR $enddate BETWEEN IF(start_date IS NULL, $enddate, start_date) AND IF(end_date IS NULL, $enddate, end_date) )";
        } elseif (!empty($startdate)) {
            $where_arr[] = "(start_date ge $startdate OR (end_date IS NOT NULL AND end_date ge $startdate))";
        } elseif (!empty($enddate)) {
            $where_arr[] = "start_date le $enddate AND (end_date ge $enddate OR end_date IS NULL)";
        }
    }

    // Select a single event
    if (xarVarValidate('id', $eid, true)) $where_arr[] = 'eid = ' . $eid;

    // Created_by - either specify the user id, or the keyword 'myself' to return
    // all events created by the current user.
    if (!empty($created_by)) {
        if ($created_by == 'myself' && xarUserIsLoggedIn()) $created_by = xarUserGetVar('uid');
        if (xarVarValidate('id', $created_by, true)) {
            $where_arr[] = 'created_by eq ' . $created_by;
        }
    }

    // Deal with external sources and references.
    if (xarVarValidate('str:1', $external_source, true)) {
        // Being a string, we need to be careful about escaping characters.
        // We hope there will be nothing in the source reference to need escaping anyway.
        $where_arr[] = 'external_source eq ' . $dbconn->qstr($external_source);
    }
    if (xarVarValidate('str:1', $external_ref, true)) {
        // Being a string, we need to be careful about escaping characters.
        // We hope there will be nothing in the source reference to need escaping anyway.
        $where_arr[] = 'external_ref eq ' . $dbconn->qstr($external_ref);
    }

    // Flags (comma-separated list of single upper-case characters)
    if (xarVarValidate('strlist:,:pre:upper:alpha:passthru:str:1:1', $flags, true)) $flags = explode(',', $flags);
    if (xarVarValidate('list:pre:upper:alpha:passthru:str:1:1', $flags, true)) {
        // Turn each flag into an individual query test.
        $flag_tests = array();
        foreach($flags as $flag) $flag_tests[] = 'flags LIKE ' . $dbconn->qstr("%${flag}%");
        $where_arr[] = '( ' . implode(' OR ', $flag_tests) . ' )';
    }

    // Query text (keywords)
    if (!empty($q) && !empty($q_fields)) {
        xarVarValidate('pre:trim:left:200:passthru:strlist: :pre:trim:passthru:str::30', $q, true);
        // Remove duplicate runs of spaces, then split into words
        // We don't support "quoted phrases" in this simple keyword search.
        $q = preg_replace('/ +/', ' ', $q);

        $q_fields_arr = explode(',', $q_fields);

        $q_where = array();
        foreach(explode(' ', $q) as $q_word) {
            $q_where_words = array();
            foreach($q_fields_arr as $q_field) {
                $q_where_words[] = $q_field . ' LIKE ' . $dbconn->qstr("%${q_word}%");
            }
            $q_where[] = ' (' . implode(' OR ', $q_where_words) . ') ';
        }
        if (empty($qrule) || $qrule == 'and') {
            $where_arr[] = implode(' AND ', $q_where);
        } else {
            $where_arr[] = ' (' . implode(' OR ', $q_where) . ') ';
        }
    }


    // Serialise the where-clause if we have anything in it.
    if (!empty($where_arr)) $where = implode(' AND ', $where_arr);
    if (!empty($where)) $params['where'] = $where;


    // Parameters after here do not appear in the where-clause.

    // Categories search
    // If a single catid, pass that in.
    // TODO: validate it.
    if (xarVarValidate('regexp:/_{0,1}[0-9]+/', $catid, true)) $params['catid'] = $catid;

    // If an array of catids, pass them in
    // Allow 'OR' logic, using '-' as the joiner.
    // TODO: validate the catids values (must all be numeric)
    // TODO: force the query into GROUP BY or DISTINCT mode with multiple categories.
    if (xarVarValidate('list:regexp:/_{0,1}[0-9]+/', $catids, true)) {
        if (!xarVarValidate('pre:lower:passthru:enum:and:or', $crule, true)) $crule = 'and';

        // If the tree search flag us set, then prefix each category ID
        // with a '_' to force the search to include sub-categories (descendants).
        if (!empty($category_tree_search)) {
            foreach($catids as $key1 => $value1) {
                if (preg_match('/^[0-9]+$/', $value1)) $catids[$key1] = '_' . $value1;
            }
        }

        $params['catid'] = implode((($crule == 'or') ? '-' : '+'), $catids);
    }

    if (!empty($docount)) {
        // Just count events if requested.
        // This count will be pretty close, but still does not quite cut it with
        // the privileges, since we could be counting events we have no permission to see.
        $return = (int)xarModAPIfunc(
            'dynamicdata', 'user', 'countitems', $params
        );
    } else {
        // Parameters after here are not required for the count version of the query.
        
        // Allows individual fields to be selected.
        // Useful in drop-down selections where only limited data is required.
        // Some fields are mandatory, such as 'eid', 'calendar_id' and 'created_by' - make sure they are available.
        if (xarVarValidate('strlist:,:pre:ftoken', $fieldlist, true)) {
            $fieldlist = explode(',', $fieldlist);
            foreach(array('eid', 'calendar_id', 'created_by') as $mandatory_field) {
                if (!in_array($mandatory_field, $fieldlist)) $fieldlist[] = $mandatory_field;
            }
            $fieldlist = implode(',', $fieldlist);
            $params['fieldlist'] = $fieldlist;
        }

        // Sort order
        // TODO: create 'sort' by combining some easier-to-handle parameters.
        // Note also that some property names do not match the table column names.
        // TODO: validate the sort columns.
        if (empty($sort)) $sort = $default_listing_sort;
        $params['sort'] = $sort;

        // startnum and numitems (used by the pager)
        $params['startnum'] = (isset($startnum) ? $startnum : 1);
        $params['numitems'] = (isset($numitems) ? $numitems : -1);

        $events = xarModAPIfunc('dynamicdata', 'user', 'getitems', $params);

        // Some fields are grouped. We need to prepare an array of grouped
        // fields. Start by getting a list of all the fields available in the
        // events object.
        if (!empty($static_object)) {
            $object = $static_object;
        } else {
            $object = xarModAPIFunc(
                'dynamicdata', 'user', 'getobject',
                array('modid' => $modid, 'itemtype' => $itemtype)
            );
            // Cache the object, useful when doing imports.
            $static_object = $object;
        }

        if (!empty($group_prefixes)) {
            $fields = array_keys($object->properties);
            $group_prefixes = explode(',', $group_prefixes);
            $field_groups = array();
            foreach ($group_prefixes as $group_prefix) {
                foreach($fields as $field) {
                    if (preg_match('/^'.$group_prefix.'_.+/', $field)) {
                        list($part1, $part2) = explode('_', $field, 2);
                        $field_groups[$field] = array('prefix' => $group_prefix, 'suffix' => $part2);
                    }
                }
            }
        }

        // Get an array of possible flag values.
        $flag_values= array();
        if (!empty($object->properties['flags']->options)) {
            // The flags are stored as an array of (id,name) arrays.
            foreach($object->properties['flags']->options as $flag_value) {
                $flag_values[$flag_value['id']] = $flag_value['name'];
            }
        }

        // Move the events to the result array. While doing this:
        // - Do a security check
        // - Add a reference to the calendar details
        if (!empty($events)) {
            $position = 1;
            foreach ($events as $event) {
                // Security check.
                // We are not doing checks on category here, as we only want to use categories
                // for an organisational convenience.
                if (!xarSecurityCheck('OverviewIEvent', 0, 'IEvent', $event['calendar_id'] . ':' .$event['eid']. ':' . $event['created_by'])) break;

                // Some elements should be cast to integers.
                // The DD fetch will eventually be able to work this kind of thing out for itself.
                foreach(array('eid', 'calendar_id', 'created_time', 'updated_time', 'created_by', 'updated_by', 'startdate', 'enddate') as $cast) {
                    if (isset($event[$cast]) && is_numeric($event[$cast])) $event[$cast] = (int)$event[$cast];
                }

                // Expand the flags (array of 'flag'=>'name' pairs) - just for convenience
                if (isset($event['flags'])) {
                    $flags_arr = explode(',', $event['flags']);
                    if (!empty($flags_arr)) {
                        $flags_arr = array_flip($flags_arr);
                        foreach($flags_arr as $key => $value) {
                            $flags_arr[$key] = isset($flag_values[$key]) ? $flag_values[$key] : $key;
                        }
                    }
                    $event['flags_arr'] = $flags_arr;
                }

                // Include the duration, in days, as it is used a lot.
                // Set the duration to zero if the event is open-ended.
                // This value is 1-based, so a timed event on a single day is counted as one day duration.
                if (!isset($event['enddate'])) {
                    $event['duration_days'] = 0;
                } else {
                    // Find the days between two dates.
                    list($s_year, $s_month, $s_day) = explode('-', date('Y-m-d', $event['startdate']));
                    list($e_year, $e_month, $e_day) = explode('-', date('Y-m-d', $event['enddate']));
                    $s_jd = gregoriantojd($s_month, $s_day, $s_year);
                    $e_jd = gregoriantojd($e_month, $e_day, $e_year);
                    $event['duration_days'] = $e_jd - $s_jd + 1;
                }

                // Group various fields together.
                if (!empty($field_groups)) {
                    foreach($field_groups as $field => $field_group) {
                        if (!isset($event[$field_group['prefix']])) $event[$field_group['prefix']] = array();
                        // Note the '&' reference, in case there are further transforms on the values of these fields.
                        if (isset($event[$field]) && $event[$field] != '') $event[$field_group['prefix']][$field_group['suffix']] =& $event[$field];
                    }
                }

                // Add the position in the list.
                $event['position'] = $position;

                // Format the address.
                $event['address_formatted'] = xarModAPIfunc(
                    'ievents', 'user', 'format_address',
                    array('event' => $event, 'format' => $address_format)
                );

                // The complete [updated] event is then added to the result array.
                $return[$event['eid']] = $event;
                $position += 1;
            }

            // Get all the category linkages
            $catids = xarModAPIFunc('categories', 'user', 'getlinks',
                array('iids' => array_keys($events), 'reverse' => 1, 'modid' => $modid)
            );

            // Distribute the categories over the events array.
            // TODO: summarise the categories in various groupings.
            if (!empty($catids)) {
                // catid is actually an array.
                foreach ($catids as $key => $catid) $return[$key]['catids'] = $catid;
            }
        }
    }

    return $return;
}

?>
