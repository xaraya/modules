<?php
// Code for underline text
function bbcode_userapi_overline($args)
{
    extract($args);

    $message = preg_replace("/\[o\](.*?)\[\/o\]/si", xarTplModule('bbcode','user', 'overline', array('replace' => '\\1')), $message);

    return $message;
}
?>