<?php
// Code for underline text
function bbcode_userapi_size($args)
{
    // Always have to extract the args
    extract($args);

    // This is the part that you add for new bbcode.  Basically, search for the correct tag, then load the template for the tag, then replace what is found.
    $message = preg_replace("/\[size\=([a-zA-Z0-9.-]+)\](.*?)\[\/size\]/si", xarTplModule('bbcode','user', 'size', array('replace1' => '\\1', 'replace2' => '\\2')), $message);

    // Always have to return the message.
    return $message;
}
?>