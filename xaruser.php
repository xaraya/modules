<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * the main user function
 */
function pubsub_user_main()
{

// TODO: show subscribed events for the user here ?

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
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
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
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $data['eventid'] = xarVarPrepForDisplay($eventid);

    return $data;
}
?>
