<?php

function calendar_user_submit()
{
    xarVarFetch('cal_sdow','int:0:6',$cal_sdow,0);
    xarVarFetch('cal_date','int::',$cal_date,0);

    $c = xarModAPIFunc('calendar','user','factory','calendar');
    $c->setStartDayOfWeek($cal_sdow);

    $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
    $data['cal_sdow'] =& $c->getStartDayOfWeek();
    $data['shortDayNames'] =& $c->getShortDayNames($c->getStartDayOfWeek());
    $data['mediumDayNames'] =& $c->getMediumDayNames($c->getStartDayOfWeek());
    $data['longDayNames'] =& $c->getLongDayNames($c->getStartDayOfWeek());
    $data['calendar'] =& $c;

    // return the event data
    xarVarFetch('event_id','int::',$event_id,0);
    $e = xarModAPIFunc('calendar','user','factory','event');
    $e->buildEvent($event_id);
    // remember to pass in the existing array so it can be appended too
    $e->getEventDataForBL($data);

    // echo the content to the screen
    return $data;
}

?>
