<?php
// Code for underline text
function bbcode_userapi_underline($args)
{
    extract($args);

    $message = preg_replace("/\[u\](.*?)\[\/u\]/si", xarTplModule('bbcode','user', 'underline', array('replace' => '\\1')), $message);

    return $message;
}
?>