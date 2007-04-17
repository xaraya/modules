<?php

    class WeekEvent_Decorator extends Calendar_Decorator
    {
        //Calendar engine
        var $cE;
        var $tableHelper;

        var $year;
        var $month;
        var $day =1;
        var $firstDay = false;

        function build($events=array())
        {
            /*include_once CALENDAR_ROOT . 'Day.php';
            include_once CALENDAR_ROOT .  'Table/Helper.php';
            $this->tableHelper = new Calendar_Table_Helper($this, $this->firstDay);
            $this->cE = & $this->getEngine();
            $this->year  = $this->thisYear();
            $this->month = $this->thisMonth();
*/
//            require_once CALENDAR_ROOT.'Day.php';
            include_once CALENDAR_ROOT .  'Table/Helper.php';
            $this->tableHelper = new Calendar_Table_Helper($this, $this->firstDay);
            $this->cE = & $this->getEngine();
            $this->year  = $this->cE->stampToYear($this->calendar->thisWeek);
            $this->month = $this->cE->stampToMonth($this->calendar->thisWeek);
            $this->day   = $this->cE->stampToDay($this->calendar->thisWeek);
            $end   = $this->cE->getDaysInWeek(
                $this->year,
                $this->month,
                $this->day
            );

            /*
            $daysInMonth = $this->cE->getDaysInMonth($this->year, $this->month);
            for ($i=1; $i<=$daysInMonth; $i++) {
                $Day = new Calendar_Day(2000,1,1); // Create Day with dummy values
                $Day->setTimeStamp($this->cE->dateToStamp($this->year, $this->month, $i));
                $this->children[$i] = new Event($Day);
            }
            */

            for ($i=1; $i <= $end; $i++) {
                $Day = new Calendar_Day(2000,1,1); // Create Day with dummy values
                $Day->setTimeStamp($this->cE->dateToStamp($this->year, $this->month, $this->day++));
                $this->children[$i] = new Event($Day);

            /*
                $stamp = $this->cE->dateToStamp($year, $month, $day++);
                $this->children[$i] = new Calendar_Day(
                                    $this->cE->stampToYear($stamp),
                                    $this->cE->stampToMonth($stamp),
                                    $this->cE->stampToDay($stamp));
                                    */
            }

            $this->calendar->children = $this->children;
            if (count($events) > 0) {
                $this->setSelection($events);
            }

            //set empty days (@see Calendar_Month_Weeks::build())
            if ($this->calendar->firstWeek) {
                $eBefore = $this->tableHelper->getEmptyDaysBefore();
                for ($i=1; $i <= $eBefore; $i++) {
                    $this->children[$i]->setEmpty();
                }
            }
            if ($this->calendar->lastWeek) {
                $eAfter = $this->tableHelper->getEmptyDaysAfterOffset();
                for ($i = $eAfter+1; $i <= $end; $i++) {
                    $this->children[$i]->setEmpty();
                }
            }

            return true;

        }

        function setSelection($events)
        {
            $this->cE =& $this->getEngine();
            $year  = $this->cE->stampToYear($this->calendar->thisWeek);
            $month = $this->cE->stampToMonth($this->calendar->thisWeek);
            $day   = $this->cE->stampToDay($this->calendar->thisWeek);
            $end   = $this->cE->getDaysInWeek(
                $this->thisYear(),
                $this->thisMonth(),
                $this->thisDay()
            );

            for ($i=1; $i<=$end; $i++) {
                $stamp1 = $this->cE->dateToStamp($this->year, $this->month, $day);
                $stamp2 = $this->cE->dateToStamp($this->year, $this->month, $day+1);
                foreach ($events as $event) {
                    if (($stamp1 >= $event['start'] && $stamp1 < $event['end']) ||
                        ($stamp2 >= $event['start'] && $stamp2 < $event['end']) ||
                        ($stamp1 <= $event['start'] && $stamp2 > $event['end'])
                    ) {
                        $this->children[$i]->addEntry1($event);
                        $this->children[$i]->setSelected();
                    }
                }
                $day++;
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