<?php
function bbcode_userapi_code($args)
{
    extract($args);

    // [code] and [/code] for code stuff.
    $message = preg_replace("/\[code\](.*?)\[\/code\]/si", xarTplModule('bbcode','user', 'code', array('replace' => '\\1')), $message);

    return $message;
}
?>