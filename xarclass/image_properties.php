<?php
/**
 * Images module class
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 * Class Image properties
 */
class Image_Properties
{
    public $fileName;
    public $fileLocation;
    public $_thumbsdir;
    public $height;
    public $width;
    public $_oheight;
    public $_owidth;
    public $_percent;
    public $mime = null;
    public $_tmpFile;
    public $_fileId;

    public function __constructor($fileInfo, $thumbsdir = null)
    {
        if (empty($thumbsdir)) {
            $this->_thumbsdir = './';
        } else {
            $this->_thumbsdir = $thumbsdir;
        }

        if (is_array($fileInfo)) {
            $this->fileLocation = $fileInfo['fileLocation'];
            $this->fileName = $fileInfo['fileName'];
            $this->_fileId = $fileInfo['fileId'];
            if (!empty($fileInfo['imageType'])) {
                $this->_owidth  = $this->width  = $fileInfo['imageWidth'];
                $this->_oheight = $this->height = $fileInfo['imageHeight'];
                $this->setPercent(100);
                $this->setMime($this->_getMimeType($fileInfo['imageType']));
                return $this;
            }
            $imageInfo = xarMod::apiFunc('images', 'user', 'getimagesize', $fileInfo);
        } elseif (file_exists($fileInfo)) {
            $this->fileLocation = $fileInfo;
            $this->fileName = basename($fileInfo);
            $imageInfo = @getimagesize($fileInfo);
        } else {
            trigger_error("File [$this->fileLocation] does not exist.");
            return null;
        }

        if (!empty($imageInfo) && is_array($imageInfo)) {
            $this->_owidth  = $this->width  = $imageInfo[0];
            $this->_oheight = $this->height = $imageInfo[1];
            $this->setPercent(100);
            $this->setMime($this->_getMimeType($imageInfo[2]));
            return $this;
        } else {
            trigger_error("File [$this->fileLocation] is not an image.");
            return null;
        }
    }

    public function Image_Properties($fileLocation, $thumbsdir = null)
    {
        return $this->__constructor($fileLocation, $thumbsdir);
    }

    public function _getMimeType($mimeType)
    {
        if (is_numeric($mimeType)) {
            switch ($mimeType) {
                case 1:  return array('text' => 'image/gif', 'id' => 1);
                case 2:  return array('text' => 'image/jpeg', 'id' => 2);
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
                case 15: return array('text' => 'image/vnd.wap.wbmp', 'id' => 15);
                case 16: return array('text' => 'image/x-xbitmap', 'id' => 16);
                default: return 'application/octet-stream';
            }
        } else {
            return $mimeType;
        }
    }

    public function getMime()
    {
        return $this->mime;
    }

    public function setMime($mimeType)
    {
        $old_mime = $this->mime;
        $this->mime = $mimeType;
        return $old_mime;
    }

    public function Constrain($toSide = null)
    {
        if ($toSide == null) {
            return false;
        } else {
            switch (strtolower($toSide)) {
                case 'height':
                    $this->setWidth($this->getWidth2HeightRatio() * $this->height);
                    break;
                case 'width':
                    $this->setHeight($this->getHeight2WidthRatio() * $this->width);
                    break;
                case 'both':
                    // choose wisely
                    $ratios = array();

                    $ratios[0]['width']  = $this->getWidth2HeightRatio() * $this->height;
                    $ratios[0]['height'] = $this->getHeight2WidthRatio() * $ratios[0]['width'];

                    $ratios[1]['height'] = $this->getHeight2WidthRatio() * $this->width;
                    $ratios[1]['width']  = $this->getWidth2HeightRatio() * $ratios[1]['height'];

                    foreach ($ratios as $ratio) {
                        if ($ratio['width'] <= $this->width && $ratio['height'] <= $this->height) {
                            $this->setWidth($ratio['width']);
                            $this->setHeight($ratio['height']);
                            break;
                        }
                    }
                    // free up some memory
                    unset($ratios,$ratio);
                    break;
            }
        }
        return true;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $new_hpercent = @($height / $this->_oheight);
        $this->_percent['height'] = $new_hpercent * 100;
        $this->height = ceil($height);
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        $new_wpercent = @($width / $this->_owidth);
        $this->_percent['width'] = $new_wpercent * 100;
        $this->width = ceil($width);
        return $this->width;
    }

