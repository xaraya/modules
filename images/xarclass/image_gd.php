<?php

include_once('image_properties.php');

class Image_GD extends Image_Properties {

    function __constructor($fileLocation, $thumbsdir=NULL) {
        parent::__constructor($fileLocation, $thumbsdir);
    }

    function Image_GD($fileLocation, $thumbsdir=NULL) {
        return $this->__constructor($fileLocation, $thumbsdir);
    }
    
    // Grabbed from: http://nyphp.org/content/presentations/GDintro/gd20.php
    // written by: Jeff Knight of New York PHP
    function resize() {
        if ($this->_owidth == $this->width && $this->_oheight == $this->height) {
            return NULL;
        } 
        
        if ($this->getDerivative()) {
            echo "\n" . $this->getDerivative() . "\n";
            return TRUE;
        }
        
        $origImage = $this->_open();
        
        if (is_resource($origImage)) {
            $this->_tmpFile = tempnam(NULL, 'xarimage-');
            
            $newImage = imageCreateTrueColor($this->width, $this->height);
            imageCopyResampled($newImage, $origImage, 0, 0, 0, 0, $this->width, $this->height, $this->_owidth, $this->_oheight);
            imageJPEG($newImage, $this->_tmpFile);
            imageDestroy($newImage);
            imageDestroy($origImage);
            echo "Image is: " . filesize($this->_tmpFile) . " bytes long.\n";
            $this->saveDerivative();
        }
        
        return isset($ermsg) ? $ermsg : NULL;
        
    }
    
    function &_open() { 
        
        $origImage = NULL;
        
        switch ($this->mime['text']) {
            case 'image/gif':
                if (imagetypes() & IMG_GIF)  { 
                    $origImage = imageCreateFromGIF($this->fileLocation) ;
                } 
                break;
            case 'image/jpeg':
            case 'image/jpg':
                if (imagetypes() & IMG_JPG)  {
                    $origImage = imageCreateFromJPEG($this->fileLocation) ;
                } 
                break;
            case 'image/png':
                if (imagetypes() & IMG_PNG)  {
                    $origImage = imageCreateFromPNG($this->fileLocation) ;
                } 
                break;
            case 'image/wbmp':
                if (imagetypes() & IMG_WBMP)  {
                    $origImage = imageCreateFromWBMP($this->fileLocation) ;
                } 
                break;
        }
        
        return $origImage;
    
    }
    
    function rotate() {
    
    }
    
    function scale() {
    
    }
    
    function crop() {
    
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

$image = new Image_GD('/home/ccorliss/projects/stable/html/modules/base/xarimages/exception.jpg');
$image->setPercent(150);
print_r($image);

$image->resize();
#$image->saveImage($derivative = TRUE);
#$thumb = $image->getDerivative(height, width)

?>
