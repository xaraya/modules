<?php

/**
 * Format the date and time for an event.
 *
 * Either the whole event can be passed in, or the components
 * that are required to format the date and time.
 * An array with various useful formats will be returned, and
 * can be used in templates as required.
 *
 * @param event array Whole event passed in.
 * @param startdate integer Event start timestamp, in unix timestamp format
 * @param enddate integer Event end timestamp, in unix timestamp format
 * @param all_day string The 'all day' flag, either 'A' or 'T'
 *
 * @param Support custom formats too.
 */

function ievents_userapi_format_datetime($args)
{
    extract($args);

    if (!empty($event)) {
        // A whole event was passed in.
        // Extract the stuff we need.
        if (isset($event['all_day'])) $all_day = $event['all_day'];
        if (isset($event['startdate'])) $startdate = $event['startdate'];
        if (isset($event['enddate'])) $enddate = $event['enddate'];
    }

    if (!xarVarValidate('enum:A:T', $all_day, true)) $all_day = 'A';

    /*
     * FIXME: if $startdate or $enddate are not set in form during creation,
     * they are set to -900 (at least in mysql and postgres).
     * It's not possible to use dates before 01-01-1970 in current xarLocaleGetFormattedTime implementation,
     * so we don't have to worry about conflict with user wanting date
     * Wed, 31 Dec 1969 23:45:00 GMT which matches -900.
     */
    if (!xarVarValidate('int', $startdate, true) || $startdate == -900) $startdate = NULL;
    if (!xarVarValidate('int', $enddate, true) || $enddate == -900) $enddate = NULL;

    // TODO: The templates to be defined centrally.
    $ml_allday = xarML('all day');

    // all these variables were NOT localized. That's why it's duplicated. TODO: do it better
    /*
    $ml_single_date = '#(1)'; // useless
    $ml_startend_date = '#(1) to #(2)';
    $ml_from_date = '#(1) to TBC';
    $ml_unknown_time = ''; // used nowhere
    $ml_start_time = 'starts #(1)';
    $ml_startend_time = 'starts #(1) ends #(2)';
    $ml_startend_time_dur = '#(1) to #(2) (duration #(3))';
     */

    $short_date = '';
    $medium_date = '';
    $long_date = '';
    $short_time = '';
    $medium_time = '';
    $long_time = '';

    // NOTE: suppose we don't want use $startdate == NULL as today (as how it is in current xarLocaleGetFormattedTime)
    // TODO: Do not allow to create event without $startdate (if it's not wanted feature)
    if (!isset($startdate)) {
        $short_date = $medium_date = $long_date = xarML('Date not set'); // TODO: put it in some "error" css class or don't suppress error warning during validation
    } elseif (!isset($enddate) || date('Ymd', $startdate) > date('Ymd', $enddate)) {
        // Event is open-ended
        $short_date = xarML('#(1) to TBC', xarLocaleGetFormattedDate('short', $startdate));
        $medium_date = xarML('#(1) to TBC', xarLocaleGetFormattedDate('medium', $startdate));
        $long_date = xarML('#(1) to TBC', xarLocaleGetFormattedDate('long', $startdate));

        if ($all_day == 'T') {
            // Just a single start date.
            // All-day times are dealt with below.
            // TODO: don't display any time if in summary page

            $short_time = xarML('starts #(1)', xarLocaleGetFormattedTime('short', $startdate));
            $medium_time = xarML('starts #(1)', xarLocaleGetFormattedTime('medium', $startdate));
            $long_time = xarML('starts #(1)', xarLocaleGetFormattedTime('long', $startdate));
        }
    } elseif (date('Ymd', $startdate) == date('Ymd', $enddate)) {
        // Same day
        $short_date = xarML('#(1)', xarLocaleGetFormattedDate('short', $startdate));
        $medium_date = xarML('#(1)', xarLocaleGetFormattedDate('medium', $startdate));
        $long_date = xarML('#(1)', xarLocaleGetFormattedDate('long', $startdate));

        if ($all_day == 'T') {
            // Timed event - show the times.
            if ($enddate <= $startdate) {
                // Duration is zero or negative - don't have an effective end date
                $short_time = xarML('starts #(1)', xarLocaleGetFormattedTime('short', $startdate));
                $medium_time = xarML('starts #(1)', xarLocaleGetFormattedTime('medium', $startdate));
                $long_time = xarML('starts #(1)', xarLocaleGetFormattedTime('long', $startdate));
            } else {
                // Non-zero duration
                // Calculate the duration.
                $duration = ievents_userapi_format_datetime_duration($startdate, $enddate);

                $short_time = xarML('#(1) to #(2) (duration #(3))', xarLocaleGetFormattedTime('short', $startdate), xarLocaleGetFormattedTime('short', $enddate), $duration);
                $medium_time = xarML('#(1) to #(2) (duration #(3))', xarLocaleGetFormattedTime('medium', $startdate), xarLocaleGetFormattedTime('medium', $enddate), $duration);
                $long_time = xarML('#(1) to #(2) (duration #(3))', xarLocaleGetFormattedTime('long', $startdate), xarLocaleGetFormattedTime('long', $enddate), $duration);
            }
        }
    } else {
        // Multiple-day
        $short_date = xarML('#(1) to #(2)', xarLocaleGetFormattedDate('short', $startdate), xarLocaleGetFormattedDate('short', $enddate));
        $medium_date = xarML('#(1) to #(2)', xarLocaleGetFormattedDate('medium', $startdate), xarLocaleGetFormattedDate('medium', $enddate));
        $long_date = xarML('#(1) to #(2)', xarLocaleGetFormattedDate('long', $startdate), xarLocaleGetFormattedDate('long', $enddate));

        if ($all_day == 'T') {
            // Just a single start date.
            // All-day times are dealt with below.
            // TODO: don't display if in summary page

            // TODO: don't display any time if in summary page
            $short_time = xarML('starts #(1) ends #(2)', xarLocaleGetFormattedTime('short', $startdate), xarLocaleGetFormattedTime('short', $enddate));
            $medium_time = xarML('starts #(1) ends #(2)', xarLocaleGetFormattedTime('medium', $startdate), xarLocaleGetFormattedTime('medium', $enddate));
            $long_time = xarML('starts #(1) ends #(2)', xarLocaleGetFormattedTime('long', $startdate), xarLocaleGetFormattedTime('long', $enddate));
        }
    }

    // Time is 'all day' if the flag is set.
    if ($all_day == 'A' && isset($startdate)) {
        $short_time = $medium_time = $long_time = $ml_allday;
    }

    
    $return = array(
        'short' => array(
            'date' => $short_date,
            'time' => $short_time,
        ),
        'medium' => array(
            'date' => $medium_date,
            'time' => $medium_time,
        ),
        'long' => array(
            'date' => $long_date,
            'time' => $long_time,
        ),
    );

    return $return;
}

