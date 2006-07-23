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
 * subscribe user to a pubsub element
 * @param $args['modid'] module ID of event
 * @param $args['itemtype'] itemtype of event
 * @param $args['cid'] cid of event
 * @param $args['extra'] some extra group criteria
 *
 * @param $args['userid'] the subscriber OR
 * @param $args['email'] EMail address of anonymous user
 *
 * @param $args['groupdescr'] <unknown>
 * @returns output
 * @return output with pubsub information
 */
function pubsub_userapi_subscribe($args)
{
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($modid))      { $invalid[] = 'modid'; }
    if (!isset($cid))        { $invalid[] = 'cid'; }
    if (!isset($itemtype))   { $invalid[] = 'itemtype'; }
    if (!isset($userid) && !isset($email))     { $invalid[] = 'userid/email'; }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
        join(', ',$invalid), 'subscribe', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if( !isset($userid) && isset($email) && !empty($email) )
    {
        $userid = -1;
        //TODO: Email validation to make sure this is a valid looking email address
    } elseif ( !isset($email) ) {
        $email = '';
    }

    if (!isset($extra))
        $extra = '';

    if (!isset($groupdescr))
        $groupdescr = 'Subscribe';

    // check if we already have an event for this, or create it if necessary
    $eventid = xarModAPIFunc('pubsub','admin','checkevent',
                             array('modid' => $modid,
                                   'itemtype' => $itemtype,
                                   'cid' => $cid,
                                   'extra' => $extra,
                                   'groupdescr' => $groupdescr));
    if (empty($eventid)) return; // throw back

// TODO: fill in eventid *and* actionid (wherever that is supposed to come from)
// AM hardcoding actionid to 1 for now, will have to work out options for htmlmail etc. later
    if (!xarModAPIFunc('pubsub',
                       'user',
                       'adduser',
                        array('eventid' => $eventid
                             ,'actionid' => 1
                             ,'userid' => $userid
                             ,'email' => $email
                              ))) {
        $msg = xarML('Bad return from #(1) in function #(2)() in module #(3)',
                     'adduser', 'subscribe', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
    }

    return true;

} // END subscribe

?>