<?php

    sys::import('xaraya.structures.containers.blocks.basicblock');

    class Keywords_CloudBlock extends BasicBlock
    {
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'cloud';
    protected $module           = 'keywords'; // module block type belongs to, if any
    protected $text_type        = 'Keywords Cloud';  // Block type display name
    protected $text_type_long   = 'Display keywords cloud'; // Block type description
    // Additional info, supplied by developer, optional 
    protected $type_category    = 'block'; // options [(block)|group] 
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';
    
    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

        public $cloudtype           = 1;
        public $color               = '#000000';
        public $background          = '#FFFFFF';

        function display()
        {
            $vars = $this->getContent();
            $vars['tags'] = array();
            switch ($data['cloudtype']) {
                case 1:
                break;
                case 2:
                case 3:
                    $vars['tags'] = xarMod::apiFunc('keywords','user','getkeywordhits',array('cloudtype' => $data['cloudtype']));
                break;                
            }
            return $vars;
        }

        function modify()
        {
            $data = $this->getContent();
            
            $data['status'] = '';
            switch ($data['cloudtype']) {
                default:
                case 1:
                    if (!xarModIsAvailable('categories')) $data['status'] = 'not_available';
                    break;
                case 3:
                    if (!xarModIsAvailable('keywords')) $data['status'] = 'not_available';
                    break;
            }
            return $data;
        }

        public function update()
        {
            // Get the cloud type
            if (!xarVarFetch('cloudtype',  'int',      $vars['cloudtype'], $this->cloudtype, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('color',      'str:1:',   $vars['color'],          $this->color,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('background', 'str:1:',   $vars['background'],           $this->background,XARVAR_NOT_REQUIRED)) return;
            $this->setContent($vars);
            return true;
        }
    }
?>
