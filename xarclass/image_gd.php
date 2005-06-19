<?php

include_once('modules/images/xarclass/image_properties.php');

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
    function resize($forceResize = false) 
    {
        
        // If the original height and widht are the same
        // as the new height and width, return true
        if ($this->_owidth == $this->width && 
            $this->_oheight == $this->height) {
                return TRUE;
        } 
        
        if (empty($forceResize) && $this->getDerivative()) {
            return TRUE;
        }
        
        $origImage = $this->_open();
        
        if (is_resource($origImage)) {
            if (is_dir($this->_thumbsdir) && is_writable($this->_thumbsdir)) {
                $this->_tmpFile = tempnam($this->_thumbsdir, 'xarimage-');
            } else {
                $this->_tmpFile = tempnam(NULL, 'xarimage-');
            }
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

        if (!file_exists($this->fileLocation) && !empty($this->_fileId)) {
            // get the image data from the database
            $data = xarModAPIFunc('uploads', 'user', 'db_get_file_data', array('fileId' => $this->_fileId));
            if (!empty($data)) {
                $src = implode('', $data);
                unset($data);
                $origImage = imagecreatefromstring($src);
            }
            return $origImage;
        }

        switch ($this->mime['text']) {
            case 'image/gif':
                // this will fail for GIF Read Support !
                //if (imagetypes() & IMG_GIF)  {
                if (function_exists('imageCreateFromGIF')) {
                    $origImage = @imageCreateFromGIF($this->fileLocation) ;
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
