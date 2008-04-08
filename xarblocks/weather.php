<?php
    sys::import('xaraya.structures.containers.blocks.basicblock');

    class WeatherBlock extends BasicBlock
    {
        public $no_cache            = 1;

        public $name                = 'WeatherBlock';
        public $module              = 'weather';
        public $text_type           = 'Current';
        public $text_type_long      = 'Current Conditions';
        public $allow_multiple      = true;
        public $show_preview        = true;

        function display(Array $data=array())
        {
            $data = parent::display($data);
            if(!xarSecurityCheck('ReadWeatherBlock',1,'Block',"All:" . $data['name'] . ":All",'All')) return;
            
            // Get variables from content block
            $w = xarModAPIFunc('weather','user','factory');
            $blocks = unserialize(xarModUserVars::get('math','locations'));
            try {
                $data['content']['blockstoshow'] = xarModUserVars::get('math','locationnumber');
                $locations = $blocks['locations'];
                $data['content']['wDataArray'] = array();
                foreach ($locations as $locationinfo) {
                    $location = unserialize($locationinfo);
                    if (empty($location['city']['code']))continue;
                    $w->setLocation($location['city']['code']);
                    $data['content']['wDataArray'][] = $w->ccData();
                }
            } catch (Exception $e) {
                $data['content']['blockstoshow'] = 1;
                $location = unserialize(xarModUserVars::get('weather','default_location'));
                $w->setLocation($location['city']['code']);
                $data['content']['wDataArray'] = array($w->ccData());
            }
            return $data;
        }
    }
?>