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
 * @return array of cross links
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xlink_userapi_getlinks($args)
{
    extract($args);

    if (!isset($modid) || !is_numeric($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module id', 'user', 'getlinks', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item type', 'user', 'getlinks', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!isset($itemid) || !is_numeric($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'user', 'getlinks', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $xlinktable = $xartable['xlink'];

    // Get links for this module item
    $query = "SELECT xar_id,
                     xar_basename,
                     xar_refid
              FROM $xlinktable
              WHERE xar_moduleid = " . xarVarPrepForStore($modid) . "
                AND xar_itemtype = " . xarVarPrepForStore($itemtype) . "
                AND xar_itemid = " . xarVarPrepForStore($itemid) . "
              ORDER BY xar_basename ASC, xar_refid ASC";

    if (isset($numitems) && is_numeric($numitems)) {
        if (empty($startnum)) {
            $startnum = 1;
        }
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    } else {
        $result =& $dbconn->Execute($query);
    }
    if (!$result) return;

    $links = array();
    while (!$result->EOF) {
        $link = array();
        list($linkid,
             $link['basename'],
             $link['refid']) = $result->fields;
        $links[$linkid] = $link;
        $result->MoveNext();
    }
    $result->Close();

    return $links;
}


?>
