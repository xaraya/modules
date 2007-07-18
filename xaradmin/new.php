<?php

/**
 * Add an event or calendar.
 */

function ievents_admin_new($args)
{
    return xarModfunc('ievents', 'admin', 'modify', $args);
}

?>