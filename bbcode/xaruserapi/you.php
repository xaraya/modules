<?php
// Code for underline text
function bbcode_userapi_you($args)
{
    extract($args);

    $message = preg_replace("/\[you\]/si", xarTplModule('bbcode','user', 'you'), $message);

    return $message;
}
?>