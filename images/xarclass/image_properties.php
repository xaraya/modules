<?php

class Image_Properties {

    var $fileName;
    var $fileLocation;
    var $_thumbsdir;
    var $height;
    var $width;
    var $_oheight;
    var $_owidth;
    var $_percent;
    var $mime;
    var $_tmpFile;
    
    function __constructor($fileLocation, $thumbsdir = NULL) {
        $this->fileLocation = $fileLocation;
        $this->fileName = basename($fileLocation);
        
        if (NULL == $thumbsdir || empty($thumbsdir)) {
            $this->_thumbsdir = './'; 
        } else {
            $this->_thumbsdir = $thumbsdir;
        }
        
        $imageInfo = getimagesize($this->fileLocation);
        
        if (is_array($imageInfo)) {
            $this->_owidth  = $this->width  = $imageInfo[0];
            $this->_oheight = $this->height = $imageInfo[1];
            $this->setPercent(100);
            $this->setMime($this->_getMimeType($imageInfo[2]));
            return $this;
        } else {
            trigger_error("File [$fileLocation] is not an image.");
            return NULL;
        }

    }

    function Image_Properties($fileLocation, $thumbsdir = NULL) {
        return $this->__constructor($fileLocation, $thumbsdir);
    }

    function _getMimeType($mimeType) {
        if (is_numeric($mimeType)) {
            switch ($mimeType) {
                case 1:  return array('text' => 'image/gif', 'id' => 1);
                case 2:  return array('text' => 'image/jpg', 'id' => 2);
                case 3:  return array('text' => 'image/png', 'id' => 3);
                case 4:  return array('text' => 'application/x-shockwave-flash', 'id' => 4);
                case 5:  return array('text' => 'image/psd', 'id' => 5);
                case 6:  return array('text' => 'image/bmp', 'id' => 6);
                case 7:
                case 8:  return array('text' => 'image/tiff', 'id' => 8);
                case 9:  return array('text' => 'application/octet-stream', 'id' => 9);
                case 10: return array('text' => 'image/jp2', 'id' => 10);
                case 11: return array('text' => 'application/octet-stream', 'id' => 11);
                case 12: return array('text' => 'application/octet-stream', 'id' => 12);
                case 13: return array('text' => 'application/x-shockwave-flash', 'id' => 13);
                case 14: return array('text' => 'image/iff', 'id' => 14);
                case 15: return array('text' => 'image/vnd.wap.wbmp', 'id' => 15);;
                case 16: return array('text' => 'image/xbm', 'id' => 16);
                default: return 'application/octet-stream';
            }
        } else {
            return $mimeType;
        }
    }        

    function getMime() {
        return $this->mime;
    }

    function setMime($mimeType) {
        $old_mime = $this->mime;
        $this->mime = $mimeType;
        return $old_mime;
    }

    function Constrain($toSide = NULL) {
        if ($toSide == NULL) {
            return FALSE;
        } else {
            switch(strtolower($toSide)) {
                case 'height': 
                    $this->setWidth($this->getWidth2HeightRatio() * $this->height);
                    break;
                case 'width':
                    $this->setHeight($this->getHeight2WidthRatio() * $this->width);
                    break;
            }
        }
        return true;
    }
    
    function getHeight( ) {
        return $this->height;
    }
   
    function setHeight($height) {
        $new_hpercent = @($height / $this->_oheight);
        $this->_percent['height'] = $new_hpercent * 100;
        $this->height = ceil($height);
        return $this->height;
    }

    function getWidth( ) {
        return $this->width;
    }

    function setWidth($width) {
        $new_wpercent = @($width / $this->_owidth);
        $this->_percent['width'] = $new_wpercent * 100;
        $this->width = ceil($width);
        return $this->width;
    }

    function getWidth2HeightRatio() {
        return $this->_owidth / $this->_oheight;
    }

    function getHeight2WidthRatio() {
        return @($this->_oheight / $this->_owidth);
    }

    function getPercent() {
        return $this->_percent;
    }

    function setPercent( $args ) {
        if (!is_array($args) && is_numeric($args)) {
            return $this->_setPercent($args);
        }

        switch(count($args)) {
            case 1:
                switch(strtolower(key($args))) {
                    case 'percent':
                        return $this->_setPercent($args['percent']);
                        break;
                    case 'wpercent':
                        return $this->_setWPercent($args['wpercent']);
                        break;
                    case 'hpercent':
                        return $this->_setHPercent($args['hpercent']);
                        break;
                    default:
                        return FALSE;
                }
                break;
            case 2:
                if (isset($args['wpercent']) && isset($args['hpercent']))
                    return $this->_setWxHPercent($args['wpercent'], $args['hpercent']);
                else
                    return FALSE;
                break;
            default:
                return FALSE;
                break;
        }
    }

    function _setPercent($percent) {
        $this->setHeight($this->_oheight * ($percent / 100));
        $this->_percent['height'] = $percent;

        $this->setWidth($this->_owidth * ($percent / 100));
        $this->_percent['width']  = $percent;
        return TRUE;
    }

    function _setWxHPercent($wpercent, $hpercent) {
        $this->setHeight($this->_oheight * ($hpercent / 100));
        $this->_percent['height'] = $hpercent;

        $this->setWidth($this->_owidth * ($wpercent / 100));
        $this->_percent['width']  = $wpercent;
        return TRUE;
    }

    function _setWPercent($wpercent) {
        $this->_percent['width'] = $wpercent;
        return $this->setWidth($this->_owidth * ($wpercent / 100));
    }

    function _setHPercent($hpercent) {
        $this->_percent['height'] = $hpercent;
        return $this->setHeight($this->_oheight * ($hpercent / 100));
    }
    
    function save() {
        if (!empty($this->_tmpFile) && file_exists($this->_tmpFile) && filesize($this->_tmpFile)) {
            if (@copy($this->_tmpFile, $this->fileLocation)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        return TRUE;
    }
    
    function saveDerivative() {
        if (!empty($this->_tmpFile) && file_exists($this->_tmpFile) && filesize($this->_tmpFile)) {
            // remove any file name extension from the file name 
            $fileParts = explode('.', $this->fileName);
            if (count($fileParts) > 1) {
                array_pop($fileParts);
                if (count($fileParts) > 1) {
                    $fileName = implode('.', $fileParts);
                } else {
                    $fileName = $fileParts[0];
                }
            } 

            $derivName = $this->_thumbsdir . '/' . $fileName . "-{$this->width}x{$this->height}.jpg";
            if (copy($this->_tmpFile, $derivName)) {
                return $derivName;
            } else {
                return NULL;
            }                
        } else {
            return NULL;
        }
    }
    
    function getDerivative() {
        
        // remove any file name extension from the file name 
        $fileParts = explode('.', $this->fileName);
        if (count($fileParts) > 1) {
            array_pop($fileParts);
            if (count($fileParts) > 1) {
                $fileName = implode('.', $fileParts);
            } else {
                $fileName = $fileParts[0];
            }
        } 
        if ($this->width == $this->_owidth && $this->height == $this->_oheight) {
            $derivName = $this->fileLocation;
        } else {
            $derivName = $this->_thumbsdir . '/' . $fileName . "-{$this->width}x{$this->height}.jpg";
        }

        if (file_exists($derivName)) {
            return $derivName;
        } else {
            return NULL;
        }
    }

}

?>
