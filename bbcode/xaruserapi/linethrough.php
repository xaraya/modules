<?php
function bbcode_userapi_linethrough($args)
{
    extract($args);

    $message = preg_replace("/\[lt\](.*?)\[\/lt\]/si", xarTplModule('bbcode','user', 'linethrough', array('replace' => '\\1')), $message);

    return $message;
}
?>