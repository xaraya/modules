<?php

/**
 * Handle <xar:image ... /> tags
 * Format : <xar:image src="fileId | URL" width="[0-9]+(px|%)" [height="[0-9]+(px|%)" constrain="(yes|true|1|no|false|0)"] label="text" />
 * examples:
 *  <xar:image src="32" width="25%" constrain="yes" label="An image" />
 *  <xar:image src="http://somesite.com/some_image.jpg" width="50px" height="50px" label="a little image" />
 *
 * @param $args array containing the item that you want to display, or fields
 * @returns string
 * @return the PHP code needed to invoke showdisplay() in the BL template
 */

function images_userapi_handle_image_tag($args)
{
    extract($args);

    if (!isset($width) && !isset($height)) {
        $msg = xarML('Required attributes \'#(1)\' and \'#(2)\' for tag <xar:image> are missing. See tag documentation.', 'width', 'height');
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Missing Attributes'), new DefaultUserException($msg));
        return false;
    }

    $format = 'array(%s)';
    foreach ($args as $key => $value) {
        $items[] = "'$key' => \"$value\"";
    } 
    $array = sprintf($format, implode(',', $items));

    $imgTag = sprintf("\$tag = xarModAPIFunc('images', 'user', 'resize', %s); if (!\$tag) { return; } else { echo \$tag; }", $array);   
    return $imgTag;
}

?>
