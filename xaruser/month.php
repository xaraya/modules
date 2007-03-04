<?php

    include_once(CALENDAR_ROOT.'Month/Weekdays.php');
    include_once(CALENDAR_ROOT.'Day.php');

    sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");
    sys::import("modules.calendar.class.Calendar.Decorator.Event");

    function calendar_user_month()
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
        $Month = new Calendar_Month_Weekdays(
            $data['cal_year'],
            $data['cal_month'],
            CALENDAR_FIRST_DAY_OF_WEEK);

        $args = array(
            'day' => &$Day,
        );
        $events = xarModAPIFunc('calendar','user','getevents',$args);

        $Month->build();
        $data['Month'] =& $Month;
        $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
        return $data;
    }

/*    class MonthEvent_Decorator extends Calendar_Decorator
    {
        //Calendar engine
        var $cE;
        var $tableHelper;

        var $year;
        var $month;
        var $firstDay = false;

        function build($events=array())
        {
            include_once CALENDAR_ROOT . 'Day.php';
            include_once CALENDAR_ROOT .  'Table/Helper.php';

            $this->tableHelper = & new Calendar_Table_Helper($this, $this->firstDay);
            $this->cE = & $this->getEngine();
            $this->year  = $this->thisYear();
            $this->month = $this->thisMonth();

            $daysInMonth = $this->cE->getDaysInMonth($this->year, $this->month);
            for ($i=1; $i<=$daysInMonth; $i++) {
                $Day = new Calendar_Day(2000,1,1); // Create Day with dummy values
                $Day->setTimeStamp($this->cE->dateToStamp($this->year, $this->month, $i));
                $this->children[$i] = new DiaryEvent($Day);
            }
            if (count($events) > 0) {
                $this->setSelection($events);
            }
            Calendar_Month_Weekdays::buildEmptyDaysBefore();
            Calendar_Month_Weekdays::shiftDays();
            Calendar_Month_Weekdays::buildEmptyDaysAfter();
            Calendar_Month_Weekdays::setWeekMarkers();
            return true;
        }

        function setSelection($events)
        {
            $daysInMonth = $this->cE->getDaysInMonth($this->year, $this->month);
            for ($i=1; $i<=$daysInMonth; $i++) {
                $stamp1 = $this->cE->dateToStamp($this->year, $this->month, $i);
                $stamp2 = $this->cE->dateToStamp($this->year, $this->month, $i+1);
                foreach ($events as $event) {
                    if (($stamp1 >= $event['start'] && $stamp1 < $event['end']) ||
                        ($stamp2 >= $event['start'] && $stamp2 < $event['end']) ||
                        ($stamp1 <= $event['start'] && $stamp2 > $event['end'])
                    ) {
                        $this->children[$i]->addEntry($event);
                        $this->children[$i]->setSelected();
                    }
                }
            }
        }

        function fetch()
        {
            $child = each($this->children);
            if ($child) {
                return $child['value'];
            } else {
                reset($this->children);
                return false;
            }
        }
    }
*/
?>