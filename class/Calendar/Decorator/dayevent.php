<?php

    class DayEvent_Decorator extends Calendar_Decorator
    {
        //Calendar engine
        public $cE;
        public $tableHelper;

        public $year;
        public $month;
        public $day;
        public $firstDay = false;

        function build($events=array())
        {
            require_once CALENDAR_ROOT.'Hour.php';

            $this->cE = & $this->getEngine();
            $this->year  = $this->thisYear();
            $this->month = $this->thisMonth();
            $this->day = $this->thisDay();
            $hoursInDay = $this->cE->getHoursInDay($this->year, $this->month, $this->day);
            for ($i=0; $i < $hoursInDay; $i++) {
                $hour = new Calendar_Hour($this->year, $this->month, $this->day, $i);
                $this->children[$i] = new Event($hour);
            }
            if (count($events) > 0) {
                $this->setSelection($events);
            }
            return true;
        }


/*            include_once CALENDAR_ROOT . 'Day.php';
            include_once CALENDAR_ROOT .  'Table/Helper.php';
            $this->tableHelper = new Calendar_Table_Helper($this, $this->firstDay);
            $this->cE = & $this->getEngine();
            $this->year  = $this->thisYear();
            $this->month = $this->thisMonth();
            $this->day = $this->thisDay();

            $hoursInDay = $this->cE->getDaysInMonth($this->year, $this->month);
            for ($i=1; $i<=$daysInMonth; $i++) {
                $Day = new Calendar_Day(2000,1,1); // Create Day with dummy values
                $Day->setTimeStamp($this->cE->dateToStamp($this->year, $this->month, $i));
                $this->children[$i] = new Event($Day);
            }
            $this->calendar->children = $this->children;
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
*/

        function setSelection($events)
        {
            $hoursInDay = $this->cE->getHoursInDay($this->year, $this->month, $this->day);
            for ($i=1; $i<=$hoursInDay; $i++) {
                $stamp1 = $this->cE->dateToStamp($this->year, $this->month, $this->day, $i);
                $stamp2 = $this->cE->dateToStamp($this->year, $this->month, $this->day, $i+1);
                foreach ($events as $event) {
                if (($stamp1 <= $event['start'] && $stamp2 > $event['start'])) {
                        $this->children[$i]->addEntry1($event);
                        $this->children[$i]->setSelected();
                    }
                }
                /*
                    if (($stamp1 >= $event['start'] && $stamp1 < $event['end']) ||
                        ($stamp2 >= $event['start'] && $stamp2 < $event['end']) ||
                        ($stamp1 <= $event['start'] && $stamp2 > $event['end'])
                    ) {
                        $this->children[$i]->addEntry1($event);
                        $this->children[$i]->setSelected();
                    }
                }
                */
            }
        }

        function fetch()
        {
            if (empty($this->children)) return array();
            $child = each($this->children);
            if ($child) {
                return $child['value'];
            } else {
                reset($this->children);
                return false;
            }
        }
    }
?>