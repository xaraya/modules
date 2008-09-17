<?php

    class YearEvent_Decorator extends Calendar_Decorator
    {
        //Calendar engine
        public $cE;
        public $tableHelper;

        public $year;
        public $month = 1;
        public $day = 1;
        public $firstDay = false;

        function build($events=array(), $firstDay = null)
        {
            $this->year = $this->calendar->year;
            require_once CALENDAR_ROOT.'Factory.php';
            $this->calendar->firstDay = $this->calendar->defineFirstDayOfWeek($firstDay);
            $monthsInYear = $this->calendar->cE->getMonthsInYear($this->calendar->thisYear());
            for ($i=1; $i <= $monthsInYear; $i++) {
                $month = Calendar_Factory::create('Month', $this->year, $i);
                $MonthDecorator = new MonthEvent_Decorator($month);
                $MonthDecorator->build($events);
                $this->children[$i] = $MonthDecorator->calendar;
            }
            $this->calendar->children = $this->children;
            
            return true;
        }
    }
?>