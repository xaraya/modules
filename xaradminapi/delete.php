<?php

/**
 * Delete an event.
 *
 * @param eid integer Event ID
 */

function ievents_adminapi_delete($args)
{
    extract($args);

    list($module, $modid, $itemtype_events) =
        xarModAPIfunc('ievents', 'user', 'params',
            array('names' => 'module,modid,itemtype_events')
        );


    if (empty($eid)) {
        // Event ID is mandatory.
        $msg = xarML('Invalid event ID #(1)', $eid);
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }

    // Fetch the event
    $event = xarModAPIfunc($module, 'user', 'getevent', array('eid' => $eid));

    // Does not exist, so we will pretend the delete was successful.
    if (empty($event)) return true;

    // Check privileges.
    if (!xarSecurityCheck('DeleteIEvent', 0, 'IEvent', $event['calendar_id'] . ':' .$event['eid']. ':' . $event['created_by'])) {
        // Event ID is mandatory.
        $msg = xarML('No privilege to delete event #(1)', $eid);
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }

    // Do the delete through the object.
    $object = xarModAPIFunc(
        'dynamicdata', 'user', 'getobject',
        array('modid' => $modid, 'itemtype' => $itemtype_events)
    );

    $object->deleteItem(array('itemid' => $eid));

    // Call delete hooks.
    xarModCallHooks(
        'item', 'delete', $eid,
        array('module' => $module, 'itemtype' => $itemtype_events)
    );

    return true;
}

?>