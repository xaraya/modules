<?php

/**
 * get a module item
 *
 * @param $args['id'] id of the xlink entry, or
 * @param $args['basename'] base name +
 * @param $args['refid'] reference id
 * @returns array
 * @return array of module id, item type and item id
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xlink_userapi_getitem($args)
{
    extract($args);

    if (!empty($id)) {
        if (!is_numeric($id)) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                         'xlink id', 'user', 'getitem', 'xlink');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                           new SystemException($msg));
            return;
        }
    } else {
        if (!isset($basename)) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                        'base name', 'user', 'getitem', 'xlink');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                           new SystemException($msg));
            return;
        }
        if (!isset($refid)) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                         'reference id', 'user', 'getitem', 'xlink');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                           new SystemException($msg));
            return;
        }
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $xlinktable = $xartable['xlink'];

    // Get module item for this id
    $query = "SELECT xar_id,
                     xar_basename,
                     xar_refid,
                     xar_moduleid,
                     xar_itemtype,
                     xar_itemid
              FROM $xlinktable";
    if (!empty($id)) {
        $query .= " WHERE xar_id = ?";
        $bindvars = array((int) $id);
    } else {
        $query .= " WHERE xar_basename = ? AND xar_refid = ?";
        $bindvars = array((string) $basename, (string) $refid);
    }

    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    $item = array();
    if ($result->EOF) {
        $result->Close();
        return $item;
    }
    list($item['id'],
         $item['basename'],
         $item['refid'],
         $item['moduleid'],
         $item['itemtype'],
         $item['itemid']) = $result->fields;

    $result->Close();
    return $item;
}

?>
