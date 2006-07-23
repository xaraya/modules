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
 * unsubscribe user from a pubsub element
 * @param $args['modid'] module ID of event
 * @param $args['itemtype'] itemtype of event
 * @param $args['cid'] cid of event
 * @param $args['extra'] some extra group criteria
 * @param $args['userid'] the subscriber
 * @returns output
 * @return output with pubsub information
 */
function pubsub_userapi_unsubscribe($args)
{
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($modid))     { $invalid[] = 'modid'; }
    if (!isset($cid))         { $invalid[] = 'cid'; }
//    if (!isset($itemtype))  { $invalid[] = 'itemtype'; }
    if (!isset($userid))    { $invalid[] = 'userid'; }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) in function #(3)() in module #(4)',
        join(', ',$invalid), 'unsubscribe', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubregtable = $xartable['pubsub_reg'];

    // fetch pubsubid to unsubscribe from
    $query = "SELECT xar_pubsubid
                FROM $pubsubeventstable, $pubsubregtable
               WHERE $pubsubeventstable.xar_modid = ?
                 AND $pubsubregtable.xar_eventid = $pubsubeventstable.xar_eventid
                 AND $pubsubregtable.xar_userid = ?
                 AND $pubsubeventstable.xar_cid = ?";

    $bindvars = array((int)$modid, (int)$userid, (int)$cid);
    if (isset($extra)) {
        $query .= " AND $pubsubeventstable.xar_extra = ?";
        array_push($bindvars, $extra);
    }
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result || $result->EOF) return;

    list($pubsubid) = $result->fields;

    if (!xarModAPIFunc('pubsub',
                       'user',
                       'deluser',
                        array('pubsubid' => $pubsubid))) {
        $msg = xarML('Bad return from #(1) in function #(2)() in module #(3)',
                     'deluser', 'unsubscribe', 'Pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
    }

    return true;

} // END unsubscribe

?>
