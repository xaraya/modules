<?php
function bbcode_userapi_sup($args)
{
    extract($args);

    $message = preg_replace("/\[sup\](.*?)\[\/sup\]/si", xarTplModule('bbcode','user', 'sup', array('replace' => '\\1')), $message);

    return $message;
}
?>