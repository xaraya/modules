<?php

function uploads_adminapi_dd_value_needs_conversion($value)
{
    // if the value is empty or it has a value starting with ';'
    // Then it doesn't need to be converted - so return false.
    if (empty($value) || (strlen($value) && ';' == $value{0})) {
        // conversion not needed
        return FALSE;
    } else {
        // conversion needed
        return TRUE;
    }
}

?>