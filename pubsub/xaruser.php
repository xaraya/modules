<?php
/**
 * File: $Id$
 *
 * Pubsub User Interface
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Pubsub Module
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */

/**
 * the main user function
 */
function pubsub_user_main()
{
    // Return output
    return xarML('This module has no user interface *except* via display hooks');
}

/**
 * remove user from a pubsub element
 * @param $args['eventid'] event ID
 * @returns output
 * @return output with pubsub information
 */
function pubsub_user_remove($args)
{
    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($eventid) || !is_numeric($eventid)) {
        $invalid[] = 'eventid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(3)() in module #(4)',
        join(', ',$invalid), 'remove', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!xarModAPIFunc('pubsub',
                       'user',
                       'deluser',
                        array('eventid' => $eventid)))
        return; // throw back

    return true;
}



/**
 * handle fact a user may already be subscribed and give them option to unsubscribe
 * @param $args['eventid'] event already subscribed to
 * @returns output
 * @return output with pubsub information
 */
//FIXME: <garrett> don't think we need to use this anymore
function pubsub_user_subscribed($args)
{
    extract($args);
    $invalid = array();
    if (!isset($actionid) || !is_numeric($actionid)) {
        $invalid[] = 'actionid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'subscribed', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $data['eventid'] = xarVarPrepForDisplay($eventid);

    return $data;
}
?>
