<?php
/**
 * Initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the Images module development team
 * @param $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function images_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);

    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }

    // if we don't have a numeric fileId, can't do too much
    if (empty($fileId) || !is_numeric($fileId)) {
        return;
    } else {
        // get the mime type from the arguments
        if (!empty($fileType)) {
            $type = explode('/', $fileType);

        // get the mime type from cache for resize()
        } elseif (xarVarIsCached('Module.Images','imagemime.'.$fileId)) {
            $fileType = xarVarGetCached('Module.Images','imagemime.'.$fileId);
            $type = explode('/', $fileType);

        // get the mime type from the database (urgh)
        } else {
            // Bug 5410 Make a two step process
            $imageinfo = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' =>$fileId));
            $image = end($imageinfo);

            if (empty($image)) {
                // fileId is nonexistant...
                return;
            }

            $type = explode('/', $image['fileType']);
        }

        if ($type[1] == 'jpeg')
            $type[1] = 'jpg';

        $fileName = $fileId . '.' . $type[1];
    }
    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'images';

    // clean the array of the items we already have
    // so we can add any other values to the end of the url
    unset($args['func']);
    unset($args['fileId']);

    if (!empty($args)) {

        foreach ($args as $name => $value) {
            $extra[] = "$name=$value";
        }

        $extras = $join . implode('&', $extra);
    }

    // specify some short URLs relevant to your module
    if ($func == 'display') {
        // check for required parameters
        if (!empty($fileId) && is_numeric($fileId)) {
            $path = '/' . $module . '/' . $fileName . (isset($extras) ? $extras : '');
        }
    } else {
        // anything else that you haven't defined a short URL equivalent for
        // -> don't create a path here
    }

    return $path;
}

?>
