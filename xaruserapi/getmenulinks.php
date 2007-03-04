<?php
function calendar_userapi_getmenulinks()
{
    xarVarFetch('cal_sdow','int::',$cal_sdow,xarModGetUserVar('calendar','cal_sdow'));
    xarVarFetch('cal_date','int::',$cal_date,xarLocaleFormatDate('%Y%m%d'));

    $menulinks[] = array('url'   => xarModURL('calendar','user','day',array('cal_date'=>$cal_date)),
                         'title' => xarML('Day'),
                         'label' => xarML('Day'));

    $menulinks[] = array('url'   => xarModURL('calendar','user','week',array('cal_date'=>$cal_date)),
                         'title' => xarML('Week'),
                         'label' => xarML('Week'));

    $menulinks[] = array('url'   => xarModURL('calendar','user','month',array('cal_date'=>$cal_date)),
                         'title' => xarML('Month'),
                         'label' => xarML('Month'));

    $menulinks[] = array('url'   => xarModURL('calendar','user','year',array('cal_date'=>$cal_date)),
                         'title' => xarML('Year'),
                         'label' => xarML('Year'));

    if(xarUserIsLoggedIn()) {
        $menulinks[] = array('url' => xarModURL('calendar','user','modifyconfig'),
                             'title' => xarML('Modify Config'),
                             'label' => xarML('Modify Config'));
    }

    return $menulinks;

}
?>
