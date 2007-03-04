<?php
function calendar_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminCalendar',0)) {
        $menulinks[] = Array(
            'url'=>xarModURL('calendar','admin','view'),
            'title'=>xarML('View calendars'),
            'label'=>xarML('View calendars')
            );
        $menulinks[] = Array(
            'url'=>xarModURL('calendar','admin','viewevents'),
            'title'=>xarML('View events'),
            'label'=>xarML('View events')
            );
        $menulinks[] = Array(
            'url'=>xarModURL('calendar','admin','modifyconfig'),
            'title'=>xarML('Modify the configuration for Calendar'),
            'label'=>xarML('Modify Config')
            );

    /*
        $menulinks[] = Array(
            'url'=>xarModURL('calendar','admin','add_event'),
            'title'=>xarML('Add a new calendar event'),
            'label'=>xarML('Add event')
            );
        $menulinks[] = Array(
            'url'=>xarModURL('calendar','admin','view'),
            'title'=>xarML('View queued events'),
            'label'=>xarML('View Queue')
            );
        */
    }
    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
