<?php
/**
 * Count all events.
 */

function ievents_userapi_countevents($args)
{
    // We don't want startnum and numitems set
    if (isset($args['startnum'])) unset($args['startnum']);
    if (isset($args['numitems'])) unset($args['numitems']);

    // Indicate to getevents that want to count
    $args['docount'] = true;

    // Hand the counting off to the getevents API.
    $count = xarModAPIFunc('ievents', 'user', 'getevents', $args);
    return $count;
}

?>