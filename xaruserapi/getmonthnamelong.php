<?php

function calendar_userapi_getMonthNameLong($args)
{
    extract($args); unset($args);
    if(!isset($month)) $month = date('m');

    // make sure we have a valid month value
    if(!xarVarValidate('int:1:12',$month)) {
        return;
    }
    $c = xarModAPIFunc('calendar','user','factory','calendar');
    return $c->MonthLong($month);
}

?>
