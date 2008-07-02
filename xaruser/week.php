<?php

    include_once(CALENDAR_ROOT.'Week.php');
    sys::import('modules.query.class.query');

    function calendar_user_week()
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');

        $WeekEvents = new Calendar_Week($data['cal_year'],$data['cal_month'],$data['cal_day'],CALENDAR_FIRST_DAY_OF_WEEK);

        $start_time = $WeekEvents->thisWeek;
        $end_time = $WeekEvents->nextWeek;
        
        $q = new Query('SELECT');
        $a[] = $q->plt('start_time',$start_time);
        $a[] = $q->pge('start_time + duration',$start_time);
        $b[] = $q->plt('start_time',$end_time);
        $b[] = $q->pge('start_time + duration',$end_time);
        $c[] = $q->pgt('start_time',$start_time);
        $c[] = $q->ple('start_time + duration',$end_time);

        $d[] = $q->pqand($a);
        $d[] = $q->pqand($b);
        $d[] = $q->pqand($c);
        $q->qor($d);

        $q->eq('role_id',xarSession::getVar('role_id'));
        $q->qecho();
        $data['conditions'] = $q;

        return $data;
    }
?>