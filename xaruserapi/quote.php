<?php
function bbcode_userapi_quote($args)
{
    extract($args);

    // [quote]text[/quote] code..
    $patterns[0] = "#\[quote\](.*?)\[/quote\]#si";
    $replacements[0] = "<p>" . xarML('Quote') . " :</p> <div style=\"width: 90%; overflow: auto;\"><blockquote>\\1</blockquote></div>";
    
    // [quote=name]text[/quote] code..
    $patterns[1] = "#\[quote=(.*?)\](.*?)\[/quote\]#si";
    $replacements[1] = "<p>" . xarML('Quote') . " \\1:</p> <div style=\"width: 90%; overflow: auto;\"><blockquote>\\2</blockquote></div>";

    $message = preg_replace($patterns, $replacements, $message);
            
    return $message;
}
?>
