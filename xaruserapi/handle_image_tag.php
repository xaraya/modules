<?php
/**
 * Images Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 * Handle <xar:image-resize ... /> tags
 * Format : <xar:image-resize src="fileId | URL" width="[0-9]+(px|%)" [height="[0-9]+(px|%)" constrain="(yes|true|1|no|false|0)"] label="text" />
 * examples:
 *  <xar:image-resize src="32" width="50px" height="50px" label="resize an image using pixels" />
 *  <xar:image-resize src="somedir/some_image.jpg" width="25%" constrain="yes" label="resize an image using percentages" />
 *  <xar:image-resize src="32" setting="JPEG 800 x 600" label="process an image with predefined setting" />
 *  <xar:image-resize src="32" params="$params" label="process an image with phpThumb parameters" />
 *
 * @param $args array containing the image that you want to resize and display
 * @return string the PHP code needed to invoke resize() in the BL template
 */

function images_userapi_handle_image_tag($args)
{
    extract($args);

    if (!isset($width) && !isset($height) && !isset($setting) && !isset($params)) {
        $msg = xarML("Required attributes '#(1)', '#(2)', '#(3)' or '#(4)' for tag <xar:image> are missing. See tag documentation.", 'width', 'height', 'setting', 'params');
        throw new Exception($msg);
    }

    $format = 'array(%s)';
    foreach ($args as $key => $value) {
        // preserve support for $info[fileId] as before
        if (substr($value,0,1) == '$' && strpos($value,'[') === FALSE) {
            $items[] = "'$key' => $value";
        } else {
            $items[] = "'$key' => \"$value\"";
        }
    }
    $array = sprintf($format, implode(',', $items));

    $imgTag = sprintf("
        \$tag = xarModAPIFunc('images', 'user', 'resize', %s);
        if (!\$tag) {
            return;
        } else {
            echo \$tag;
        }", $array);
    return $imgTag;
}

?>