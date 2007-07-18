<?php

/**
 * Delete an event.
 *
 * @param confirm string Must be set to perform the delete, otherwise confirmation is requested.
 * @param return_url string The return URL.
 */

function ievents_user_delete($args)
{
    extract($args);

    // Template data.
    $return = array();
    $message = '';
    $result = false;

    // Confirm text.
    xarVarFetch('confirm', 'str:0:30', $confirm, '', XARVAR_NOT_REQUIRED);

    // Return URL.
    xarVarFetch('return_url', 'str:0:200', $return_url, '', XARVAR_NOT_REQUIRED);
    $return['return_url'] = $return_url;
    
    // Need an event ID to delete.
    xarVarFetch('eid', 'id', $eid, 0, XARVAR_NOT_REQUIRED);
    if (empty($eid)) {
        // Error message
        $message = xarML('Missing event ID (eid)');
    } else {
        // Fetch the event.
        $event = xarModAPIfunc('ievents', 'user', 'getevent', array('eid' => $eid));

        if (empty($event)) {
            // Error message
            $message = xarML('Event number #(1) does not exist', $eid);
        } else {
            // Check we have permission to delete it.
            if (!xarSecurityCheck('DeleteIEvent', 0, 'IEvent', $event['calendar_id'] . ':' .$event['eid']. ':' . $event['created_by'])) {
                // Error message
                $message = xarML('No privileges to delete this event');
            } else {
                // If confirmed, then do the delete
                if (!empty($confirm)) {
                    // Do the delete
                    $result = xarModAPIfunc('ievents', 'admin', 'delete', array('eid' => $eid));

                    if (empty($result)) {
                        // Error message
                        $message = xarML('Failed to delete the event');
                    } else {
                        // Status message
                        $message = xarML('Event deleted');
                    }
                } else {
                    $return['event'] = $event;
                }
            }
        }
    }

    // If successfuly deleted, then redirect if required.
    if ($result && !empty($return_url)) {
        // When setting a redirect URL, be sure not to try and return to the
        // event we just deleted.
        xarResponseRedirect($return_url);
        return true;
    }

    $return['message'] = $message;

    // Template data.
    return $return;
}

?>