<?php

    class MonthEvent_Decorator extends Calendar_Decorator
    {
        //Calendar engine
        public $cE;
        public $tableHelper;

        public $year;
        public $month;
        public $day =1;
        public $firstDay = false;

        function build($events=array())
        {
            include_once CALENDAR_ROOT .  'Table/Helper.php';
            $this->tableHelper = new Calendar_Table_Helper($this->calendar, $this->firstDay);
            $this->cE = & $this->getEngine();
            $this->month = $this->calendar->month;
            $this->year = $this->calendar->year;

            $daysInMonth = $this->cE->getDaysInMonth($this->year, $this->month);
            for ($i=1; $i<=$daysInMonth; $i++) {
                $Day = new Calendar_Day($this->calendar->year, $this->calendar->month, $i);
                $this->calendar->children[$i] = new Event($Day);
            }
            if (count($events) > 0) {
                $this->setSelection($events);
            }
            $this->calendar->tableHelper = & $this->tableHelper;
            $this->calendar->buildEmptyDaysBefore();
            $this->calendar->shiftDays();
            $this->calendar->buildEmptyDaysAfter();
            $this->calendar->setWeekMarkers();
            return true;
        }

        function setSelection($events)
        {
            $daysInMonth = $this->cE->getDaysInMonth($this->year, $this->month);
            for ($i=1; $i<=$daysInMonth; $i++) {
                $stamp1 = $this->cE->dateToStamp($this->year, $this->month, $i);
                $stamp2 = $this->cE->dateToStamp($this->year, $this->month, $i+1);
                foreach ($events as $event) {
                    $end_time = $event['start_time'] + $event['duration'];
                    if (($stamp1 >= $event['start_time'] && $stamp1 < $end_time) ||
                        ($stamp2 >= $event['start_time'] && $stamp2 < $end_time) ||
                        ($stamp1 <= $event['start_time'] && $stamp2 > $end_time)
                    ) {
                        $this->calendar->children[$i]->addEntry1($event);
                        $this->calendar->children[$i]->setSelected();
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