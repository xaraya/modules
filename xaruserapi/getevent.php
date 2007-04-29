<?php

/**
 * Get a single event.
 *
 * @todo If we fetch more than one event, perhaps we need to raise an error.
 */

function ievents_userapi_getevent($args)
{
    $args['numitems'] = 2;

    $events = xarModAPIfunc('ievents', 'user', 'getevents', $args);

    if (!empty($events)) {
        $event = reset($events);
    } else {
        $event = array();
    }

    return $event;
}

?>