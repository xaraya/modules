<?php

/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 * @param $params array containing the elements of PATH_INFO
 * @returns array
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 */
function chat_userapi_decode_shorturl($params)
{
    $args = array();
    $module = 'chat';
    return array('main', $args);
    // default : return nothing -> no short URL
    // (e.g. for multiple category selections)
}
?>