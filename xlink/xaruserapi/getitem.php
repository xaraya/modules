<?php

/**
 * get a module item
 *
 * @param $args['base'] base name
 * @param $args['id'] id
 * @returns array
 * @return array of module id, item type and item id
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xlink_userapi_getitem($args)
{
    extract($args);

    if (!isset($base)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'base', 'user', 'getitem', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!isset($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'base', 'user', 'getitem', 'xlink');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $xlinktable = $xartable['xlink'];

    // Get module item for this id
    $query = "SELECT xar_moduleid,
                     xar_itemtype,
                     xar_itemid
              FROM $xlinktable
              WHERE xar_basename = '" . xarVarPrepForStore($base) . "'
                AND xar_refid = '" . xarVarPrepForStore($id) . "'";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $item = array();
    if ($result->EOF) {
        $result->Close();
        return $item;
    }
    list($item['moduleid'],
         $item['itemtype'],
         $item['itemid']) = $result->fields;

    $result->Close();
    return $item;
}


?>
