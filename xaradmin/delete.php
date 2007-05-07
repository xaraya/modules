<?php

/**
 * Delete an event or calendar.
 */

function ievents_admin_delete($args)
{
    // Can be called up using the itemtype and itemid
    list($itemtype_calendars, $itemtype_events) = 
        xarModAPIfunc('ievents', 'user', 'params', 
            array('names' => 'itemtype_calendars,itemtype_events')
        );

    if (!empty($itemtype)) {
        if ($itemtype != $itemtype_events && $itemtype != $itemtype_calendars) {
            // Unknown itemtype - raise an error.
            if (empty($object)) {
                $data['message'] = xarML('Unknown or invalid itemtype #(1)', $args['itemtype']);
                return $data;
            }
        }
    } else {
        // Assume we are deleting an event.
        $itemtype = $itemtype_events;
    }

    // Pass control over to the main GUI.
    if ($itemtype == $itemtype_events) {
        // Deleting an event
        if (isset($args['itemid']) && empty($args['eid'])) $args['eid'] = $args['itemid'];
        return xarModfunc('ievents', 'user', 'delete', $args);
    } else {
        // Deleting a calendar
        // TODO: implement delete-calendars function.
        //if (isset($args['itemid']) && empty($args['cid'])) $args['cid'] = $args['itemid'];
        //return xarModfunc('ievents', 'user', 'delete', $args);
    }
}

?>