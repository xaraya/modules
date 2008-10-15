<?php

/**
 * Delete an event or calendar.
 *
 * @todo Support deleting a calendar, when the GUI is available.
 */

function ievents_admin_delete($args)
{
    extract($args);

    xarVarFetch('itemtype', 'id', $itemtype, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('eid', 'id', $eid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('cid', 'id', $cid, 0, XARVAR_NOT_REQUIRED);

    // Can be called up using the itemtype and itemid

    $itemtype_events = xarModGetVar('ievents', 'itemtype_events');
    $itemtype_calendars = xarModGetVar('ievents', 'itemtype_calendars');

    if (!empty($itemtype)) {
        if ($itemtype != $itemtype_events /* && $itemtype != $itemtype_calendars*/) {
            // Unknown itemtype - raise an error.
            // FIXME: raise a xar error as there is no template to display this message in
            $data['message'] = xarML('Unknown or invalid itemtype #(1)', $args['itemtype']);
            return $data;
        }
    } else {
        // Assume we are deleting an event.
        $itemtype = $itemtype_events;
    }

    // Pass control over to the main GUI.
    if ($itemtype == $itemtype_events) {
        // Deleting an event
        if (isset($itemid) && empty($eid)) $eid = $itemid;
        return xarModfunc('ievents', 'user', 'delete', $args);
    } else {
        // Deleting a calendar
        // TODO: implement delete-calendars function.
        //if (isset($args['itemid']) && empty($args['cid'])) $args['cid'] = $args['itemid'];
        //return xarModfunc('ievents', 'user', 'delete', $args);
    }
}

?>
