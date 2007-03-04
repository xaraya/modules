<?php

function calendar_userapi_getUserDateTimeInfo()
{
    // dates come in as YYYYMMDD
    xarVarFetch('cal_date', 'str:4:8', $cal_date, xarLocaleFormatDate('%Y%m%d'));

    $data = array();
    $data['cal_date'] =& $cal_date;

    if(!preg_match('/([\d]{4,4})([\d]{2,2})?([\d]{2,2})?/',$cal_date,$match)) {
        $year = xarLocaleFormateDate('Y');
        $month = xarLocaleFormateDate('m');
        $day = xarLocaleFormateDate('d');
    } else {
        $year = $match[1];
        if(isset($match[2])) {
            $month=$match[2];
        } else {
            $month='01';
        }
        if(isset($match[3])) {
            $day=$match[3];
        } else {
            $day='01';
        }
    }

    //$data['selected_date']   = (int) $year.$month.$day;
    $data['cal_day']    = (int) $day;
    $data['cal_month']  = (int) $month;
    $data['cal_year']   = (int) $year;
    //$data['selected_timestamp'] = gmmktime(0,0,0,$month,$day,$year);

    sys::import('xaraya.structures.datetime');
    $today = new XarDateTime();
    $usertz = xarModGetUserVar('roles','usertimezone',xarSessionGetVar('uid'));
    $useroffset = $today->getTZOffset($usertz);
    $data['now'] = getdate(time() + $useroffset);
    return $data;
}

?>
