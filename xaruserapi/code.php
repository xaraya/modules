<?php
function bbcode_userapi_code($args)
{
    extract($args);

    // [code] and [/code] for code stuff.
    $message = preg_replace("/\[code\](.*?)\[\/code\]/si", "<p>" . xarML('Code') . ": </p><div class='bbcode_code' style=' padding: 5px; white-space: normal'>\\1</div>", $message);

    return $message;
}
?>
