<?php
/**
 * Images Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 * Primarily used by Articles as a transform hook to turn "upload tags" into various display formats
 *
 * @param  $args ['extrainfo']
 * @return
 */
function & images_userapi_transformhook ( $args )
{
    extract($args);

    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] =& images_userapi_transform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        foreach ($extrainfo as $text) {
            $result[] =& images_userapi_transform($text);
        }
    } else {
        $result =& images_userapi_transform($extrainfo);
    }
    return $result;
}

function & images_userapi_transform ( $body )
{

    while(eregi('#(image-resize):([0-9]+):([^#]*)#', $body, $parts)) {
        // first argument is always the complete haystack
        // get rid of it
        array_shift($parts);

        list($type, $id) = $parts;
        // get rid of the type and id so all we have left are the arguments now :)
        array_shift($parts);
        array_shift($parts);

        // The remaining indice should be the only one and should contain the arguments
        // that we will package and send to the resize function
        assert('count($parts) == 1');
        $parts = $parts[0];

        switch ( $type )  {
            case 'image-resize':
                $parts = explode(':', $parts);
                // with image-resize, all we want to pass back to the content is the url
                // location of the resized image so it can be dropped in a <img> tag
                // like so: <img src="#image-resize:23:200::true#" alt="some alt text" />
                list($width, $height, $constrain) = $parts;
                if (!empty($width)) {
                    $args['width'] = $width;
                }

                if (!empty($height)) {
                    $args['height'] = $height;
                }

                if (!empty($constrain)) {
                    $args['constrain'] = (int) ((bool) $constrain);
                }

                $args['label'] = 'empty';
                $args['src']   = $id;

                if (!xarModAPIFunc('images', 'user', 'resize', $args)) {
                    return;
                } else {
                    unset($args['label']);
                    unset($args['constrain']);
                    unset($args['src']);

                    $args['fileId'] = $id;

                    $args['width']  = $args['width'];
                    $args['height'] = $args['height'];

                    $replacement = xarModURL('images', 'user', 'display', $args);
                }
                break;
        }
        $parts = implode(':', $parts);
        $body = ereg_replace("#$type:$id:$parts#", $replacement, $body);

    }

    return $body;
}
?>
