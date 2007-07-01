<?php

/**
 * API to provide export functions for events, into various formats.
 */

// Return a new export object.
function ievents_exportapi_new_export($args)
{
    $object = new ievents_exportapi_export_master($args);
    return $object;
}

/**
 * Master class as a shared entry point.
 */
class ievents_exportapi_export_master
{
    // The export handler.
    // This is instantiated from a selection of handlers.
    var $handler = NULL;
    var $handler_name = NULL;

    // The supported handlers
    var $handlers = array(
        'iCal' => array('short' => 'iCal', 'long' => 'iCalendar', 'class' => 'ical', 'extension' => 'ics'),
        'vCal' => array('short' => 'vCal', 'long' => 'vCalendar', 'class' => 'vcal', 'extension' => 'vcs'),
        //'pilot_csv' => array('short' => 'Pilot CSV', 'long' => 'Palm Pilot CSV', 'class' => 'pilotcsv', 'extension' => 'csv'),
        //'pilot_text' => array('short' => 'Pilot CSV', 'long' => 'Palm Pilot text', 'class' => 'pilottext', 'extension' => 'txt'),
    );

    // Output modifiers.
    // TODO: use the site encoding by default (for both in and out), but provide translation if required.
    var $output_encoding = 'utf8';
    var $input_encoding = 'utf8';

    // TODO: these two needed, or leave in the handler?
    var $line_ending = "\r\n";
    var $content_type = '';

    // Error and status conditions
    var $error_message = '';
    var $error_code = 0;

    // TODO: get the module version into here
    var $product_id = '-//XarayaIEvents-UnknownVersion';

    // Constructor
    function ievents_exportapi_export_master($args)
    {
        // If the RSS theme is available, then add it to the list of handlers
        if (xarThemeIsAvailable('rss')) {
            $this->handlers += array('rss' => array('short' => 'RSS', 'long' => 'RSS', 'class' => '', 'extension' => 'xml'));
        }

        
        return $this;
    }

    // Initiailise a handler.
    function set_handler($handler_name, $args = array())
    {
        if (!isset($this->handlers[$handler_name])) {
            return $this->set_error(xarML('Export format "#(1)" is not supported', $handler_name));
        }

        // Attempt to load the handler class.
        // Suppress errors in case it does not exist.
        $class_name = $this->handlers[$handler_name]['class'];
        $this->handler = xarModAPIfunc('ievents', 'export', 'new_' . $class_name, $args, false);

        if (empty($this->handler)) {
            return $this->set_error(xarML('Handler class "#(1)" could not be loaded', $class_name));
        }

        // Set some default values.
        $this->handler_name = $handler_name;
        $this->handler->file_extension = $this->handlers[$handler_name]['extension'];
        $this->handler->product_id = $this->product_id;
        $this->handler->input_encoding = $this->input_encoding;
        $this->handler->output_encoding = $this->output_encoding;
        $this->handler->categories = xarModAPIfunc('ievents', 'user', 'getallcategories');

        // Success.
        return true;
    }

    // Return the calendar file as a single string.
    // Pass in $events (already extracted)
    // TODO: check data and that handler has been loaded.
    function get_export($events)
    {
        $result = array();

        foreach($events as $event) {
            $result[] = $this->handler->format_event($event);
        }

        // Each line of each formatted event is already terminated, so just
        // implode then end-to-end.
        return $this->handler->wrap_export(implode('', $result));
    }

    // Stream the export to the output and exit.
    // TODO: check we have events and data is valid.
    // TODO: redirect to an error page if we encounter problems (check before sending the headers).
    function stream_export($events)
    {
        // Send the headers.
        $file = $this->handler_name . '.' . $this->handler->file_extension;

        header ('Content-Type: ' . $this->handler->content_type);
        header ('Content-Disposition: attachment; filename="' . $file .  '"');
        header ('Pragma: no-cache');
        header ('Cache-Control: no-cache');

        echo $this->get_export($events);

        exit(0);
    }

    // Set an error condition.
    function set_error($message, $error_code = 1)
    {
        $this->error_message = $message;
        $this->error_code = $error_code;

        return false;
    }
}

// Handler master class
class ievents_exportapi_handler_master
{
    // Error codes.
    var $error_message = '';
    var $error_code = 0;
    var $product_id = '';

    var $line_ending = "\r\n";
    var $content_type = 'text/calendar';
    var $file_extension = 'txt';

    var $output_encoding = 'utf8';
    var $input_encoding = 'utf8';

    var $file_extension = 'txt';

    var $categories = array();

    // Constructor.
    function ievents_exportapi_handler_master($args)
    {
        return true;
    }

    // Set an error condition.
    function set_error($message, $error_code = 1)
    {
        $this->error_message = $message;
        $this->error_code = $error_code;

        return false;
    }

    // Wrap up an export with a header and footer.
    // The content is assumed to be fully formatted.
    function wrap_export($content)
    {
        return $content;
    }

    // A variety of helper methods.
    // Many are cribbed from WebCalendar (http://www.k5n.us/webcalendar.php) - with many thanks

    // Convert a single byte to quoted-printable
    function quoted_printable_encode_char($char)
    {
        $result = '';

        if ((ord($char) >= 33 && ord($char) <= 60) || (ord($char) >= 62 && ord($char) <= 126) || 
      ord($char) == 9 || ord($char) == 32) {
            $result = $char;
        } else {
            $result = sprintf("=%02X", ord($char));
        }

        return $result;
    }


    // Fold lines at some maximum size.
    // Different folding methods are supported: ical
    // $line can be a string or an array of strings.
    function fold_lines($line, $maxlen = 0, $method = 'ical')
    {
        // TODO
    }

    // Format a local datetime in utc string format (iCal and vCal)
    function utc_datetime($datetime)
    {
        $utc_date = gmdate('Ymd', $datetime);
        $utc_hour = gmdate('His', $datetime);

        return sprintf ('%sT%sZ', $utc_date, $utc_hour);
    }

    // Transform to quoted-printable.
    // This does not fold lines - that is dealt with separately.
    function quoted_printable_encode($text)
    {
        // TODO: if there is nothing in the string to encode, then return
        // straight away to save time.
        $return = '';

        $len = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            $return .= $this->quoted_printable_encode_char($text[$i]);
        }

        return $return;
    }
}

?>