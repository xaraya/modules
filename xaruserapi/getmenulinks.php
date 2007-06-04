<?php

/**
 * Menu links for main menu.
 */

function ievents_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('OverviewIEvent')) {
        // Overview of events (the main listings)
        $menulinks[] = Array(
            'url'   => xarModURL('ievents', 'user', 'view'),
            'title' => xarML('View events'),
            'label' => xarML('View events'),
        );

        // If user is allowed to submit new events, then provide a link.
        if (xarSecurityCheck('CommentIEvent', 0, 'IEvent', 'All:All:All')) {
            $menulinks[] = Array(
                'url'   => xarModURL('ievents', 'user', 'modify'),
                'title' => xarML('New event'),
                'label' => xarML('New event'),
            );
        }

        // Overview of the calendars.
        $menulinks[] = Array(
            'url'   => xarModURL('ievents', 'user', 'viewcals'),
            'title' => xarML('View calendars'),
            'label' => xarML('View calendars'),
        );
    }

    return $menulinks;
}

?>