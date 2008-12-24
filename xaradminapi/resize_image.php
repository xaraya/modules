<?php
/**
 * Resizes an image to the given dimensions
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
 * Resizes an image to the given dimensions
 *
 * @author mikespub
 * @param   integer $fileId        The (uploads) file id of the image to load, or
 * @param   string  $fileLocation  The file location of the image to load
 * @param   string  $height        The new height (in pixels or percent) ([0-9]+)(px|%)
 * @param   string  $width         The new width (in pixels or percent)  ([0-9]+)(px|%)
 * @param   boolean $constrain     if height XOR width, then constrain the missing value to the given one
 * @param   string  $thumbsdir     (optional) The directory where derivative images are stored
 * @param   string  $derivName     (optional) The name of the derivative image to be saved
 * @param   boolean $forceResize   (optional) Force resizing the image even if it already exists
 * @return  string the location of the newly resized image
 * @throws  BAD_PARAM
 */
function images_adminapi_resize_image($args)
{
    extract($args);
    // Check the conditions
    if (empty($fileId) && empty($fileLocation)) {
        $mesg = xarML("Invalid parameter '#(1)' to API function '#(2)' in module '#(3)'",
                      '', 'resize_image', 'images');
        throw new Exception($mesg);
    } elseif (!empty($fileId) && !is_numeric($fileId)) {
        $mesg = xarML("Invalid parameter '#(1)' to API function '#(2)' in module '#(3)'",
                      'fileId', 'resize_image', 'images');
        throw new Exception($mesg);
    } elseif (!empty($fileLocation) && !is_string($fileLocation)) {
        $mesg = xarML("Invalid parameter '#(1)' to API function '#(2)' in module '#(3)'",
                      'fileLocation', 'resize_image', 'images');
        throw new Exception($mesg);
    }

    if (!isset($width) && !isset($height)) {
        $msg = xarML("Required parameters '#(1)' and '#(2)' are missing.", 'width', 'height');
        throw new Exception($msg);
    } elseif (!isset($width) && !xarVarValidate('regexp:/[0-9]+(px|%)/:', $height)) {
        $msg = xarML("'#(1)' parameter is incorrectly formatted.", 'height');
        throw new Exception($msg);
    } elseif (!isset($height) && !xarVarValidate('regexp:/[0-9]+(px|%)/:', $width)) {
        $msg = xarML("'#(1)' parameter is incorrectly formatted.", 'width');
        throw new Exception($msg);
    }

    // just a flag for later
    $constrain_both = FALSE;

    if (!isset($constrain)) {
        if (isset($width) XOR isset($height)) {
            $constrain = TRUE;
        } elseif (isset($width) && isset($height)) {
            $constrain = FALSE;
        }
    } else {
        // we still want to constrain here, but we might need to be a little bit smarter about it
        // if we have both a height and a width, we don't want the image to be any larger than
        // any pf the supplied values, so we have to provide some logic to handle this
        if (isset($width) && isset($height)) {
            //$constrain = FALSE;
            $constrain_both = TRUE;
        } //else {
            $constrain = (bool) $constrain;
        //}

    }

    $notSupported = FALSE;

    // if both arguments are specified, give priority to fileId
    if (!empty($fileId)) {
        $fileInfo = end(xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $fileId)));
        if (empty($fileInfo)) {
            return;
        } else {
            $location = $fileInfo['fileLocation'];
        }
    } else {
        $location = $fileLocation;
        $fileId = null;
    }

    // TODO: refactor to support other libraries (ImageMagick/NetPBM)
    if (!empty($fileInfo['fileLocation'])) {
        $imageInfo = xarModAPIFunc('images','user','getimagesize',$fileInfo);
        $gd_info = xarModAPIFunc('images', 'user', 'gd_info');
        if (empty($imageInfo) || (!$imageInfo[2] & $gd_info['typesBitmask'])) {
            $notSupported = TRUE;
        }
    } elseif (!empty($fileLocation) && file_exists($fileLocation)) {
        $imageInfo = @getimagesize($fileLocation);
        $gd_info = xarModAPIFunc('images', 'user', 'gd_info');
        if (empty($imageInfo) || (!$imageInfo[2] & $gd_info['typesBitmask'])) {
            $notSupported = TRUE;
        }
    } else {
        $notSupported = TRUE;
    }
    // Raise a user error when the format is not supported
    if ($notSupported) {
        $msg = xarML('Image type for file: #(1) is not supported for resizing', $location);
        throw new Exception($msg);
    }

    if (empty($thumbsdir)) {
        $thumbsdir = xarModVars::get('images', 'path.derivative-store');
    }

    $image = xarModAPIFunc('images', 'user', 'load_image', array('fileId' => $fileId,
                                                                 'fileLocation' => $location,
                                                                 'thumbsdir' => $thumbsdir));

    if (!is_object($image)) {
        $msg = xarML('File not found.');
        throw new Exception($msg);
    }

    if (isset($width)) {
        eregi('([0-9]+)(px|%)', $width, $parts);
        $type = ($parts[2] == '%') ? _IMAGES_UNIT_TYPE_PERCENT : _IMAGES_UNIT_TYPE_PIXELS;
        switch ($type) {
            case _IMAGES_UNIT_TYPE_PERCENT:
                $image->setPercent(array('wpercent' => $width));
                break;
            default:
            case _IMAGES_UNIT_TYPE_PIXELS:
                $image->setWidth($parts[1]);

        }

        if ($constrain) {
            $constrain_both ? $image->Constrain('both') : $image->Constrain('width');
        }
    }

    if (isset($height)) {
        eregi('([0-9]+)(px|%)', $height, $parts);
        $type = ($parts[2] == '%') ? _IMAGES_UNIT_TYPE_PERCENT : _IMAGES_UNIT_TYPE_PIXELS;
        switch ($type) {
            case _IMAGES_UNIT_TYPE_PERCENT:
                $image->setPercent(array('hpercent' => $height));
                break;
            default:
            case _IMAGES_UNIT_TYPE_PIXELS:
                $image->setHeight($parts[1]);

        }

        if ($constrain) {
            $constrain_both ? $image->Constrain('both') : $image->Constrain('height');
        }
    }

    if (empty($derivName)) {
        $derivName = '';
    }

    if (empty($forceResize)) {
        $location = $image->getDerivative($derivName);
        $forceResize = false;
    } else {
        $location = '';
        $forceResize = true;
    }
    if (!$location) {
        if ($image->resize($forceResize)) {
            $location = $image->saveDerivative($derivName);
            if (!$location) {
                $msg = xarML('Unable to save resized image !');
                throw new Exception($msg);
            }
        } else {
            $msg = xarML("Unable to resize image '#(1)'!", $image->fileLocation);
            throw new Exception($msg);
        }
    }

    return $location;
}

?>
