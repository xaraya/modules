<?php

function xarpages_decodeapi_example($args)
{
    // This is a sample custom short URL decode function.
    // It will take whatever path components have not been
    // decoded to determine the page.
    // It should return any additional decoded GET parameters
    // as an array of name=>value pairs.

    // Example: if the next additonal path component is a number,
    // prefixed with the letter 'c' then treat it as a category.

    // The first entry is always the module name or alias.
    array_shift($args);

    // Initialise the array to return.
    $get = array();

    $arg = array_shift($args);

    if (preg_match('/c[0-9]+/', $arg)) {
        // Set GET parameter 'cid' to the numeric part of the category.
        $get['cid'] = substr($arg, 1);
        array_shift($args);
    }

    return $get;
}

?>