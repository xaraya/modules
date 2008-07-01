<?php

    include_once(CALENDAR_ROOT.'Month/Weekdays.php');
    sys::import('modules.query.class.query');

    function calendar_user_month()
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');

        $MonthEvents = new Calendar_Month_Weekdays(
            $data['cal_year'],
            $data['cal_month'] + 1,
            xarModVars::get('calendar', 'cal_sdow'));
        $end_time = $MonthEvents->getTimestamp();
        $MonthEvents = new Calendar_Month_Weekdays(
            $data['cal_year'],
            $data['cal_month'],
            xarModVars::get('calendar', 'cal_sdow'));
        $start_time = $MonthEvents->getTimestamp();

        $q = new Query('SELECT');
        $a[] = $q->plt('start_time',$start_time);
        $a[] = $q->pge('end_time',$start_time);
        $b[] = $q->plt('start_time',$end_time);
        $b[] = $q->pge('end_time',$end_time);
        $c[] = $q->pgt('start_time',$start_time);
        $c[] = $q->ple('end_time',$end_time);

        $d[] = $q->pqand($a);
        $d[] = $q->pqand($b);
        $d[] = $q->pqand($c);
        $q->qor($d);
        $data['conditions'] = $q;

        return $data;
    }

?>