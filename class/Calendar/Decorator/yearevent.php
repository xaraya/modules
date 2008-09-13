<?php

    class YearEvent_Decorator extends Calendar_Decorator
    {
        public $cE;
        public $tableHelper;
        public $firstDay = false;

        function build($events=array())
        {

        function build($sDates = array(), $firstDay = null)
        {
            include_once CALENDAR_ROOT . 'Day.php';
            include_once CALENDAR_ROOT .  'Table/Helper.php';
            $this->tableHelper = new Calendar_Table_Helper($this, $this->firstDay);
            $this->cE = & $this->getEngine();
            require_once CALENDAR_ROOT.'Factory.php';
            $this->firstDay = $this->defineFirstDayOfWeek($firstDay);
            $monthsInYear = $this->cE->getMonthsInYear($this->thisYear());
            for ($i=1; $i <= $monthsInYear; $i++) {
                $month = Calendar_Factory::create('Month', $this->year, $i);
                $start_time = $month->getTimestamp();
                $month = Calendar_Factory::create('Month', $this->year, $i+1);
                $end_time = $month->getTimestamp();
                $events = $this->getEvents($start_time, $end_time, $role_id); 
                $MonthDecorator = new MonthEvent_Decorator($month);
                $MonthDecorator->build($events);

                $this->children[$i] = MonthDecorator;
            }
            if (count($sDates) > 0) {
                $this->setSelection($sDates);
            }
            return true;
        }

        public function getEvents($start_time, $end_time, $role_id)
        {
            // get all the events. need to improve this query and combine it with the query in the template
            $xartable = xarDB::getTables();
            $q = new Query('SELECT', $xartable['calendar_event']);
            $q->ge('start_time', $start_time);
            $q->lt('start_time', $end_time);
            $q->eq('role_id',$role_id);
    //        $q->qecho();
            if (!$q->run()) return;
            return $q->output();
        }
    }
?>