<?php

function xarpages_encodeapi_example($args)
{
    // Example encode function.
    // An array of name=value pairs will be passed in
    // for encoding.
    // The return value will be an array of path components
    // and unconsumed (unused) name=value pairs.
    // Example: if the value 'cid' is set to a numeric value, then
    // add it onto the path, prefixed with the letter 'c'.

    extract($args);

    // Initialise the return values.
    $path = array();
    $get = $args;

    // Check if the cid is set
    if (isset($cid) && is_numeric($cid)) {
        // Set the path.
        $path[] = 'c' . $cid;

        // 'consume' the cid GET parameter.
        unset($get['cid']);
    }

    return array(
        'path' => $path,
        'get' => $get
    );
}

?>