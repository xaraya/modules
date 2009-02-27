<?php

/*
 * Get the parent/left/right values for a single item.
 * Will include the virtual item '0' if necessary.
 * id: ID of the item.
 * tablename: name of table
 * idname: name of the ID column
 */

function xarpages_treeapi_getprevsibling($args)
{
    // Expand the arguments.
    extract($args);

    // Database.
    $dbconn =& xarDBGetConn();

    if ($id <> 0) {
        // Insert point is a real item.
        $query = "SELECT 
                    parent.$idname
                  FROM 
                    $tablename AS node, 
                    $tablename AS parent 
                  WHERE 
                    parent.xar_right = node.xar_left - 1
                    AND 
                    node.$idname = ?";
        // return result
        while (!$result->EOF) {
            list($pid) = $result->fields;
        }
        if (isset($pid)) {
            return $pid;
        } else {
            return;
        }
    } else {
        // Insert point is the virtual root.
        // Virtual root has no siblings
        return;
    }
}

?>
