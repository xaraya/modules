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
    $dbconn =& xarDBGetConn();

    if ($id <> 0) {
        // Insert point is a real item.
        $query = 'SELECT xar_parent'
            . ' FROM ' . $tablename
            . ' WHERE ' . $idname . ' = ?';
        $result = $dbconn->execute($query, array((int)$id));
        if (!$result->EOF) {
            list($parent) = $result->fields;
            $return = array((int)$parent);
        } else {
            // Item not found.
            // TODO: raise error.
            return;
        }
    } else {
        // Insert point is the virtual root.
        $return = array(0);
    }

}

?>
