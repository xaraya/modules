<?php

/**
 * Capture general person details and write them to a file.
 * Variations on this could add the captured data to the
 * args array, so that they could be used in the item template
 * e.g. 'Thankyou #$firstname#'
 * Use this function with a form that submits personal details as
 * listed in the xarVarFetch section. Expand the form and adapt as
 * needed.
 * This version writes to the 'var' directory under xarpages/details.txt
 * so ensure the directory exists and is writeable. It will write a header
 * row when the file is first created.
 * Further security checks could include limiting the size of the data file, and
 * suppressing multiple submissions from a single IP within a certain timeframe.
 */

function xarpages_funcapi_capture_person_file($args)
{
    // Do only some general trimming of the form data.
    $details = array();
    xarVarFetch('firstname', 'pre:trim:left:60:passthru:str:0', $details['firstname'], '', XARVAR_NOT_REQUIRED);
    xarVarFetch('surname', 'pre:trim:left:60:passthru:str:0', $details['surname'], '', XARVAR_NOT_REQUIRED);
    xarVarFetch('email', 'pre:trim:left:60:lower:passthru:email', $details['email'], '', XARVAR_NOT_REQUIRED);
    xarVarFetch('notifyme', 'pre:lower:passthru:enum:yes:no', $details['notifyme'], 'no', XARVAR_NOT_REQUIRED);

    // Grab a key to prevent multiple-submissions.
    // Will only really work if sessions are available.
    // Grab the unique key from the array before the dates and times are added.
    $unique = md5(implode('-', $details));

    // Check to see if this form has already been submitted.
    // This is an attempt to prevent multiple submits of the same form.
    $last_unique = xarSessionGetVar('xarpages.unique_submit');

    if (!empty($last_unique) && $last_unique == $unique) {
        // If the details are the same as the session-cached value
        // then return with no further processing.
        return;
    }

    xarSessionSetVar('xarpages.unique_submit', $unique);

    $details['date'] = date('d/m/Y');
    $details['time'] = date('H:i:s');
    $details['remote_address'] = $_SERVER['REMOTE_ADDR'];
    $details['http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];

    // Remove any '|'s or non-printable characters.
    foreach($details as $key => $value) {
        $value = str_replace(array('|', '"'), '', $value);
        $details[$key] = preg_replace(array('/[\\ca-\\cz\\x7f]/'), '', $value);
    }

    // Format the output.
    $header = implode('|', array_keys($details));
    $data = '"' . implode('"|"', array_values($details)) . '"';

    // Write to the data file.
    // Write to var/xarpages/details.txt
    // Don't write any errors - this will either work or not.
    // The directory needs to exist and be writable.
    $pathname = sys::varpath() . '/xarpages' . '/details.txt';

    if (!file_exists($pathname)) {
        // File does not exist - create it and write the header.
        $rs = @fopen($pathname, 'a');
        if (!empty($rs)) {
            @fwrite($rs, $header . "\n");
            @fclose($rs);
        }
    }

    if (is_writeable($pathname)) {
        // File is writeable - add the data line.
        $rs = @fopen($pathname, 'a');
        if (!empty($rs)) {
            @fwrite($rs, $data . "\n");
            @fclose($rs);
        }
    }
}

?>