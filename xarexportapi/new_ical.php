<?php

/**
 * iCal export handler.
 * Extends the master handler with various details and methods.
 */

function ievents_exportapi_new_ical($args)
{
    return new ievents_exportapi_handler_ical($args);
}

class ievents_exportapi_handler_ical extends ievents_exportapi_handler_master
{
    // Definitions (generally read-only).
    var $content_type = 'text/calendar';
    var $file_extension = 'ics';

    // Constructor
    function ievents_exportapi_handler_ical($args)
    {
        // Call up the parent constructor.
        parent::ievents_exportapi_handler_master($args);

        return $this;
    }

    // Wrap up an export with a header and footer.
    // The content is assumed to be fully formatted.
    function wrap_export($content)
    {
        $header = array(
            'BEGIN:VCALENDAR',
            'PRODID:' . $this->product_id,
            'VERSION:2.0',
            'METHOD:PUBLISH',
        );

        $footer = array(
            'END:VCALENDAR',
        );

        return implode($this->line_ending, $header)
            . $this->line_ending
            . $content
            . implode($this->line_ending, $footer)
            . $this->line_ending;
    }

    // Format a single event entry.
    // Looping over events is handled externally.
    function format_event($args)
    {
        $header = array('BEGIN:VEVENT');
        $footer = array('END:VEVENT');
        $lines = array();

        extract($args);

        // The 'title' is the summary.
        if (!xarVarValidate('str:1', $title, true)) $title = xarML('Unknown');
        // Checkme: 'quotedprintable' may be required.
        $lines[] = 'SUMMARY:' . parent::fold_lines($title, 'utf8', 76, 8);

        // The description is everything else we have - the summary and the main description.
        // We may just stick with the summary for now.
        // We need to ensure it consists of text only (not HTML).
        if (!xarVarValidate('str:1:', $summary, true)) $summary = '';
        if (!xarVarValidate('str:1:', $description, true)) $description = '';
        $lines[] = 'DESCRIPTION:' . parent::fold_lines(xarModAPIfunc('ievents', 'user', 'transform', array('text' =>$summary)), 'utf8', 76, 12);

        // Only public events will be listed
        $lines[] = 'CLASS:PUBLIC';

        // Dates and times.
        // Untimed event: DTSTART;VALUE=DATE:Ymd and DURATION:P1D and DTEND;VALUE=DATE:Ymd (24 hours later)
        // Timed event: DTSTART:utc-timestamp and DURATION:PTnM (where n=duration in minutes???) and DTEND:utc-timestamp
        // For all: DTSTAMP:last-updated-utc-timestamp

        // The all_day flag is either 'A' or 'T'.
        if (!isset($all_day)) $all_day = 'A';

        if ($all_day == 'A' || !isset($enddate) || date('Ymd', $startdate) != date('Ymd', $enddate)) {
            // All day event (or no end date, or start/end dates more than one day apart).
            // Q: what timezone is this? A: it is just a date, so the timezone is irrelevant.
            $lines[] = 'DTSTART;VALUE=DATE:' . date('Ymd', $startdate);

            // If we have no end date, then assume it will end in 24 hours?
            // CHECKME: can we just leave the end date open?
            if (!isset($enddate)) {
                $lines[] = 'DTEND;VALUE=DATE:' . date('Ymd', $startdate + (3600*24));
                $duration_days = 1;
            } else {
                // Find duration, in days.
                if ($enddate == $startdate) {
                    $duration_days = 1;
                } else {
                    $duration_days = ceil(($enddate - $startdate) / 86400);
                }
                $lines[] = 'DTEND;VALUE=DATE:' . date('Ymd', $enddate);
            }

            $lines[] = "DURATION:P${duration_days}D";
        } else {
            // Quantise the times (to nearest block of minutes)
            $startdate = xarModAPIfunc('ievents', 'user', 'quantise', array('time' => $startdate));
            $enddate = xarModAPIfunc('ievents', 'user', 'quantise', array('time' => $enddate));

            // Timed event, lasting one day or less.
            $lines[] = 'DTSTART:' . parent::utc_datetime($startdate);
            $lines[] = 'DTEND:' . parent::utc_datetime($enddate);

            // TODO: use relevant time components, e.g. 'PTnDmHxM'
            $duration_minutes = (int)(date('G', $enddate)*60 + date('i', $enddate) - date('G', $startdate)*60 - date('i', $startdate));
            $lines[] = "DURATION:PT${duration_minutes}M";
        }

        if (isset($updated_time)) {
            $lines[] = 'DTSTAMP:' . parent::utc_datetime($updated_time);;
        }

        // Send the contact.
        $contact_arr = array();
        if (!empty($contact_name)) $contact_arr[] = xarML('Name: #(1)', $contact_name);
        if (!empty($contact_email)) $contact_arr[] = xarML('E-mail: #(1)', $contact_email);
        if (!empty($contact_phone)) $contact_arr[] = xarML('Phone: #(1)', $contact_phone);
        if (!empty($contact_arr)) {
            $lines[] = 'CONTACT:' . parent::fold_lines(implode('; ', $contact_arr), 'utf8', 76, 8);
        }

        // Send the location.
        $location_arr = array();
        if (!empty($location_venue)) $location_arr[] = xarML('Venue: #(1)', $location_venue);
        if (!empty($location_address)) $location_arr[] = xarML('#(1)', preg_replace('/[ ,]*[\r\n]+/', ', ', $location_address));
        if (!empty($location_postcode)) $location_arr[] = xarML('#(1)', $location_postcode);
        if (!empty($location_country)) $location_arr[] = xarML('#(1)', $location_country);
        if (!empty($location_arr)) {
            $lines[] = 'LOCATION:' . parent::fold_lines(implode('; ', $location_arr), 'utf8', 76, 9);
        }

        // The URL back to this event.
        $lines[] = 'URL:' . xarModURL('ievents', 'user', 'view', array('eid' => $eid), false);

        // Unique ID
        $lines[] = 'UID:' . parent::utc_datetime($startdate) . '-' . $eid . '@' . getenv('SERVER_NAME');

        // Categories
        if (!empty($catids)) {
            $cat_arr = array();
            foreach($catids as $catid) {
                if (isset($this->categories['flatlist'][$catid])) {
                    $cat_arr[] = parent::fold_lines($this->categories['flatlist'][$catid]['name'], 'utf8', 200, 0);
                }
            }
            $lines[] = 'CATEGORIES:' . parent::fold_lines(implode(',', $cat_arr), 'none', 76, 0);
        }

        // Also to include:
        // ORGANIZER;CN="John Smith":MAILTO:jsmith@host.com
        // status

        return implode($this->line_ending, $header)
            . $this->line_ending
            . implode($this->line_ending, $lines)
            . $this->line_ending
            . implode($this->line_ending, $footer)
            . $this->line_ending;
    }
}

?>