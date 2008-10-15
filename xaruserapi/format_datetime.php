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
    if (!xarVarValidate('int', $startdate, true)) $startdate = NULL;
    if (!xarVarValidate('int', $enddate, true)) $enddate = NULL;

    // TODO: The templates to be defined centrally.
    $ml_single_date = '#(1)';
    $ml_startend_date = '#(1) to #(2)';
    $ml_from_date = '#(1) to TBC';

    $ml_unknown_time = '';
    $ml_allday = 'all day';
    $ml_start_time = 'starts #(1)';
    $ml_startend_time = 'starts #(1) ends #(2)';
    $ml_startend_time_dur = '#(1) to #(2) (duration #(3))';

    $short_date = '';
    $medium_date = '';
    $long_date = '';
    $short_time = '';
    $medium_time = '';
    $long_time = '';

    // Is event open-ended (no end date?)
    if (!isset($enddate)) {
        // Event is open-ended
        $short_date = xarML($ml_from_date, xarLocaleGetFormattedDate('short', $startdate));
        $medium_date = xarML($ml_from_date, xarLocaleGetFormattedDate('medium', $startdate));
        $long_date = xarML($ml_from_date, xarLocaleGetFormattedDate('long', $startdate));

        if ($all_day == 'T') {
            // Just a single start date.
            // All-day times are dealt with below.
            // TODO: don't display any time if in summary page

            $short_time = xarML($ml_start_time, xarLocaleGetFormattedTime('short', $startdate));
            $medium_time = xarML($ml_start_time, xarLocaleGetFormattedTime('medium', $startdate));
            $long_time = xarML($ml_start_time, xarLocaleGetFormattedTime('long', $startdate));
        }
    } elseif (date('Ymd', $startdate) == date('Ymd', $enddate)) {
        // Same day
        $short_date = xarML($ml_single_date, xarLocaleGetFormattedDate('short', $startdate));
        $medium_date = xarML($ml_single_date, xarLocaleGetFormattedDate('medium', $startdate));
        $long_date = xarML($ml_single_date, xarLocaleGetFormattedDate('long', $startdate));

        if ($all_day == 'T') {
            // Timed event - show the times.
            if ($enddate == $startdate) {
                // Duration is zero - don't have an effective end date
                $short_time = xarML($ml_start_time, xarLocaleGetFormattedTime('short', $startdate));
                $medium_time = xarML($ml_start_time, xarLocaleGetFormattedTime('medium', $startdate));
                $long_time = xarML($ml_start_time, xarLocaleGetFormattedTime('long', $startdate));
            } else {
                // Non-zero duration
                // Calculate the duration.
                $duration = ievents_userapi_format_datetime_duration($startdate, $enddate);

                $short_time = xarML($ml_startend_time_dur, xarLocaleGetFormattedTime('short', $startdate), xarLocaleGetFormattedTime('short', $enddate), $duration);
                $medium_time = xarML($ml_startend_time_dur, xarLocaleGetFormattedTime('medium', $startdate), xarLocaleGetFormattedTime('medium', $enddate), $duration);
                $long_time = xarML($ml_startend_time_dur, xarLocaleGetFormattedTime('long', $startdate), xarLocaleGetFormattedTime('long', $enddate), $duration);
            }
        }
    } else {
        // Multiple-day
        $short_date = xarML($ml_startend_date, xarLocaleGetFormattedDate('short', $startdate), xarLocaleGetFormattedDate('short', $enddate));
        $medium_date = xarML($ml_startend_date, xarLocaleGetFormattedDate('medium', $startdate), xarLocaleGetFormattedDate('medium', $enddate));
        $long_date = xarML($ml_startend_date, xarLocaleGetFormattedDate('long', $startdate), xarLocaleGetFormattedDate('long', $enddate));

        if ($all_day == 'T') {
            // Just a single start date.
            // All-day times are dealt with below.
            // TODO: don't display if in summary page

            // TODO: don't display any time if in summary page
            $short_time = xarML($ml_startend_time, xarLocaleGetFormattedTime('short', $startdate), xarLocaleGetFormattedTime('short', $enddate));
            $medium_time = xarML($ml_startend_time, xarLocaleGetFormattedTime('medium', $startdate), xarLocaleGetFormattedTime('medium', $enddate));
            $long_time = xarML($ml_startend_time, xarLocaleGetFormattedTime('long', $startdate), xarLocaleGetFormattedTime('long', $enddate));
        }
    }

    // Time is 'all day' if the flag is set.
    if ($all_day == 'A') {
        $short_time = xarML($ml_allday);
        $medium_time = xarML($ml_allday);
        $long_time = xarML($ml_allday);
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
