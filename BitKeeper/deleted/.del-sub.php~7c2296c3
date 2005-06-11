<?php
function bbcode_userapi_sub($args)
{
    extract($args);

    $message = preg_replace("/\[sub\](.*?)\[\/sub\]/si", xarTplModule('bbcode','user', 'sub', array('replace' => '\\1')), $message);

    return $message;
}
?>