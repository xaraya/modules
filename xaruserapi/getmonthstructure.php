<?php

function calendar_userapi_getmonthstructure($args=array()) 
{
    extract($args); unset($args);
    if(!isset($month)) return;
    if(!isset($year)) return;
    
    xarVarValidate('int:1:12', $month);
    xarVarValidate('int::', $year);
    xarVarFetch('cal_sdow','int:0:6',$cal_sdow,0);
    
    $c = xarModAPIFunc('calendar','user','factory','calendar');
    $c->setStartDayOfWeek($cal_sdow);
    // echo the content to the screen
    return $c->getCalendarMonth($year.$month);
}

?>
