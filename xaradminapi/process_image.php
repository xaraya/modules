<?php
/**
 * Process an image using phpThumb
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
 * Process an image using phpThumb
 *
 * @author mikespub
 * @param   array   $image    The image info array (e.g. coming from getimageinfo or getimages/getuploads/getderivatives)
 * @param   integer $saveas   How to save the processed image (0 = derivative, 1 = [image]_new.[ext], 2 = replace, 3 = output)
 * @param   string  $setting  The predefined setting to use, or
 * @param   array   $params   The phpThumb parameters to use
 * @param   boolean $iscached Check if the processed file already exists (default FALSE)
 * @returns string
 * @return the location of the newly processed image
 */

function images_adminapi_process_image($args)
{
    extract($args);

    $settings = xarModAPIFunc('images','user','getsettings');
    if (!empty($setting) && !empty($settings[$setting])) {
        $params = $settings[$setting];
    } elseif (!empty($params)) {
        $setting = md5(serialize($params));
    } else {
        $setting = '';
        $params = '';
    }

    if (empty($saveas)) {
        $saveas = 0;
    }

    if (empty($image) || empty($params)) {
        $msg = xarML("Invalid parameter '#(1)' to API function '#(2)' in module '#(3)'",
                     '', 'process_image', 'images');
        if ($saveas == 3) {
            $phpThumb =& images_get_thumb();
            // Generate an error image
            $phpThumb->ErrorImage($msg);
            // The calling GUI needs to stop processing here
            return true;
        } else {
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
            // Throw back the error
            return;
        }
    }

    // Default to JPEG format (like phpThumb itself)
    if (empty($params['f'])) {
        $params['f'] = 'jpeg';
    }
    // Determine new file extension based on format
    switch ($params['f']) {
        case 'jpeg';
            $ext = 'jpg';
            break;
        case 'png';
        case 'gif';
        default;
            $ext = $params['f'];
            break;
    }

    // If the image is stored in a real file
    if (file_exists($image['fileLocation'])) {
        switch ($saveas) {
            case 1: // [image]_new.[ext]
                $save = realpath($image['fileLocation']);
                if ($save) {
                    $save = preg_replace('/\.\w+$/',"_new.$ext",$save);
                }
                break;

            case 2: // replace
                // Note: the file extension might not match the selected format here
                $save = realpath($image['fileLocation']);
                break;

            case 3: // output the image to the browser
                $save = '';
                break;

            case 0: // derivative
            default:
                $thumbsdir = xarModGetVar('images', 'path.derivative-store');
                // Use MD5 hash of file location here
                $save = realpath($thumbsdir) . '/' . md5($image['fileLocation']);
                // Add the setting to the filename
                $add = xarVarPrepForOs($setting);
                $add = strtr($add, array(' ' => ''));
                $save .= "-$add.$ext";
                break;
        }
        // Check if we can use a cached file
        if (!empty($iscached) && !empty($save) && file_exists($save)) {
            return $save;
        }

        $file = realpath($image['fileLocation']);
        $phpThumb =& images_get_thumb();
        $phpThumb->setSourceFilename($file);

    // If the image is stored in the database (uploads module)
        // NOTE: the next line is the *only* place i could find which suppresses exceptions through the 0 parameter at the end
        // NOTE: in the 2.x branch that parameter does not exist anymore, so the next code needs to be changed.
    } elseif (is_numeric($image['fileId']) && xarModIsAvailable('uploads') && xarModAPILoad('uploads','user',0) &&
              defined('_UPLOADS_STORE_DB_DATA') && ($image['storeType'] & _UPLOADS_STORE_DB_DATA)) {

        $uploadsdir = xarModGetVar('uploads', 'path.uploads-directory');
        switch ($saveas) {
            case 1: // [image]_new.[ext] // CHECKME: not in the database ?
                $save = realpath($uploadsdir) . '/' . $image['fileName'];
                $save = preg_replace('/\.\w+$/',"_new.$ext",$save);
                break;

            case 2: // replace in the database here
                if (is_dir($uploadsdir) && is_writable($uploadsdir)) {
                    $save = tempnam($uploadsdir, 'xarimage-');
                } else {
                    $save = tempnam(NULL, 'xarimage-');
                }
                $dbfile = 1;
                break;

            case 3: // output the image to the browser
                $save = '';
                break;

            case 0: // derivative
            default:
                $thumbsdir = xarModGetVar('images', 'path.derivative-store');
                // Use file id here
                $save = realpath($thumbsdir) . '/' . $image['fileId'];
                // Add the setting to the filename
                $add = xarVarPrepForOs($setting);
                $add = strtr($add, array(' ' => ''));
                $save .= "-$add.$ext";
                break;
        }
        // Check if we can use a cached file
        if (!empty($iscached) && !empty($save) && empty($dbfile) && file_exists($save)) {
            return $save;
        }

        // get the image data from the database
        $data = xarModAPIFunc('uploads', 'user', 'db_get_file_data', array('fileId' => $image['fileId']));
        if (empty($data)) {
            $msg = xarML("Invalid parameter '#(1)' to API function '#(2)' in module '#(3)'",
                          'image', 'process_image', 'images');
            if ($saveas == 3) {
                $phpThumb =& images_get_thumb();
                // Generate an error image
                $phpThumb->ErrorImage($msg);
                // The calling GUI needs to stop processing here
                return true;
            } else {
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                            new SystemException($msg));
                // Throw back the error
                return;
            }
        }

        $src = implode('', $data);
        unset($data);
        $phpThumb =& images_get_thumb();
        $phpThumb->setSourceData($src);

    } else {
        $msg = xarML("Invalid parameter '#(1)' to API function '#(2)' in module '#(3)'",
                      'image', 'process_image', 'images');
        if ($saveas == 3) {
            $phpThumb =& images_get_thumb();
            // Generate an error image
            $phpThumb->ErrorImage($msg);
            // The calling GUI needs to stop processing here
            return true;
        } else {
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
            // Throw back the error
            return;
        }
    }

