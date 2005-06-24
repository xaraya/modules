<?php
// $Id: getuserdatetimeinfo.php,v 1.2 2005/01/26 08:45:26 michelv01 Exp $

function julian_userapi_getUserDateTimeInfo()
{
    // dates come in as YYYYMMDD
    xarVarFetch('cal_date', 'str:4:8', $cal_date, xarLocaleFormatDate('%Y%m%d'));

    $bl_data = array();
   $bl_data['cal_date'] =& $cal_date;
    
    if(!preg_match('/([0-9]{4,4})([0-9]{2,2})?([0-9]{2,2})?/',$cal_date,$match)) {
        $year = gmdate('Y');
        $month = gmdate('m');
        $day = gmdate('d');
    } else {
        $year = $match[1];
        if(isset($match[2])) {
            $month=$match[2];
        } else {
            $month=gmdate('m');
        }
        if(isset($match[3])) {
            $day=$match[3];
        } else {
            $day=gmdate('d');
        }
    }
    
    $bl_data['selected_date']     = $year.$month.$day;
    $bl_data['selected_day']     = $day;
    $bl_data['selected_month']  = $month;
    $bl_data['selected_year']     = $year;
    $bl_data['selected_timestamp'] = gmmktime(0,0,0,$month,$day,$year);
    
    return $bl_data;
}

?>
