<?php
function bbcode_userapi_code($args)
{
    extract($args);
    // $message = str_replace('<br />', '', $message); 
    // [code] and [/code] for code stuff.
    $patterns[0] = "/\[code\](.*?)\[\/code\]/si";
    $replacements[0] = xarTplModule('bbcode','user', 'code', array('replace' => '\\1'));

    $patterns[1] = "#\[code=php\](.*?)\[/code\]#si";
    $replacements[1] = xarTplModule('bbcode','user', 'phpcode', array('replace' => '\\1'));

    $patterns[2] = "#\[code=sql\](.*?)\[/code\]#si";
    $replacements[2] = xarTplModule('bbcode','user', 'sqlcode', array('replace' => '\\1'));

    $patterns[3] = "#\[code=xml\](.*?)\[/code\]#si";
    $replacements[3] = xarTplModule('bbcode','user', 'xmlcode', array('replace' => '\\1'));

    $patterns[4] = "#\[code=csharp\](.*?)\[/code\]#si";
    $replacements[4] = xarTplModule('bbcode','user', 'csharpcode', array('replace' => '\\1'));

    $patterns[5] = "#\[code=delphi\](.*?)\[/code\]#si";
    $replacements[5] = xarTplModule('bbcode','user', 'delphicode', array('replace' => '\\1'));

    $patterns[6] = "#\[code=jscript\](.*?)\[/code\]#si";
    $replacements[6] = xarTplModule('bbcode','user', 'jscriptcode', array('replace' => '\\1'));

    $patterns[7] = "#\[code=python\](.*?)\[/code\]#si";
    $replacements[7] = xarTplModule('bbcode','user', 'pythoncode', array('replace' => '\\1'));

    $patterns[8] = "#\[code=vb\](.*?)\[/code\]#si";
    $replacements[8] = xarTplModule('bbcode','user', 'vbcode', array('replace' => '\\1'));

    $message = preg_replace($patterns, $replacements, $message);
    return $message;
}
?>