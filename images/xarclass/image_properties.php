<?php

class Image_Properties {

    var $height;
    var $width;
    var $_oheight;         
    var $_owidth;
    var $_percent;
    var $mime;

    function __constructor($fileLocation) {

        $imageInfo = getimagesize($fileLocation);
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

    function Image_Properties($fileLocation) {
        return $this->__constructor($fileLocation);
    }

    function _getMimeType($mimeType) {
        if (is_numeric($mimeType)) {
            switch ($mimeType) {
                case 1:
                    return 'image/gif';
                case 2:
                    return 'image/jpg';
                case 3:
                    return 'image/png';
                default:
                    return FALSE;
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

    function getHeight( ) {
        return $this->height;
    }

    function setHeight($height, $constrain = FALSE) {
        if ($constrain) {
            $this->setWidth($this->getWidth2HeightRatio() * $height);
        }
        $new_hpercent = @($height / $this->_oheight);
        $this->_percent['height'] = $new_hpercent * 100;
        $this->height = ceil($height);
        return $old_height;
    }

    function getWidth( ) {
        return $this->width;
    }

    function setWidth($width, $constrain = FALSE) {
        if ($constrain) {
            $this->setHeight($this->getHeight2WidthRatio() * $width);
        }
        $new_wpercent = @($width / $this->_owidth);
        $this->_percent['width'] = $new_wpercent * 100;
        $this->width = ceil($width);
        return $old_width;
    }

    function getWidth2HeightRatio() {
        return $this->width / $this->height;
    }

    function getHeight2WidthRatio() {
        return $this->height / $this->width;
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
}

?>
