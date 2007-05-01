<?php

/**
 * Modify or add an event.
 */

function ievents_admin_modify($args)
{
    return xarModfunc('ievents', 'user', 'modifyevent', $args);
}

?>