<?php

/**
 * Menu links for main menu.
 */

function ievents_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('OverviewIEvent')) {

        // Overview of the calendars.
        $menulinks[] = Array(
            'url'   => xarModURL('ievents', 'user', 'viewcals'),
            'title' => xarML('View Calendars'),
            'label' => xarML('View Calendars'),
        );

        // Overview of events (the main listings)
        $menulinks[] = Array(
            'url'   => xarModURL('ievents', 'user', 'view'),
            'title' => xarML('View Events'),
            'label' => xarML('View Events'),
        );

        // If user is allowed to submit new events, then provide a link.
        if (xarSecurityCheck('CommentIEvent', 0, 'IEvent', 'All:All:All')) {
            $menulinks[] = Array(
                'url'   => xarModURL('ievents', 'user', 'modify'),
                'title' => xarML('New Event'),
                'label' => xarML('New Event'),
            );
        }


    }

    return $menulinks;
}

?>