<?php
class KeywordsConfig extends ObjectDescriptor
{
    public $module;
    public $itemtype = 0;
    public $index_id;
    
    public $global_config = false;
    public $restrict_words = false;
    public $allow_manager_add = true;
    public $auto_tag_create = array();
    public $auto_tag_persist = false;
    public $meta_keywords = 0;
    public $meta_lang = '';

    public $config_state = '';
    
    public function __construct($module, $itemtype=0, $args=array())
    {
        parent::__construct($args);
        parent::refresh($this);
        $this->module = $module;
        $this->itemtype = $itemtype;
        self::__wakeup();
    }
    
    public function __wakeup()
    {
        $this->index_id = xarMod::apiFunc('keywords', 'index', 'getid', 
            array('module' => $this->module, 'itemtype' => $this->itemtype));
    }
    
    public function __sleep()
    {
        return array_keys($this->getPublicProperties());
    }
}
?>