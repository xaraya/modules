<?php
/**
 * Count the number of derivative images
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
 * count the number of derivative images
 *
 * @author mikespub
 * @param   mixed   $fileId    (optional) The file id(s) of the image(s) we're looking for
 * @param   string  $fileName  (optional) The name of the image we're getting derivatives for
 * @param   string  $thumbsdir (optional) The directory where derivative images are stored
 * @param   string  $filematch (optional) Specific file match for derivative images
 * @return int the number of images
 */
function images_adminapi_countderivatives($args)
{
    extract($args);

    if (!empty($fileId)) {
        if (!is_array($fileId)) {
            $fileId = array($fileId);
        }
        return count($fileId);
    }

    if (empty($thumbsdir)) {
        $thumbsdir = xarModGetVar('images', 'path.derivative-store');
    }
    if (empty($thumbsdir)) {
        return 0;
    }
    if (empty($filematch)) {
        $filematch = '';
        if (!empty($fileName)) {
            // Note: resized images are named [filename]-[width]x[height].jpg - see resize() method
            //$filematch = '^' . $fileName . '-\d+x\d+';
            // Note: processed images are named [filename]-[setting].[ext] - see process_image() function
            $filematch = '^' . $fileName . '-.+';
        }
    }
    if (empty($filetype)) {
        // Note: resized images are JPEG files - see resize() method
        //$filetype = 'jpg';
        // Note: processed images can be JPEG, GIF or PNG files - see process_image() function
        $filetype = '(jpg|png|gif)';
    }

    $params = array('basedir'   => $thumbsdir,
                    'filematch' => $filematch,
                    'filetype'  => $filetype);

    $cachekey = md5(serialize($params));
    // get the number of images from temporary cache - see getderivatives()
    if (xarVarIsCached('Modules.Images','countderivatives.'.$cachekey)) {
        return xarVarGetCached('Modules.Images','countderivatives.'.$cachekey);
    } else {
        $files = xarModAPIFunc('dynamicdata','admin','browse',
                               $params);
        if (!isset($files)) return;

        return count($files);
    }

    return 0;
}

?>
