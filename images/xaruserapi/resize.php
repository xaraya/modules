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

    if (!isset($src) || !is_numeric($src)) {
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
    } elseif (isset($width) && !xarVarFetch('width', 'regexp:/[0-9]+(px|%)/:', $width)) {
        $msg = xarML('\'#(1)\' parameter is incorrectly formatted.', 'width');
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Invalid Parameter'), new DefaultUserException($msg));
        return FALSE;
    } elseif (isset($height) && !xarVarFetch('height', 'regexp:/[0-9]+(px|%)/:', $height)) {
        $msg = xarML('\'#(1)\' parameter is incorrectly formatted.', 'height');
        xarExceptionSet(XAR_USER_EXCEPTION, xarML('Invalid Parameter'), new DefaultUserException($msg));
        return FALSE;
    }

    if (!isset($constrain)) {
        if (isset($width) XOR isset($height)) {
            $constrain = TRUE;
        } elseif (isset($width) && isset($height)) {
            $constrain = FALSE;
        }
    } else {
        if (isset($width) && isset($height)) {
            $constrain = FALSE;
        } else {
            $constrain = (bool) $constrain;
        }
        
    }
    
    $image = xarModAPIFunc('images', 'user', 'load_image', array('fileId' => $src));
    if (!is_object($image)) {
        return 'echo "<img src=\"\" alt=\"' . xarML('File not found.') . '\">;';
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
            $image->Constrain('width');
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
            $image->Constrain('height');
        }
    }

    $url = xarModURL('images', 'user', 'display', 
                      array('fileId' => $src, 
                            'height' => $image->getHeight(),
                            'width'  => $image->getWidth()));

    $imgTag = sprintf('<img src="%s" alt="%s" />', $url, $label);

    if (!$image->getDerivative()) {
        if ($image->resize()) {
            if (!$image->saveDerivative()) {
                return FALSE;
            }
        } else {
            $msg = xarML('Unable to resize image \'#(1)\'!', $image->fileLocation);
            xarExceptionSet(XAR_USER_EXCEPTION, xarML('Image Manipulation Failed'), new DefaultUserException($msg));
            return FALSE;
        }
    }
    
    return $imgTag;
}

?>
