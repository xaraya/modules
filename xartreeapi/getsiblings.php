<?php

/*
 * Get the parent/left/right values for a single item.
 * Will include the virtual item '0' if necessary.
 * id: ID of the item.
 * tablename: name of table
 * idname: name of the ID column
 * includeself: bool to include the given pid in the result (default false)
 */

function xarpages_treeapi_getsiblings($args)
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
                    parent.xar_parent = node.xar_parent
                    AND 
                    node.$idname = ?";
        if (!isset($includeself) || $includeself != true) {
            $query .= " AND parent.$idname != ?";
        }
        $query .= " ORDER BY parent.xar_left";

        $siblings = array();

        // return results in proper order
        while (!$result->EOF) {
            list($pid) = $result->fields;
            $siblings[] = $pid;
        }
        if (count($siblings) > 0) {
            return $siblings;
        } else {
            return;
        }
    } else {
        // Insert point is the virtual root.
        // Virtual root has no siblings
        if(isset($includeself) && $includeself == true) {
            return array(0);
        } else {
            return;
        }
    }
}

?>