    public function getWidth2HeightRatio()
    {
        return $this->_owidth / $this->_oheight;
    }

    public function getHeight2WidthRatio()
    {
        return @($this->_oheight / $this->_owidth);
    }

    public function getPercent()
    {
        return $this->_percent;
    }

    public function setPercent($args)
    {
        if (!is_array($args) && is_numeric($args)) {
            return $this->_setPercent($args);
        }

        switch (count($args)) {
            case 1:
                switch (strtolower(key($args))) {
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
                        return false;
                }
                break;
            case 2:
                if (isset($args['wpercent']) && isset($args['hpercent'])) {
                    return $this->_setWxHPercent($args['wpercent'], $args['hpercent']);
                } else {
                    return false;
                }
                break;
            default:
                return false;
                break;
        }
    }

    public function _setPercent($percent)
    {
        $this->setHeight($this->_oheight * ($percent / 100));
        $this->_percent['height'] = $percent;

        $this->setWidth($this->_owidth * ($percent / 100));
        $this->_percent['width']  = $percent;
        return true;
    }

    public function _setWxHPercent($wpercent, $hpercent)
    {
        $this->setHeight($this->_oheight * ($hpercent / 100));
        $this->_percent['height'] = $hpercent;

        $this->setWidth($this->_owidth * ($wpercent / 100));
        $this->_percent['width']  = $wpercent;
        return true;
    }

    public function _setWPercent($wpercent)
    {
        $this->_percent['width'] = $wpercent;
        return $this->setWidth($this->_owidth * ($wpercent / 100));
    }

    public function _setHPercent($hpercent)
    {
        $this->_percent['height'] = $hpercent;
        return $this->setHeight($this->_oheight * ($hpercent / 100));
    }

    public function save()
    {
        if (!empty($this->_tmpFile) && file_exists($this->_tmpFile) && filesize($this->_tmpFile)) {
            if (@copy($this->_tmpFile, $this->fileLocation)) {
                @unlink($this->_tmpFile);
                return true;
            } else {
                @unlink($this->_tmpFile);
                return false;
            }
        }
        return true;
    }

    public function saveDerivative($fileName = '')
    {
        if (!empty($this->_tmpFile) && file_exists($this->_tmpFile) && filesize($this->_tmpFile)) {
            if (empty($fileName)) {
                // the image is stored in the database, i.e. no valid file location
                if (!file_exists($this->fileLocation) && !empty($this->_fileId)) {
                    // Use file id here
                    $fileName = $this->_fileId;
                } else {
                    // Use MD5 hash of file location here
                    $fileName = md5($this->fileLocation);
                }
                $derivName = $this->_thumbsdir . '/' . $fileName . "-{$this->width}x{$this->height}.jpg";
            } else {
                $derivName = $fileName;
            }

            if (@copy($this->_tmpFile, $derivName)) {
                @unlink($this->_tmpFile);
                return $derivName;
            } else {
                @unlink($this->_tmpFile);
                return null;
            }
        } else {
            return null;
        }
    }

    public function getDerivative($fileName = '')
    {
        if (empty($fileName)) {
            if ($this->width == $this->_owidth && $this->height == $this->_oheight) {
                // return the file location "as is" - could be in the database
                return $this->fileLocation;
            }
            // the image is stored in the database, i.e. no valid file location
            if (!file_exists($this->fileLocation) && !empty($this->_fileId)) {
                // Use file id here
                $fileName = $this->_fileId;
            } else {
                // Use MD5 hash of file location here
                $fileName = md5($this->fileLocation);
            }
            $derivName = $this->_thumbsdir . '/' . $fileName . "-{$this->width}x{$this->height}.jpg";
        } else {
            $derivName = $fileName;
        }

        if (file_exists($derivName)) {
            return $derivName;
        } else {
            return null;
        }
    }
}
