<?php
    sys::import('xaraya.structures.containers.blocks.basicblock');

    class Weather_WeatherBlock extends BasicBlock
    {
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'weather';
    protected $module           = 'weather'; // module block type belongs to, if any
    protected $text_type        = 'Current Weather';  // Block type display name
    protected $text_type_long   = 'Show current weather conditions'; // Block type description
    // Additional info, supplied by developer, optional
    protected $type_category    = 'block'; // options [(block)|group]
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';

    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

        function display()
        {
            $data = $this->getContent();

            // Get variables from content block
            $w = xarModAPIFunc('weather','user','factory');
            $blocks = unserialize(xarModUserVars::get('math','locations'));
            try {
                $data['blockstoshow'] = xarModUserVars::get('math','locationnumber');
                $locations = $blocks['locations'];
                $data['wDataArray'] = array();
                foreach ($locations as $locationinfo) {
                    $location = unserialize($locationinfo);
                    if (empty($location['city']['code']))continue;
                    $w->setLocation($location['city']['code']);
                    $data['wDataArray'][] = $w->ccData();
                }
                if (empty($data['wDataArray'])) {
                    $data['blockstoshow'] = 1;
                    $location = unserialize(xarModUserVars::get('weather','default_location'));
                    $w->setLocation($location['city']['code']);
                    $data['wDataArray'] = array($w->ccData());
                }
                if (empty($data['wDataArray'])) $data['content'] = "";
            } catch (Exception $e) {
                $data['blockstoshow'] = 1;
                $location = unserialize(xarModUserVars::get('weather','default_location'));
                $w->setLocation($location['city']['code']);
                $data['wDataArray'] = array($w->ccData());
            }
            return $data;
        }
    }
?>