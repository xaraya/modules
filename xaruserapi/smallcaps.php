<?php
function bbcode_userapi_smallcaps($args)
{
    extract($args);

    $message = preg_replace("/\[sc\](.*?)\[\/sc\]/si", xarTplModule('bbcode','user', 'smallcaps', array('replace' => '\\1')), $message);

    return $message;
}
?>