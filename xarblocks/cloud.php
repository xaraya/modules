<?php

    sys::import('xaraya.structures.containers.blocks.basicblock');

    class Keywords_CloudBlock extends BasicBlock
    {
        public $name                = 'CloudBlock';
        public $module              = 'math';
        public $text_type           = 'Cloud';
        public $text_type_long      = 'Cloud Block';
        public $allow_multiple      = true;
        public $show_preview        = true;

        public $cloudtype           = 1;
        public $color               = '#000000';
        public $background          = '#FFFFFF';

        function display(Array $data=array())
        {
            $data = parent::display($data);
            
            $vars['color'] = $data['color'];
            $vars['background'] = $data['background'];
            $vars['tags'] = array();
            switch ($data['cloudtype']) {
                case 1:
                break;
                case 2:
                case 3:
                    $vars['tags'] = xarMod::apiFunc('keywords','user','getkeywordhits',array('cloudtype' => $data['cloudtype']));
                break;                
            }
            $data['content'] = $vars;
            return $data;
        }

        function modify(Array $data=array())
        {
            $data = parent::modify($data);
            
            if (empty($data['color'])) $data['color'] = $this->color;
            if (empty($data['background'])) $data['background'] = $this->background;
            
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

        public function update(Array $data=array())
        {
            $data = parent::update($data);

            // Get the cloud type
            if (!xarVarFetch('cloudtype',  'int',      $vars['cloudtype'], $this->cloudtype, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('color',      'str:1:',   $vars['color'],          $this->color,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('background', 'str:1:',   $vars['background'],           $this->background,XARVAR_NOT_REQUIRED)) return;
            $data['content'] = $vars;

            return $data;
        }
    }
?>
