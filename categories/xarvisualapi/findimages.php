<?php

/**
 * Get a list of images from the modules/categories/xarimages directory
 * (may be overridden by versions in themes/<theme>/modules/categories/images)
 */
function categories_visualapi_findimages()
{
    //$curdir = xarTplGetThemeDir() . '/images';
    $curdir = 'modules/categories/xarimages';
    $curdir = realpath($curdir);
    $image_array = array();
    //$image_array[] = '';
    if ($dir = @opendir($curdir)) {
        while(($file = @readdir($dir)) !== false) {
            if (preg_match('/\.(png|gif|jpg|jpeg)$/',$file)) {
                $image_array[] = $file;
            }
        }
    }
    return $image_array;
}

?>