// Format durations (only works within a day)

function ievents_userapi_format_datetime_duration($startdate, $enddate)
{
    // We quanitise this duration a little, perhaps to the nearest ten or fifteen minutes.

    // Quanta of the hour divisions, in minutes.
    // Set to zero for no quantisation.
    static $quanta = NULL;
    if (!isset($quanta)) $quanta = xarModGetVar('ievents', 'quanta');

    $total_minutes = (int)(date('G', $enddate)*60 + date('i', $enddate) - date('G', $startdate)*60 - date('i', $startdate));

    $duration_hours = floor($total_minutes / 60);

    if ($quanta > 0) {
        $duration_minutes = $quanta * floor(($total_minutes - ($duration_hours * 60)) / $quanta);
    }

    if ($duration_hours > 0) {
        if ($duration_minutes == 0) {
            if ($duration_hours == 1) {
                return xarML('#(1) hour', $duration_hours);
            } else {
                return xarML('#(1) hours', $duration_hours);
            }
        } else {
            if ($duration_hours == 1) {
                return xarML('#(1) hour #(2) minutes', $duration_hours, $duration_minutes);
            } else {
                return xarML('#(1) hours #(2) minutes', $duration_hours, $duration_minutes);
            }
        }
    } else {
        return xarML('#(1) minutes', $duration_minutes);
    }
}

?>
