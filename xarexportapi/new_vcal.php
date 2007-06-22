<?php

/**
 * iCal export handler.
 * Extends the master handler with various details and methods.
 */

include_once(dirname(__FILE__) . '/new_ical.php');

function ievents_exportapi_new_vcal($args)
{
    return new ievents_exportapi_handler_vcal($args);
}

class ievents_exportapi_handler_vcal extends ievents_exportapi_handler_ical
{
    // Definitions (generally read-only).
    var $content_type = 'text/vcalendar';
    var $file_extension = 'vcs';
    var $cal_version = '1.0';

    var $type = 'vcal';

    // Constructor
    function ievents_exportapi_handler_vcal($args)
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
        );

        $footer = array(
            $this->format_line('END', 'VCALENDAR'),
        );

        return implode($this->line_ending, $header) . $this->line_ending
            . $content
            . implode($this->line_ending, $footer) . $this->line_ending;
    }
}

?>