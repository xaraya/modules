<?php

    sys::import('xaraya.structures.containers.blocks.basicblock');

    class MonthBlock extends BasicBlock
    {
        public $name                = 'Month';
        public $module              = 'calendar';
        public $text_type_long      = 'Month selection';
        public $show_preview        = true;
        public $no_cache            = 1;        // don't cache by default
        public $usershared          = 0;        // don't share across users
        public $cacheexpire         = null;

        function display(Array $data=array())
        {
            $data = parent::display($data);

            if (!defined('CALENDAR_ROOT')) {
                define('CALENDAR_ROOT', xarModVars::get('calendar','pearcalendar_root'));
            }
            include_once(CALENDAR_ROOT.'Month/Weekdays.php');
            include_once(CALENDAR_ROOT.'Decorator/Textual.php');
            sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");

            // Build the month
            $data['content'] = xarModAPIFunc('calendar','user','getuserdatetimeinfo');
            $data['content']['MonthCal'] = new Calendar_Month_Weekdays(
                $data['content']['cal_year'],
                $data['content']['cal_month'],
                CALENDAR_FIRST_DAY_OF_WEEK);
            $data['content']['MonthCal']->build();

            return $data;            
        }
    }

?>
