<?php
/**
 * Images Module
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
 * Resizes an image to the given dimensions and returns an img tag for the image
 *
 * @param   mixed   $src        The (uploads) id or filename of the image to resize
 * @param   string  $basedir    (optional) Base directory for the given filename
 * @param   string  $height     The new height (in pixels or percent) ([0-9]+)(px|%)
 * @param   string  $width      The new width (in pixels or percent)  ([0-9]+)(px|%)
 * @param   boolean $constrain  if height XOR width, then constrain the missing value to the given one
 * @param   string  $label      Text to be used in the ALT attribute for the <img> tag
 * @param   string  $setting    The predefined settings to apply for processing
 * @param   string  $params     The array of parameters to apply for processing
 * @param   boolean $static     Use static link instead of dynamic one where possible (default FALSE)
 * @param   string  $baseurl    (optional) Base URL for the static links
 * @param   boolean $returnpath (optional) Flag to return the image path instead of the image tag
 * @return  string An <img> tag for the newly resized image
 */
function images_userapi_resize($args)
{
    extract($args);

    if (!isset($src) || empty($src)) {
        $msg = xarML("Required parameter '#(1)' is missing or empty.", 'src');
        throw new BadParameterException(null,$msg);
    }

    if (!isset($label) || empty($label)) {
        $msg = xarML("Required parameter '#(1)' is missing or empty.", 'label');
        throw new BadParameterException(null,$msg);
    }

    if (!isset($width) && !isset($height) && !isset($setting) && !isset($params)) {
        $msg = xarML("Required parameters '#(1)', '#(2)', '#(3)' or '#(4)' for tag <xar:image> are missing. See tag documentation.",
                     'width', 'height', 'setting', 'params');
        throw new BadParameterException(null,$msg);
    } elseif (isset($height) && !xarVarValidate('regexp:/[0-9]+(px|%)/:', $height)) {
        $msg = xarML("'#(1)' parameter is incorrectly formatted.", 'height');
        throw new BadParameterException(null,$msg);
    } elseif (isset($width) && !xarVarValidate('regexp:/[0-9]+(px|%)/:', $width)) {
        $msg = xarML("'#(1)' parameter is incorrectly formatted.", 'width');
        throw new BadParameterException(null,$msg);
    }

    if( !isset($returnpath) ){ $returnpath = false; }

    $notSupported = FALSE;

    // allow passing single DD Uploads values "as is" to xar:image-resize
    if (substr($src,0,1) == ';') {
        $src = substr($src,1);
    }

    if (is_numeric($src)) {
        $imageInfo = xarModAPIFunc('images', 'user', 'getimageinfo', array('fileId' => $src));
    } else {
        if (isset($basedir)) {
            $src = $basedir . '/' . $src;
        }
        $imageInfo = xarModAPIFunc('images', 'user', 'getimageinfo',
                                   array('fileLocation' => $src));
    }
    if (!empty($imageInfo)) {
        // TODO: refactor to support other libraries (ImageMagick/NetPBM)
        $gd_info = xarModAPIFunc('images', 'user', 'gd_info');
        if (empty($imageInfo['imageType']) || (!$imageInfo['imageType'] & $gd_info['typesBitmask'])) {
            $notSupported = TRUE;
        }
    } else {
        $notSupported = TRUE;
    }
    if ($notSupported) {
        $errorMsg = xarML('Image type for file: #(1) is not supported for resizing', $src);
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

    // use predefined setting for processing
    if (!empty($setting)) {
        $settings = xarModAPIFunc('images','user','getsettings');
        if (!empty($settings[$setting])) {
            $location = xarModAPIFunc('images','admin','process_image',
                                      array('image'    => $imageInfo,
                                            'saveas'   => 0, // derivative
                                            'setting'  => $setting,
                                            // don't process the image again if it already exists
                                            'iscached' => TRUE));
            if (empty($location)) {
                $errorstack = xarErrorGet();
                $errorstack = array_shift($errorstack);
                $msg = $errorstack['short'];
                xarErrorHandled();
                return sprintf('<img src="" alt="%s" %s />', $msg, $attribs);
            }

            if (file_exists($location)) {
                $sizeinfo = @getimagesize($location);
                $attribs .= sprintf(' width="%s" height="%s"', $sizeinfo[0], $sizeinfo[1]);
            }

            if (!empty($static)) {
                // if we have a base URL, use that together with the basename
                if (!empty($baseurl)) {
                    $url = $baseurl . '/' . basename($location);

                // or if it's an absolute URL, try to get rid of it
                } elseif (substr($location,0,1) == '/' || substr($location,1,1) == ':') {
                    $thumbsdir = xarModGetVar('images', 'path.derivative-store');
                    $url = $thumbsdir . '/' . basename($location);
                }
                // if it's an absolute URL, try to get rid of it
                if (empty($url)) {
                    $url = $location;
                }

            } else {
                // use the location of the processed image here
                $url = xarModURL('images', 'user', 'display',
                                 array('fileId' => base64_encode($location)));
            }

            if( $returnpath == true ){
                return $url;
            }

            return sprintf('<img src="%s" alt="%s" %s />', $url, $label, $attribs);
        }

    // use parameters for processing
    } elseif (!empty($params)) {
        $location = xarModAPIFunc('images','admin','process_image',
                                  array('image'    => $imageInfo,
                                        'saveas'   => 0, // derivative
                                        'params'   => $params,
                                        // don't process the image again if it already exists
                                        'iscached' => TRUE));
        if (empty($location)) {
            $errorstack = xarErrorGet();
            $errorstack = array_shift($errorstack);
            $msg = $errorstack['short'];
            xarErrorHandled();
            return sprintf('<img src="" alt="%s" %s />', $msg, $attribs);
        }

        if (file_exists($location)) {
            $sizeinfo = @getimagesize($location);
            $attribs .= sprintf(' width="%s" height="%s"', $sizeinfo[0], $sizeinfo[1]);
        }

        if (!empty($static)) {
            // if we have a base URL, use that together with the basename
            if (!empty($baseurl)) {
                $url = $baseurl . '/' . basename($location);

            // or if it's an absolute URL, try to get rid of it
            } elseif (substr($location,0,1) == '/' || substr($location,1,1) == ':') {
                $thumbsdir = xarModGetVar('images', 'path.derivative-store');
                $url = $thumbsdir . '/' . basename($location);

            }
            if (empty($url)) {
                $url = $location;
            }
        } else {
            // use the location of the processed image here
            $url = xarModURL('images', 'user', 'display',
                             array('fileId' => base64_encode($location)));
        }

        if( $returnpath == true ){
            return $url;
        }

        return sprintf('<img src="%s" alt="%s" %s />', $url, $label, $attribs);
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

    // Load Image Properties based on $imageInfo
    $image = xarModAPIFunc('images', 'user', 'load_image', $imageInfo);

    if (!is_object($image)) {
        return sprintf('<img src="" alt="%s" %s />', xarML('File not found.'), $attribs);
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

    $attribs .= sprintf(' width="%s" height="%s"', $image->getWidth(), $image->getHeight());

    $location = $image->getDerivative();
    if (!$location) {
        if ($image->resize()) {
            $location = $image->saveDerivative();
            if (!$location) {
                $msg = xarML('Unable to save resized image !');
                return sprintf('<img src="%s" alt="%s" %s />', '', $msg, $attribs);
            }
        } else {
            $msg = xarML("Unable to resize image '#(1)'!", $image->fileLocation);
            return sprintf('<img src="%s" alt="%s" %s />', '', $msg, $attribs);
        }
    }

    if (!empty($static)) {
        // if we have a base URL, use that together with the basename
        if (!empty($baseurl)) {
            $url = $baseurl . '/' . basename($location);

        // or if it's an absolute URL, try to get rid of it
        } elseif (substr($location,0,1) == '/' || substr($location,1,1) == ':') {
            $thumbsdir = xarModGetVar('images', 'path.derivative-store');
            $url = $thumbsdir . '/' . basename($location);

        }
        if (empty($url)) {
            $url = $location;
        }
    } else {
        // put the mime type in cache for encode_shorturl()
        $mime = $image->getMime();
        if (is_array($mime) && isset($mime['text'])) {
            xarVarSetCached('Module.Images','imagemime.'.$src, $mime['text']);
        } else {
            xarVarSetCached('Module.Images','imagemime.'.$src, $mime);
        }
        $url = xarModURL('images', 'user', 'display',
                         array('fileId' => is_numeric($src) ? $src : base64_encode($src),
                               'height' => $image->getHeight(),
                               'width'  => $image->getWidth()));
    }

    if( $returnpath == true ){
        return $url;
    }

    $imgTag = sprintf('<img src="%s" alt="%s" %s />', $url, $label, $attribs);

    return $imgTag;
}

?>
