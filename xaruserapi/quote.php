<?php
function bbcode_userapi_quote($args)
{
    extract($args);

    // [quote]text[/quote] code..
    $patterns[0] = "#\[quote\](.*?)\[/quote\]#si";
    $replacements[0] = xarTplModule('bbcode','user', 'quote', array('replace1' => '\\1'));
    
    // [quote=name]text[/quote] code..
    $patterns[1] = "#\[quote=(.*?)\](.*?)\[/quote\]#si";
    $replacements[1] = xarTplModule('bbcode','user', 'quote', array('replace1' => '\\1', 'replace2' => '\\2'));

    $message = preg_replace($patterns, $replacements, $message);
            
    return $message;
}
?>
