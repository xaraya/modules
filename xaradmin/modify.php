<?php

/**
 * Modify an event or calendar.
 */

function ievents_admin_modify($args)
{
    extract($args);

    // Fetch various potential keys
    xarVarFetch('itemtype', 'id', $itemtype, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('eid', 'id', $eid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('cid', 'id', $cid, 0, XARVAR_NOT_REQUIRED);
    
    // Can be called up using the itemtype and itemid
    list($itemtype_calendars, $itemtype_events) = 
        xarModAPIfunc('ievents', 'user', 'params', 
            array('names' => 'itemtype_calendars,itemtype_events')
        );

    if (!empty($itemtype)) {
        if ($itemtype != $itemtype_events && $itemtype != $itemtype_calendars) {
            // Unknown itemtype - raise an error.
            // FIXME: raise a xar error as there is no template to display the error in
            $data['message'] = xarML('Unknown or invalid itemtype #(1)', $args['itemtype']);
            return $data;
        }
    } else {
        // Assume we are modifying an event.
        $itemtype = $itemtype_events;
    }

    // Pass control over to the main GUI.
    if ($itemtype == $itemtype_events) {
        // Modifying an event
        if (isset($itemid) && empty($eid)) $eid = $itemid;
        $args['eid'] = $eid;
        return xarModfunc('ievents', 'user', 'modify', $args);
    } else {
        // Modifying a calendar
        if (isset($itemid) && empty($cid)) $cid = $itemid;
        $args['cid'] = $cid;
        return xarModfunc('ievents', 'user', 'modifycal', $args);
    }
}

?>