<?php

/**
 * return a pubsub user's subscriptions
 * @param $args['userid'] ID of the user whose subscriptions to return
 * @returns array
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_getsubscriptions($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($userid) || !is_numeric($userid)) {
        $invalid[] = 'userid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'getsubscriptions', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!xarModAPILoad('categories', 'user')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $modulestable = $xartable['modules'];
    $categoriestable = $xartable['categories'];
    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubregtable = $xartable['pubsub_reg'];

    // fetch items
    $query = "SELECT $pubsubeventstable.xar_eventid
                    ,$modulestable.xar_name
                    ,$pubsubeventstable.xar_modid
                    ,$pubsubeventstable.xar_itemtype
                    ,$categoriestable.xar_name
                    ,$pubsubeventstable.xar_cid
                    ,$pubsubeventstable.xar_extra
                    ,$pubsubregtable.xar_pubsubid
                    ,$pubsubregtable.xar_actionid
                FROM $pubsubeventstable
                    ,$modulestable
                    ,$categoriestable
                    ,$pubsubregtable
               WHERE $pubsubeventstable.xar_modid = $modulestable.xar_regid
                 AND $pubsubeventstable.xar_cid = $categoriestable.xar_cid
                 AND $pubsubeventstable.xar_eventid = $pubsubregtable.xar_eventid
                 AND $pubsubregtable.xar_userid =  '" . xarVarPrepForStore($userid) . "'";

    $result = $dbconn->Execute($query);
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
