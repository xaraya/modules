<?php

/**
 * Format an address for an event according to a format string.
 *
 * @param event array The event (or some other array of items to format)
 * @param format string The format, expressed as a string of text and fields
 * @return string The formatted address
 */

function ievents_userapi_format_address($args)
{
    extract($args);

    $return_address = '';
    $newline = "\n";

    if (!empty($format)) $return_address = $format;

    // Two parameters are mandatory.
    if (empty($event) || empty($format)) return $return_address;

    // Get the fields in the format.
    preg_match_all('/{([a-zA-Z_0-9]+)}/', $format, $matches);

    // No fields present in the format string
    if (empty($matches[1])) return $return_address;

    // Get unique field names.
    $fields = array_unique($matches[1]);

    // Substitute each field in turn
    foreach($fields as $field) {
        // Skip over line breaks (optional 'LB' and mandatory 'NL')
        if ($field == 'LB' || $field == 'NL') continue;

        if (isset($event[$field]) && is_string($event[$field])) {
            $return_address = str_replace('{' . $field . '}', $event[$field], $return_address);
        } else {
            // Blank out unknown fields completely.
            $return_address = str_replace('{' . $field . '}', '', $return_address);
        }
    }

    // Collapse optional line breaks {LB}
    $return_address = preg_replace('/({LB})+/', '{LB}', $return_address);

    // Merge optional line breaks into mandatory line breaks, if they appear together
    $return_address = preg_replace('/({NL}{LB}|{LB}{NL})/', '{NL}', $return_address);

    // If the format does not start or end with a line break or newline, then
    // trim them off the formatted address.
    if (!preg_match('/{(LB|NL)}$/', $format)) $return_address = preg_replace('/({(LB|NL)})+$/', '', $return_address);
    if (!preg_match('/^{(LB|NL)}/', $format)) $return_address = preg_replace('/^({(LB|NL)})+/', '', $return_address);

    // Break the lines where required.
    $return_address = str_replace(array('{NL}', '{LB}'), $newline, $return_address);

    return $return_address;
}

?>