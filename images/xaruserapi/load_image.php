<?php

function & images_userapi_load_image( $args ) 
{

    extract($args);
    
    if (!isset($fileId) || empty($fileId)) {
        $mesg = xarML('Invalid parameter \'#(1)\' to API function \'#(2)\' in module \'#(3)\'', 
                      'fileId', 'load_object', 'iamges');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($mesg));
        return;
    }
    
    $fileInfo = end(xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $fileId)));
    
    if (empty($fileInfo)) {
        return NULL;
    } else {
        $location = $fileInfo['fileLocation'];
    }
    
    include_once('modules/images/xarclass/image_properties.php');
     
    switch(xarModGetVar('images', 'type.graphics-library')) {
        case _IMAGES_LIBRARY_IMAGEMAGICK:
            include_once('modules/images/xarclass/image_ImageMagick.php');
            return new Image_ImageMagick($location, xarModGetVar('images', 'path.derivative-store'));
            break;
        case _IMAGES_LIBRARY_NETPBM:
            include_once('modules/images/xarclass/image_NetPBM.php');
            return new Image_NetPBM($location, xarModGetVar('images', 'path.derivative-store'));
            break;
        default:
        case _IMAGES_LIBRARY_GD:
            include_once('modules/images/xarclass/image_gd.php');
            return new Image_GD($location, xarModGetVar('images', 'path.derivative-store'));
            break;
    }
}

?>
