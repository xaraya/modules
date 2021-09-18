<?php

/*
 * Get the parent/left/right values for a single item.
 * Will include the virtual item '0' if necessary.
 * id: ID of the item.
 * tablename: name of table
 * idname: name of the ID column
 */

function xarpages_treeapi_getparent($args)
{
    // Expand the arguments.
    extract($args);

    // Database.
    $dbconn =& xarDB::getConn();

    if ($id <> 0) {
        // Insert point is a real item.
        $query = 'SELECT xar_parent'
            . ' FROM ' . $tablename
            . ' WHERE ' . $idname . ' = ?';
        $result = $dbconn->execute($query, [(int)$id]);
        if (!$result->EOF) {
            [$parent] = $result->fields;
            $return = [(int)$parent];
        } else {
            // Item not found.
            // TODO: raise error.
            return;
        }
    } else {
        // Insert point is the virtual root.
        $return = [0];
    }
}
