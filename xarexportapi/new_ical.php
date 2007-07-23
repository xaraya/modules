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
    var $mime_type = 'text/calendar';
    var $file_extension = 'ics';
    var $cal_version = '2.0';
    var $max_line_length = 75;

    var $type = 'ical';

    // TODO: get the module version into here
    var $product_id = '-//XarayaIEvents-UnknownVersion';

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
            $this->format_line('BEGIN', 'VCALENDAR'),
            $this->format_line('PRODID', $this->product_id),
            $this->format_line('VERSION', $this->cal_version),
            $this->format_line('METHOD', 'PUBLISH'),
        );

        $footer = array(
            $this->format_line('END', 'VCALENDAR'),
        );

        return implode($this->line_ending, $header) . $this->line_ending
            . $content
            . implode($this->line_ending, $footer) . $this->line_ending;
    }

    // Format a single iCal line.
    // We do not deal with encoding or wrapping or line endings in any way here,
    // we just want to put the fields together, with some escaping
    // of certain characters where required.
    // The format of a line is:
    //  name *(";" param ) ":" value
    // Parameters come in name/value pairs, where the value could be a string or
    // a list of strings (passed as an array).
    // Returns a string, or possibly an array of strings for line types that do not
    // support multiple values (and an array of values is passed in)
    function format_line($name, $value, $params = array())
    {
        // TODO: if the value is an array, and the name is one that does not
        // support mulitple values, then return multiple lines through
        // recursive calling.

        // Start with the name, assuming that it contains nothing that requires
        // any encoding.
        $line = strtoupper($name);

        if (!empty($params)) {
            foreach($params as $pname => $pvalue) {
                // Parameter names are case-insensitive, so make it upper-case
                // for consistency.
                $pname = strtoupper($pname);

                $line .= ';' . $pname . '=';

                if (is_string($pvalue)) {
                    // Just a single string value.
                    $p_value = $this->escape_parameter($pvalue, $pname);

                    // Plug the single parameter value into the line
                    $line .= $pvalue;
                } elseif (is_array($pvalue)) {
                    // Value is an array.
                    $plist = array();
                    foreach($pvalue as $plistkey => $plistvalue) {
                        $pvalue[$plistkey] = $this->escape_parameter($pvalue[$plistkey], $pname);
                    }

                    // Implode the paramater values into a comma-separated list
                    $line .= implode(',', $pvalue);
                }

            }
        }

        // Set the value.
        // TODO: look at various ways of escaping it.
        // TODO: can the value also be a list?
        // The answer to that one is yes: either through multiple lines or comma-separating the values.
        // If we get this far, then assume we are comma-separating the values.
        // TODO: different escaping for values: they don't need quoting, and some chars don't need escaping.
        if (is_string($value)) {
            $line .= ':' . $this->escape_value($value);
        } elseif (is_array($value)) {
            foreach($value as $valuekey => $valuevalue) {
                $value[$valuekey] = $this->escape_value($valuevalue);
            }
            $line .= ':' . implode(',', $value);
        }

        return $line;
    }

    // Escape a single value string.
    // These are simpler than parameters
    // TODO: check whether commas need escaping *only* for certain types of lines.
    function escape_value($value, $name = '')
    {
        $value = str_replace(
            array(',', "\n"),
            array('\\,', '\\n'),
            $value
        );

        return $value;
    }

    // Escape a single parameter value.
    // TODO: check whether escaping in a quoted string is different to escaping in
    // a non-quoted string, e.g. commas may not need escaping when in a quoted string.
    function escape_parameter($pvalue, $pname = '')
    {
        // Escape special characters in the string
        // ESCAPED-CHAR = ("\\" / "\;" / "\," / "\N" / "\n")
        $pvalue = str_replace(
            array('\\', ';', ',', "\n"),
            array('\\\\', '\\;', '\\,', '\\n'),
            $pvalue
        );

        // If the string contains lower-case characters, then we will
        // quote it to preserve the case.
        // TODO: There are also specific parameter names that are always quoted.
        // TOOD: Some parameter names (e.g. LANGUAGE) do not need quoting since they are
        // treated as case-insensitive anyway, e.g. us-EN, US-EN and Us-En are equivalent.
        if (preg_match('/[a-z:]/', $pvalue)) {
            $pvalue = '"' . str_replace('"', '\\"', $pvalue) . '"';
        }

        return $pvalue;
    }

    // Format a single event entry.
    // Looping over events is handled externally.
    function format_event($args)
    {
        $header = array($this->format_line('BEGIN', 'VEVENT'));
        $footer = array($this->format_line('END', 'VEVENT'));
        $lines = array();

        extract($args);

        // The 'title' is the summary.
        if (!xarVarValidate('str:1', $title, true)) $title = xarML('Unknown');

        if ($this->type == 'ical') {
            $lines[] = $this->format_line('SUMMARY', $title);
        } elseif ($this->type == 'vcal') {
            $lines[] = $this->format_line('SUMMARY', $this->quoted_printable_encode($title), array('ENCODING' => 'QUOTED-PRINTABLE'));
        }

        // The description is everything else we have - the summary and the main description.
        // We may just stick with the summary for now.
        // We need to ensure it consists of text only (not HTML).
        if (!xarVarValidate('str:1:', $summary, true)) $summary = '';
        if (!xarVarValidate('str:1:', $description, true)) $description = '';
        if ($this->type == 'ical') {
            $lines[] = $this->format_line('DESCRIPTION', xarModAPIfunc('ievents', 'user', 'transform', array('text' =>$summary)));
        } elseif ($this->type == 'vcal') {
            $lines[] = $this->format_line('DESCRIPTION',
                $this->quoted_printable_encode(xarModAPIfunc('ievents', 'user', 'transform', array('text' =>$summary))),
                array('ENCODING' => 'QUOTED-PRINTABLE')
            );
        }

        // Only public events will be listed
        $lines[] = $this->format_line('CLASS', 'PUBLIC');

        // Dates and times.
        // Untimed event: DTSTART;VALUE=DATE:Ymd and DURATION:P1D and DTEND;VALUE=DATE:Ymd (24 hours later)
        // Timed event: DTSTART:utc-timestamp and DURATION:PTnM (where n=duration in minutes???) and DTEND:utc-timestamp
        // For all: DTSTAMP:last-updated-utc-timestamp

        // The all_day flag is either 'A' or 'T'.
        if (!isset($all_day)) $all_day = 'A';

        if ($all_day == 'A' || !isset($enddate) || date('Ymd', $startdate) != date('Ymd', $enddate)) {
            // All day event (or no end date, or start/end dates more than one day apart).
            // Q: what timezone is this? A: it is just a date, so the timezone is irrelevant.
            if ($this->type == 'ical') {
                $lines[] = $this->format_line('DTSTART', date('Ymd', $startdate), array('VALUE' => 'DATE'));
            } elseif ($this->type == 'vcal') {
                $lines[] = $this->format_line('DTSTART', date('Ymd', $startdate));
            }

            // If we have no end date, then assume it will end in 24 hours?
            // CHECKME: can we just leave the end date open? [answer: yes, we should]
            // Some research done here here: https://bugs.launchpad.net/schooltool/+bug/80161
            if (!isset($enddate)) {
                if ($this->type == 'ical') {
                    // No DTEND for a single day all-day event.
                    //$lines[] = $this->format_line('DTEND', date('Ymd', $startdate + (3600*24)), array('VALUE' => 'DATE'));
                } elseif ($this->type == 'vcal') {
                    // CHECKME: should vCal be treated the same way?
                    //$lines[] = $this->format_line('DTEND', date('Ymd', $startdate + (3600*24)));
                }
                $duration_days = 1;
            } else {
                // Find duration, in days.
                if ($enddate == $startdate) {
                    $duration_days = 1;
                } else {
                    $duration_days = ceil(($enddate - $startdate) / 86400);
                }
                if ($this->type == 'ical' && $duration_days > 1) {
                    // No end date if the duration is one day, since the DTEND *must* be *later*
                    // than the DTSTART according to the specs (so they cannot be the same, and some
                    // calendars fail to import the event if they are).
                    $lines[] = $this->format_line('DTEND', date('Ymd', $enddate), array('VALUE' => 'DATE'));
                } elseif ($this->type == 'vcal' && $duration_days > 1) {
                    $lines[] = $this->format_line('DTEND', date('Ymd', $enddate));
                }
            }

            $lines[] = $this->format_line('DURATION', "P${duration_days}D");
        } else {
            // Quantise the times (to nearest block of minutes)
            $startdate = xarModAPIfunc('ievents', 'user', 'quantise', array('time' => $startdate));
            $enddate = xarModAPIfunc('ievents', 'user', 'quantise', array('time' => $enddate));

            // Timed event, lasting one day or less.
            $lines[] = $this->format_line('DTSTART', $this->utc_datetime($startdate));
            $lines[] = $this->format_line('DTEND', $this->utc_datetime($enddate));

            // TODO: use relevant time components, e.g. 'PTnDmHxM'
            $duration_minutes = (int)(date('G', $enddate)*60 + date('i', $enddate) - date('G', $startdate)*60 - date('i', $startdate));
            $lines[] = $this->format_line('DURATION', "PT${duration_minutes}M");
        }

        if (isset($updated_time)) {
            $lines[] = $this->format_line('DTSTAMP', $this->utc_datetime($updated_time));
        }

        // Send the contact.
        $contact_arr = array();
        if (!empty($contact_name)) $contact_arr[] = xarML('Name: #(1)', $contact_name);
        if (!empty($contact_email)) $contact_arr[] = xarML('E-mail: #(1)', $contact_email);
        if (!empty($contact_phone)) $contact_arr[] = xarML('Phone: #(1)', $contact_phone);
        if (!empty($contact_arr)) {
            $lines[] = $this->format_line('CONTACT', implode('; ', $contact_arr));
        }

        // Send the location.
        $location_arr = array();
        if (!empty($location_venue)) $location_arr[] = xarML('Venue: #(1)', $location_venue);
        if (!empty($location_address)) $location_arr[] = xarML('#(1)', preg_replace('/[ ,]*[\r\n]+/', ', ', $location_address));
        if (!empty($location_postcode)) $location_arr[] = xarML('#(1)', $location_postcode);
        if (!empty($location_country)) $location_arr[] = xarML('#(1)', $location_country);
        if (!empty($location_arr)) {
            $lines[] = $this->format_line('LOCATION', implode('; ', $location_arr));
        }

        // The URL back to this event.
        $lines[] = $this->format_line('URL', xarModURL('ievents', 'user', 'view', array('eid' => $eid), false));

        // Unique ID
        $lines[] = $this->format_line('UID', $this->utc_datetime($startdate) . '-' . $eid . '@' . getenv('SERVER_NAME'));

        // Categories
        if (!empty($catids)) {
            $cat_arr = array();
            foreach($catids as $catid) {
                if (isset($this->categories['flatlist'][$catid])) {
                    $cat_arr[] = $this->categories['flatlist'][$catid]['name'];
                }
            }
            $lines[] = $this->format_line('CATEGORIES', $cat_arr);
        }

        // Also to include:
        // ORGANIZER;CN="John Smith":MAILTO:jsmith@host.com
        // status

        // TODO: fold the lines and ensure it is properly content-encoded.
        // Line length of chars without the line terminators.
        $line_length = $this->max_line_length - strlen($this->line_ending);
        // Just fold the lines, assuming the header and footer are going to
        // be short enough already.
        // TODO: lines must be folded between any two CHARACTERS, but we are
        // going to fold at BYTE boundaries. This may inadvertently split a
        // UTF-8 character in internally.
        // Similarly we need to avoid splitting quoted-printable sequences.
        // CHECK: there should be an RE that will split a string up into UTF-8
        // and quoted-printable characters.
        foreach($lines as $linekey => $line) {
            if (strlen($line) <= $line_length) continue;

            // Line needs folding.
            $lines[$linekey] = wordwrap($line, $line_length, $this->line_ending . ' ', true);
        }

        return implode($this->line_ending, $header) . $this->line_ending
            . implode($this->line_ending, $lines) . $this->line_ending
            . implode($this->line_ending, $footer) . $this->line_ending;
    }
}

?>