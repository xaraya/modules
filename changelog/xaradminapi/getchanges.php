<?php

/**
 * get entries for a module item
 *
 * @param $args['modid'] module id
 * @param $args['itemtype'] item type
 * @param $args['itemid'] item id
 * @param $args['numitems'] number of entries to retrieve (optional)
 * @param $args['startnum'] starting number (optional)
 * @returns array
 * @return array of changes
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function changelog_adminapi_getchanges($args)
{
    extract($args);

    if (!isset($modid) || !is_numeric($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module id', 'admin', 'getchanges', 'changelog');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $itemtype = 0;
    }
    if (!isset($itemid) || !is_numeric($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'getchanges', 'changelog');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $changelogtable = $xartable['changelog'];
    $rolestable = $xartable['roles'];

    // Get changes for this module item
    $query = "SELECT $changelogtable.xar_logid,
                     $changelogtable.xar_editor,
                     $changelogtable.xar_hostname,
                     $changelogtable.xar_date,
                     $changelogtable.xar_status,
                     $changelogtable.xar_remark,
                     $rolestable.xar_name
              FROM $changelogtable
              LEFT JOIN $rolestable
                ON $changelogtable.xar_editor = $rolestable.xar_uid
              WHERE $changelogtable.xar_moduleid = " . xarVarPrepForStore($modid) . "
                AND $changelogtable.xar_itemtype = " . xarVarPrepForStore($itemtype) . "
                AND $changelogtable.xar_itemid = " . xarVarPrepForStore($itemid) . "
              ORDER BY $changelogtable.xar_logid DESC";

    if (isset($numitems) && is_numeric($numitems)) {
        if (empty($startnum)) {
            $startnum = 1;
        }
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    } else {
        $result =& $dbconn->Execute($query);
    }
    if (!$result) return;

    $changes = array();
    while (!$result->EOF) {
        $change = array();
        list($change['logid'],
             $change['editor'],
             $change['hostname'],
             $change['date'],
             $change['status'],
             $change['remark'],
             $change['editorname']) = $result->fields;
        $changes[$change['logid']] = $change;
        $result->MoveNext();
    }
    $result->Close();

    return $changes;
}


?>
