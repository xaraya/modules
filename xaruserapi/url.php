<?php
// Code for underline text
function bbcode_userapi_url($args)
{
    extract($args);

    // [url]xxxx://www.phpbb.com[/url] code..
    $patterns[0] = "#\[url\]([a-z]+?://){1}(.*?)\[/url\]#si";
    $replacements[0] = xarTplModule('bbcode','user', 'underline', array('replace1' => '\\1', 'replace2' => '\\2'));

    // [url]www.phpbb.com[/url] code.. (no xxxx:// prefix).
    $patterns[1] = "#\[url\](.*?)\[/url\]#si";
    $replacements[1] = xarTplModule('bbcode','user', 'underline', array('replace1' => '\\1'));

    // [url=xxxx://www.phpbb.com]phpBB[/url] code..
    $patterns[2] = "#\[url=([a-z]+?://){1}(.*?)\](.*?)\[/url\]#si";
    $replacements[2] = xarTplModule('bbcode','user', 'underline', array('replace1' => '\\1', 'replace2' => '\\2', 'replace3' => '\\3'));

    // [url=www.phpbb.com]phpBB[/url] code.. (no xxxx:// prefix).
    $patterns[3] = "#\[url=(.*?)\](.*?)\[/url\]#si";
    $replacements[3] = xarTplModule('bbcode','user', 'underline', array('replace1' => '\\1', 'replace2' => '\\2'));

    $message = preg_replace($patterns, $replacements, $message);

    return $message;
}
?>