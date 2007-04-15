<?php
function calendar_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminCalendar',0)) {
        $menulinks[] = array('url'   => xarModURL('calendar',
                                                  'admin',
                                                  'view',
                                                  array('name' => 'calendar_calendar')),
                                                                  'title' => xarML('View the calendars'),
                                                                  'label' => xarML('Calendars'));
        $menulinks[] = array('url'   => xarModURL('calendar',
                                                  'admin',
                                                  'view',
                                                  array('name' => 'calendar_event')),
                                                                  'title' => xarML('View the events'),
                                                                  'label' => xarML('Events'));
        $menulinks[] = array('url'   => xarModURL('calendar',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration settings'),
                              'label' => xarML('Modify Config'));

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
