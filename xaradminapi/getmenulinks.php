<?php
function calendar_adminapi_getmenulinks()
{

    $menulinks = '';

    $menulinks[] = Array(
        'url'=>xarModURL('calendar','admin','view'),
        'title'=>xarML('View calendars'),
        'label'=>xarML('View calendars')
        );
    $menulinks[] = Array(
        'url'=>xarModURL('calendar','admin','add'),
        'title'=>xarML('Add calendars'),
        'label'=>xarML('Add calendars')
        );
    $menulinks[] = Array(
        'url'=>xarModURL('calendar','admin','add_event'),
        'title'=>xarML('Add a new calendar event'),
        'label'=>xarML('Add event')
        );
    $menulinks[] = Array(
        'url'=>xarModURL('calendar','admin','modifyconfig'),
        'title'=>xarML('Modify the configuration for Calendar'),
        'label'=>xarML('Modify Config')
        );

/*
    $menulinks[] = Array(
        'url'=>xarModURL('calendar','admin','view'),
        'title'=>xarML('View queued events'),
        'label'=>xarML('View Queue')
        );
    */

    return $menulinks;
}
?>
