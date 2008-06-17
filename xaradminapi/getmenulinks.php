<?php
function calendar_adminapi_getmenulinks()
{
    $menulinks = array();
    if (xarSecurityCheck('AdminCalendar',0)) {
        $menulinks[] = array('url'   => xarModURL('calendar',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('Manage the Master Tables  of this module'),
                              'label' => xarML('Master Tables'));
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

    return $menulinks;
}
?>
