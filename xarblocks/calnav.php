<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

//sys::import('xaraya.structures.descriptor');
//sys::import('modules.calendar.class.calnav');

    sys::import('xaraya.structures.containers.blocks.basicblock');

    class Calendar_CalnavBlock extends BasicBlock implements iBlock
    {

        public $name                = 'CalnavBlock';
        public $module              = 'calendar';
        public $text_type           = 'Calnav';
        public $text_type_long      = 'Calnav selection';
        public $allow_multiple      = true;

        public $nocache             = 1;

/**
 * Display func.
 * @param $blockinfo array
 * @returns $blockinfo array
 */

        function display(Array $data=array())
        {
            $data = parent::display($data);
            if (empty($data)) return;

            if (!defined('CALENDAR_ROOT')) {
                define('CALENDAR_ROOT', xarModVars::get('calendar','pearcalendar_root'));
            }
            include_once(CALENDAR_ROOT.'Calendar.php');


            $tplData['form_action'] = xarController::URL('calendar', 'user', 'changecalnav');
            $tplData['blockid'] = $data['bid'];

            if (xarServer::getVar('REQUEST_METHOD') == 'GET') {
                // URL of this page
                $tplData['return_url'] = xarServer::getCurrentURL();
            } else {
                // Base URL of the site
                $tplData['return_url'] = xarServer::getBaseURL();
            }

            $data['content'] = $tplData;

            return $data;
        }
    }
?>