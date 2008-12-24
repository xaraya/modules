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
 * get an array of images (id => field) for use in dropdown lists
 *
 * Note : for additional optional parameters, see the getuploads() and getimages() functions
 *
 * @param $args['bid'] (optional) baseId for server images, otherwise uploads images
 * @param $args['field'] field to use in the dropdown list (default 'fileName')
 * @return array of images, or false on failure
 */
function images_userapi_dropdownlist($args = array())
{
    // Add default arguments
    if (!isset($args['sort'])) {
        $args['sort'] = 'name';
    }
    if (!isset($args['numitems'])) {
        $args['numitems'] = 9999;
    }
    if (!isset($args['bid'])) {
        // Get the uploads images
        $images = xarModAPIFunc('images','admin','getuploads',$args);
    } else {
        // Get the base directories configured for server images
        $basedirs = xarModAPIFunc('images','user','getbasedirs');
        if (empty($args['bid']) || empty($basedirs[$args['bid']])) {
            $args['bid'] = 0; // themes directory
        }
        $args = array_merge($basedirs[$args['bid']], $args);
        // Get the server images
        $images = xarModAPIFunc('images','admin','getimages',$args);
    }
    if (!$images) return;

    if (!isset($args['field'])) {
        $args['field'] = 'fileName';
    }

    // Fill in the dropdown list
    $list = array();
    $list[0] = '';
    $field = $args['field'];
    foreach ($images as $image) {
        if (!isset($image[$field])) continue;
    // TODO: support other formatting options here depending on the field type ?
        $list[$image['fileId']] = xarVarPrepForDisplay($image[$field]);
    }

    return $list;
}

?>
