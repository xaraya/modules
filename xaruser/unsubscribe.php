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
 * @param $args['cid'] cid of event
 * @param $args['itemtype'] itemtype of event
 * @returns output
 * @return output with pubsub information
 */
function pubsub_user_unsubscribe($args)
{
    // do nothing if user not logged in otherwise unsubscribe
    // the currently logged in user
    if (xarUserIsLoggedIn()) {
        $userid = xarUserGetVar('uid');
    } else {
        return;
    }
    if (!xarVarFetch('modid', 'int:1:', $modid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cid', 'int:1:', $cid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype', 'int:1:', $itemtype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str:1:', $returnurl, '', XARVAR_NOT_REQUIRED)) return;


    $returnurl = rawurldecode($returnurl);

    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($returnurl) || !is_string($returnurl)) {
        $invalid[] = 'returnurl';
    }
    if (!isset($modid) || !is_numeric($modid)) {
        $invalid[] = 'modid';
    }
    if (!isset($cid) || !is_numeric($cid)) {
        $invalid[] = 'cid';
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $invalid[] = 'itemtype';
    }
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
                 AND $pubsubeventstable.xar_itemtype = ?
                 AND $pubsubregtable.xar_eventid = $pubsubeventstable.xar_eventid
                 AND $pubsubregtable.xar_userid = ?
                 AND $pubsubeventstable.xar_cid = ?";

    $bindvars = array((int)$modid, $itemtype, (int)$userid, $cid);
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

    xarResponseRedirect($returnurl);
    return true;

} // END unsubscribe

?>
