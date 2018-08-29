<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * return a pubsub user's subscriptions
 * @param $args['userid'] ID of the user whose subscriptions to return
 * @returns array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_getsubscriptions($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($userid) || !is_numeric($userid)) $invalid[] = 'userid';

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'getsubscriptions', 'Pubsub');
        throw new Exception($msg);
    }

    if (!xarModAPILoad('categories', 'user')) return;

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $modulestable = $xartable['modules'];
    $categoriestable = $xartable['categories'];
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubsubscriptionstable = $xartable['pubsub_subscriptions'];

    // fetch items
    $query = "SELECT $pubsubeventstable.eventid
                    ,$modulestable.name
                    ,$pubsubeventstable.modid
                    ,$pubsubeventstable.itemtype
                    ,$categoriestable.name
                    ,$pubsubeventstable.cid
                    ,$pubsubeventstable.extra
                    ,$pubsubsubscriptionstable.pubsubid
                    ,$pubsubsubscriptionstable.actionid
                FROM $pubsubeventstable
                    ,$modulestable
                    ,$categoriestable
                    ,$pubsubsubscriptionstable
               WHERE $pubsubeventstable.modid = $modulestable.regid
                 AND $pubsubeventstable.cid = $categoriestable.cid
                 AND $pubsubeventstable.eventid = $pubsubsubscriptionstable.eventid
                 AND $pubsubsubscriptionstable.userid =  ?";

    $result = $dbconn->Execute($query, array((int)$userid));
    if (!$result) return;

    $items = array();
    while (!$result->EOF) {
        $item = array();
        list($item['eventid'],
             $item['modname'],
             $item['modid'],
             $item['itemtype'],
             $item['catname'],
             $item['cid'],
             $item['extra'],
             $item['pubsubid'],
             $item['actionid']) = $result->fields;
        $items[$item['pubsubid']] = $item;
        $result->MoveNext();
    }
    return $items;
}

?>