// or $phpThumb->setSourceImageResource($gd_image_resource);

    foreach ($params as $name => $value) {
        if (isset($value) && $value !== false) {
            $phpThumb->$name = $value;
        }
    }

    // Process the image
    $result = $phpThumb->GenerateThumbnail();

    if (empty($result)) {
        $msg = implode("\n\n", $phpThumb->debugmessages);
        if ($saveas == 3) {
            // Generate an error image
            $phpThumb->ErrorImage($msg);
            // The calling GUI needs to stop processing here
            return true;
        } else {
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
            // Throw back the error
            return;
        }
    }

    // Output the image to the browser
    if ($saveas == 3) {
        $phpThumb->OutputThumbnail();
        // The calling GUI needs to stop processing here
        return true;

    }

    // Save it to file
    if (empty($save)) {
        $msg = xarML("Invalid parameter '#(1)' to API function '#(2)' in module '#(3)'",
                      'save', 'process_image', 'images');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
        // Throw back the error
        return;
    }

    $result = $phpThumb->RenderToFile($save);

    if (empty($result)) {
        $msg = implode("\n\n", $phpThumb->debugmessages);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
        // Throw back the error
        return;
    }

// TODO: add file entry to uploads when saveas == 1 ?

    // update the uploads file entry if we overwrite a file !
    if (is_numeric($image['fileId']) && $saveas == 2) {
        if (!xarModAPIFunc('uploads','user','db_modify_file',
                           array('fileId'    => $image['fileId'],
                                 'fileType'  => 'image/' . $params['f'],
                                 'fileSize'  => filesize($save),
                                 // reset the extrainfo
                                 'extrainfo' => ''))) {
            return;
        }
        if (!empty($dbfile)) {
            // store the image in the database
            if (!xarModAPIFunc('uploads','user','file_dump',
                               array('fileSrc' => $save,
                                     'fileId'  => $image['fileId']))) {
                return;
            }
        }
    }

    return $save;

}

function &images_get_thumb()
{
    include_once('modules/images/xarclass/phpthumb.class.php');
    $phpThumb = new phpThumb();

    $imagemagick = xarModGetVar('images', 'file.imagemagick');
    if (!empty($imagemagick) && file_exists($imagemagick)) {
        $phpThumb->config_imagemagick_path = realpath($imagemagick);
    }

// CHECKME: document root may be incorrect in some cases

    return $phpThumb;
}

?>
