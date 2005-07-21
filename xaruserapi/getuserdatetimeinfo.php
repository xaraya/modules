<?php
// $Id: getuserdatetimeinfo.php,v 1.3 2003/06/24 20:58:06 roger Exp $

function calendar_userapi_getUserDateTimeInfo()
{
    // dates come in as YYYYMMDD
    xarVarFetch('cal_date', 'str:4:8', $cal_date, xarLocaleFormatDate('%Y%m%d'));

    $bl_data = array();
    $bl_data['cal_date'] =& $cal_date;

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

    //$bl_data['selected_date']   = (int) $year.$month.$day;
    $bl_data['cal_day']    = (int) $day;
    $bl_data['cal_month']  = (int) $month;
    $bl_data['cal_year']   = (int) $year;
    //$bl_data['selected_timestamp'] = gmmktime(0,0,0,$month,$day,$year);

    return $bl_data;
}

?>
