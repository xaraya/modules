<?php

/**
 * return the path for a short URL to xarModURL for this module
 * @param $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function chat_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);
    // check if we have something to work with
    if (!isset($func)) {
        return;
    }
    // make sure you don't pass the following variables as arguments too
    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here (yet) !
    $module = 'chat';

    // specify some short URLs relevant to your module
    if ($func == 'main') {
        $path = '/' . $module . '/';
    }
    // anything else does not have a short URL equivalent
    return $path;
}
?>