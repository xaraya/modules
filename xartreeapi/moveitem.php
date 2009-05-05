<?php

// Update an hierarchy table to move the position of
// an item.
// tablename: string the name of the table.
// idname: string the name of the ID column.
// refid: integer the ID of the reference item (the one where we are moving to)
// itemid: integer the ID of the item we are moving
// offset: string the position of the item wrt the reference item ('lastchild', 'firstchild', 'after', 'before')
// TODO: need a bit more validation of parameters in here, to prevent corruption of the hierarchy.
// TODO: include changes done to categories to support non-contiguous category ranges.

function xarpages_treeapi_moveitem($args)
{
    extract($args);

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // Obtain current information on the reference item
    $refitem = xarModAPIFunc('xarpages', 'user', 'getpage', array('pid' => $refid));
    $query = 'SELECT xar_left, xar_right, xar_parent'
        . ' FROM ' . $tablename
        . ' WHERE ' . $idname . ' = ?';

    // Run the query (reference item).
    $result = $dbconn->execute($query, array($refid));
    if (!$result) return;

    if ($result->EOF) {
        $msg = xarML('Reference item "#(1)" does not exist', $refid);
        throw new BadParameterException(null,$msg);
    }
    list($ref_left, $ref_right, $ref_parent) = $result->fields;

    // Run the query (item to be moved).
    $result = $dbconn->execute($query, array((int)$itemid));
    if (!$result) return;

    if ($result->EOF) {
        $msg = xarML('Moving item "#(1)" does not exist', $itemid);
        throw new BadParameterException(null,$msg);
    }
    list($item_left, $item_right, $item_parent) = $result->fields;

    // Checking if the reference ID is of a child or itself
    if ($ref_left >= $item_left && $ref_left <= $item_right) {
        $msg = xarML('Group references siblings');
        throw new BadParameterException(null,$msg);
    }

    // Find the point of insertion.
    switch (strtolower($offset)) {
        case 'lastchild': // last child of reference item
            $insertion_point = $ref_right;
            break;
        case 'after': // after reference item, same level
            $insertion_point = $ref_right + 1;
            break;
        case 'firstchild': // first child reference item
            $insertion_point = $ref_left + 1;
            break;
        case 'before': // before reference item, same level
            $insertion_point = $ref_left;
            break;
        default:
            $msg = xarML('Offset not "#(1)" valid', $offset);
            throw new BadParameterException(null,$msg);
    };

    $size = $item_right - $item_left + 1;
    $distance = $insertion_point - $item_left;

    // If necessary to move then evaluate
    if ($distance != 0) {
        if ($distance > 0)
        { // moving forward
            $distance = $insertion_point - $item_right - 1;
            $deslocation_outside = -$size;
            $between_string = ($item_right + 1) . ' AND ' . ($insertion_point - 1);
        } else { // $distance < 0 (moving backward)
            $deslocation_outside = $size;
            $between_string = $insertion_point . ' AND ' . ($item_left - 1);
        }

        // This seems SQL-92 standard... Its a good test to see if
        // the databases we are supporting are complying with it. This can be
        // broken down in 3 simple UPDATES which shouldnt be a problem with any database.
        $query = 'UPDATE ' . $tablename
            . ' SET xar_left = CASE'
            . '    WHEN xar_left BETWEEN ' . $item_left . ' AND ' . $item_right
            . '    THEN xar_left + (' . $distance . ')'
            . '    WHEN xar_left BETWEEN ' . $between_string
            . '    THEN xar_left + (' . $deslocation_outside . ')'
            . '    ELSE xar_left'
            . ' END,'
            . ' xar_right = CASE'
            . '    WHEN xar_right BETWEEN ' . $item_left . ' AND ' . $item_right
            . '    THEN xar_right + (' . $distance . ')'
            . '    WHEN xar_right BETWEEN ' . $between_string
            . '    THEN xar_right + (' . $deslocation_outside . ')'
            . '    ELSE xar_right'
            . ' END';

        $result = $dbconn->execute($query);
        if (!$result) return;

        // Find the right parent for this item.
        if (strtolower($offset) == 'lastchild' || strtolower($offset) == 'firstchild') {
            $parent_id = $refid;
        } else {
            $parent_id = $ref_parent;
        }

        // Update parent id
        $query = 'UPDATE ' . $tablename
            . ' SET xar_parent = ?'
            . ' WHERE ' .$idname. ' = ?';

        $result = $dbconn->execute($query, array((int)$parent_id, (int)$itemid));
        if (!$result) return;
    }

    return true;
}

?>