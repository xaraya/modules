<?php
function bbcode_userapi_italics($args)
{
    extract($args);

    $message = preg_replace("/\[i\](.*?)\[\/i\]/si", xarTplModule('bbcode','user', 'italics', array('replace' => '\\1')), $message);

    return $message;
}
?>