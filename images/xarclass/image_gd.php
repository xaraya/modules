<?php

include_once('image_properties.php');

class Image_GD extends Image_Properties 
{

    function __constructor($fileLocation, $thumbsdir=NULL) 
    {
        parent::__constructor($fileLocation, $thumbsdir);
    }

    function Image_GD($fileLocation, $thumbsdir=NULL) 
    {
        return $this->__constructor($fileLocation, $thumbsdir);
    }
    
    /**
     * Concept borrowed from: 
     *    http://nyphp.org/content/presentations/GDintro/gd20.php
     * by: 
     *    Jeff Knight of New York PHP
     **
     */
    function resize() 
    {
        
        // If the original height and widht are the same
        // as the new height and width, return true
        if ($this->_owidth == $this->width && 
            $this->_oheight == $this->height) {
                return TRUE;
        } 
        
        if ($this->getDerivative()) {
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
        } else {
            return FALSE;
        } 
        return TRUE;
    }
    
    function &_open() 
    { 
        
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
    
    function rotate() 
    {
    
    }
    
    function scale() 
    {
    
    }
    
    function crop() 
    {
    
    }

}

?>