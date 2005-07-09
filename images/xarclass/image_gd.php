<?php

include_once('image_properties.php');

class Image_GD extends Image_Properties {

    var $srcFile;
    var $_tmpFile;
    
    function __constructor($fileLocation) {
        $this->srcFile = $fileLocation;
        $this =& Image_Properties::__constructor($fileLocation);
    }

    function Image_GD($fileLocation) {
        return $this->__constructor($fileLocation);
    }

    function resize() {
    }

    function rotate() {
    }

}

function Image( $location ) {
    switch(strtoupper(xarModGetVar('images', 'image.rendering.library'))) {
        case 'IMAGE_MAGICK':
            return new Image_ImageMagick($location);
            break;
        case 'NETPBM':
            return new Image_NetPBM($location);
            break;
        default:
        case 'GD':
            return new Image_GD($location);
            break;
    }
}

$image = new Image_GD('../xarimages/admin.gif');
print_r($image);
$image->setPercent(1000);
print_r($image);
$image->setPercent(50);
print_r($image);

$image = new Image_GD('/var/projects/stable/html/modules/base/xarimages/exception.jpg');
print_r($image);
$image->setPercent(1000);
print_r($image);
$image->setPercent(50);
print_r($image);

#$image->saveImage($derivative = TRUE);
#$thumb = $image->getDerivative(height, width)

?>
