<?php

    class YearEvent_Decorator extends Calendar_Decorator
    {
        //Calendar engine
        public $cE;
        public $tableHelper;

        public $year;
        public $month = 1;
        public $day =1;
        public $firstDay = false;

        function build($events=array())
        {
            include_once CALENDAR_ROOT . 'Day.php';
            include_once CALENDAR_ROOT .  'Table/Helper.php';
            $this->tableHelper = new Calendar_Table_Helper($this, $this->firstDay);
            $this->cE = & $this->getEngine();
            $this->year  = $this->thisYear();

            $daysInYear = $this->year % 4 == 0 ? 366 : 365;
            for ($i=1; $i<=$daysInYear; $i++) {
                $Day = new Calendar_Day(2000,1,1); // Create Day with dummy values
                $Day->setTimeStamp($this->cE->dateToStamp($this->year, $this->month, $i));
                $this->children[$i] = new Event($Day);
            }
            $this->calendar->children = $this->children;
            if (count($events) > 0) {
                $this->setSelection($events);
            }
            /*
            $this->calendar->tableHelper = & $this->tableHelper;
            $this->calendar->buildEmptyDaysBefore();
            $this->calendar->shiftDays();
            $this->calendar->buildEmptyDaysAfter();
            $this->calendar->setWeekMarkers();
            */
            return true;
        }

        function setSelection($events)
        {
            $daysInYear = $this->year % 4 == 0 ? 366 : 365;
            for ($i=1; $i<=$daysInYear; $i++) {
                $stamp1 = $this->cE->dateToStamp($this->year, $this->month, $i);
                $stamp2 = $this->cE->dateToStamp($this->year, $this->month, $i+1);
                foreach ($events as $event) {
                    $end_time = $event['start_time'] + $event['duration'];
                    if (($stamp1 >= $event['start_time'] && $stamp1 < $end_time) ||
                        ($stamp2 >= $event['start_time'] && $stamp2 < $end_time) ||
                        ($stamp1 <= $event['start_time'] && $stamp2 > $end_time)
                    ) {
                        $this->children[$i]->addEntry1($event);
                        $this->children[$i]->setSelected();
                    }
                }
            }
        }

        function fetch()
        {
            if (empty($this->calendar->children)) return array();
            $child = each($this->calendar->children);
            if ($child) {
                return $child['value'];
            } else {
                reset($this->calendar->children);
                return false;
            }
        }
    }
?>