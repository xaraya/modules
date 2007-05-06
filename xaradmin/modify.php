<?php

/**
 * Modify or add an event.
 */

function ievents_admin_modify($args)
{
    if (!isset($args['itemid']) && !empty($args['eid'])) $args['itemid'] = $args['eid'];

    return xarModfunc('ievents', 'user', 'modifyevent', $args);
}

?>