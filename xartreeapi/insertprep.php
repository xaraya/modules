<?php
/*
 * Prepare a tree for insering a new item.
 * Basically opens a gap for the item, then returns the
 * tree-specific values (parent, left, right) to be
 * used when inserting the item.
 * The function supports multiple trees. Each tree starts
 * at a virtual 'parent' of ID 0.
 * args: insertpoint (ID), offset ('after', 'before', 'firstchild', 'lastchild'), tablename, idname
 * Table must have columns: xar_parent, xar_left, xar_right and a specified ID column.
 */

function xarpages_treeapi_insertprep($args)
{
    // An insertion point (an ID in the table) is required.
    // Special insertion point ID is 0, which refers to the
    // virtual root of all trees. An item can not be
    // inserted on the same level as the virtual root.

    extract($args);

    // TODO: validate params: insertpoint, offset, tablename, idname

    // Default operation is 'before' - i.e. put the new item in the place
    // of the insertpoint and move everything to the right one place.
    if (!xarVarValidate('enum:before:after:firstchild:lastchild', $offset, true)) {
        $offset = 'firstchild';
    }

    if (!isset($insertpoint)) {$insertpoint = 0;}
    if (!isset($idname)) {$idname = 'xar_id';}

    // Cannot insert on the same level as the virtual root.
    if ($insertpoint == 0) {
        if ($offset == 'before') {$offset = 'firstchild';}
        if ($offset == 'after') {$offset = 'lastchild';}
    }

    $dbconn = xarDB::getConn();

    $result = xarModAPIfunc(
        'xarpages', 'tree', 'getleftright',
        array(
            'tablename' => $tablename,
            'idname' => $idname,
            'id' => $insertpoint
        )
    );
    if (!$result) {return;}
    extract($result);

    // Locate the new insert point.
    if ($offset == 'before') {
        $shift = $left;
    }
    if ($offset == 'after') {
        $shift = $right + 1;
    }
    if ($offset == 'firstchild') {
        $shift = $left + 1;
        $parent = $insertpoint;
    }
    if ($offset == 'lastchild') {
        $shift = $right;
        $parent = $insertpoint;
    }

    // Create a space of two traversal points.
    // The new item will not have children, so the traversal
    // points will be sequential.
    $query = 'UPDATE ' . $tablename
        . ' SET xar_left = xar_left + 2 '
        . ' WHERE xar_left >= ?';
    $result = $dbconn->execute($query, array($shift));
    if (!$result) {return;}

    $query = 'UPDATE ' . $tablename
        . ' SET xar_right = xar_right + 2 '
        . ' WHERE xar_right >= ?';
    $result = $dbconn->execute($query, array($shift));
    if (!$result) {return;}

    // Return the new parent/left/right values
    return array(
        'parent' => $parent,
        'left' => $shift,
        'right' => $shift + 1
    );
}

?>