<?php

function ievents_user_main($args)
{
    echo "<h1>TESTING</h1>";
    echo "<h2>Calendars</h2>";

    $calendars = xarModAPIfunc('ievents', 'user', 'getcalendars',
        array(
            'event_priv' => 'OVERVIEW',
            'cids' => array(1, 3),
            'status' => array('INACTIVE', 'ACTIVE'),
        )
    );
    echo "<pre>"; var_dump($calendars); echo "</pre>";

    echo "<h2>Events</h2>";
    $events = xarModAPIfunc('ievents', 'user', 'getevents',
        array(
            'cids' => array(1, 3),
            //'fieldlist' => 'contact_email',
            //'docount' => true,
            //'created_by' => 'myself',
            //'eid' => 1,
            //'startdate' => 1177346646,
            //'enddate' => 1177346647,
            //'drule' => 'overlap',
            //'external_source' => 'J\'J',
            //'flags' => 'A,B,f',
        )
    );
    echo "<pre>"; var_dump($events); echo "</pre>";

    echo "TODO";
}

?>