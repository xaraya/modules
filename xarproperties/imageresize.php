<?php
sys::import('modules.base.xarproperties.image');
/**
 * Handle the image resize property
 */
class ImageResizeProperty extends ImageProperty
{
    public $id         = 30078;
    public $name       = 'imageresize';
    public $desc       = 'ImageResize';
    public $reqmodules = array('images');

    public $imagetext  = 'no image';

    // this is used by DataPropertyMaster::addProperty() to set the $object->upload flag
    public $upload = false;

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->tplmodule = 'images';
//        $this->template = 'imageresize';
        $this->filepath   = 'modules/images/xarproperties';
    }

    public function validateValue($value = null)
    {
        if (!parent::validateValue($value)) return false;

        return true;
    }

    public function showOutput(Array $data = array())
    {
        if(!empty($data['inputtype'])) $this->initialization_image_source = $data['inputtype'];
        if(!empty($data['basedir'])) $this->initialization_basedirectory = $data['basedir'];
        if (empty($data['value'])) $data['value'] = $this->value;
        if (($this->initialization_image_source == 'local') || ($this->initialization_image_source == 'upload')) {
            $data['value'] = $this->initialization_basedirectory . "/" . $data['value'];
        }
        if (!empty($data['src'])) $data['value'] = $data['src'];
        if (empty($data['imagetext'])) $data['imagetext'] = $this->imagetext;
        if (empty($data['label'])) $data['label'] = $this->imagealt;
        if (empty($data['height']) && empty($data['width'])) {
            try {
                $sizeinfo = getimagesize($data['src']);
                $data['width'] = $sizeinfo[0] . "px";
                $data['height'] = $sizeinfo[1] . "px";
            } catch (Exception $e) {}
        }
        $data = array_merge($data,xarModAPIFunc('images', 'user', 'resize', $data));
        if (isset($data['url'])) $data['value'] = $data['url'];
        if (isset($sizeinfo)) {
            $data['width'] = $sizeinfo[0];
            $data['height'] = $sizeinfo[1];
        }
        return parent::showOutput($data);
    }

}
?>
