<?php

/**
 * Resizes an image to the given dimensions and returns an img tag for the image
 *
 * @param   integer $src        The (uploads) id of the image to resiae
 * @param   string  $height     The new height (in pixels or percent) ([0-9]+)(px|%)
 * @param   string  $width      The new width (in pixels or percent)  ([0-9]+)(px|%)
 * @param   boolean $constrain  if height XOR width, then constrain the missing value to the given one
 * @param   string  $label      Text to be used in the ALT attribute for the <img> tag
 * @returns string
 * @return an <img> tag for the newly resized image
 */

function images_userapi_resize($args)
{
    extract($args);
    
    if (!isset($src) || empty($src) || !is_numeric($src)) {
        $msg = xarML('Required parameter \'#(1)\' is missing or not numeric.', 'src');
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Invalid Parameter'), new DefaultUserException($msg));
        return FALSE;
    }

    if (!isset($label) || empty($label)) {
        $msg = xarML('Required parameter \'#(1)\' is missing or empty.', 'label');
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Invalid Parameter'), new DefaultUserException($msg));
        return FALSE;
    }

    if (!isset($width) && !isset($height)) {
        $msg = xarML('Required parameters \'#(1)\' and \'#(2)\' are missing.', 'width', 'height');
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Missing Parameters'), new DefaultUserException($msg));
        return FALSE;
    } elseif (!isset($width) && !xarVarFetch('height', 'regexp:/[0-9]+(px|%)/:', $height)) {
        $msg = xarML('\'#(1)\' parameter is incorrectly formatted.', 'height');
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Invalid Parameter'), new DefaultUserException($msg));
        return FALSE;
    } elseif (!isset($height) && !xarVarFetch('width', 'regexp:/[0-9]+(px|%)/:', $width)) {
        $msg = xarML('\'#(1)\' parameter is incorrectly formatted.', 'width');
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Invalid Parameter'), new DefaultUserException($msg));
        return FALSE;
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

    $fileInfo = end(xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $src)));
    // TODO: refactor to support other libraries (ImageMagick/NetPBM)
    if (isset($fileInfo['fileLocation']) && file_exists($fileInfo['fileLocation'])) {
        $imageInfo = getimagesize($fileInfo['fileLocation']);
        $gd_info = xarModAPIFunc('images', 'user', 'gd_info');
        if (empty($imageInfo) || (!$imageInfo[2] & $gd_info['typesBitmask'])) {
            $notSupported = TRUE;
        }
    } else {
        $notSupported = TRUE;
    }
    if ($notSupported) {
        $errorMsg = xarML('Image type is not supported for resizing');
        return '<img src="" alt="' . $errorMsg . '" />';

    }

    $attribs = '';
    $allowedAttribs = array('border', 'class', 'id', 'style', 'align', 'hspace', 'vspace',
                            'onclick', 'onmousedown', 'onmouseup', 'onmouseout', 'onmouseover');

    foreach ($args as $key => $value) {
        if (in_array(strtolower($key), $allowedAttribs)) {
            $attribs .= sprintf(' %s="%s"', $key, $value);
        }
    }

    $image = xarModAPIFunc('images', 'user', 'load_image', array('fileId' => $src));

    if (!is_object($image)) {
        return sprintf('<img src="" alt="%s" %s />', xarML('File not found.'), $attribs) . '" />';
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
            $constrain_both ? $image->Constrain('both') : $image->Constrain('width');
        }
    }

    $attribs .= sprintf(' width="%s" height="%s"', $image->getWidth(), $image->getHeight());

    $url = xarModURL('images', 'user', 'display',
                      array('fileId' => $src,
                            'height' => $image->getHeight(),
                            'width'  => $image->getWidth()));

    $imgTag = sprintf('<img src="%s" alt="%s" title="%s" %s />', $url, $label, $label, $attribs);

    if (!$image->getDerivative()) {
        if ($image->resize()) {
            if (!$image->saveDerivative()) {
                return FALSE;
            }
        } else {
            $msg = xarML('Unable to resize image \'#(1)\'!', $image->fileLocation);
            $imgTag = sprintf('<img src="%s" alt="%s" %s />', $url, $msg, $attribs) . '" />';
            return FALSE;
        }
    }

    return $imgTag;
}

?>
