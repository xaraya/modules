<?php
// Code for underline text
function bbcode_userapi_image($args)
{
    // Always have to extract the args
    extract($args);

    // This is the part that you add for new bbcode.  Basically, search for the correct tag, then load the template for the tag, then replace what is found.
    $message = preg_replace("#\[img\](http://)?(.*?)\[/img\]#si", xarTplModule('bbcode','user', 'image', array('replace' => '\\1')), $message);

    // Always have to return the message.
    return $message;
}
?>