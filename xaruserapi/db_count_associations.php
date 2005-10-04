<?php

/**
 *  Retrieve the total count assocations for a particular file/module/itemtyp/item combination
 *
 * @author  Carl P. Corliss
 * @access  public
 * @param   integer fileId    The id of the file
 * @param   integer modId     The id of module this file is associated with
 * @param   integer itemType  The item type within the defined module
 * @param   integer objectId    The id of the item types item

 * @returns integer           The total number of assocations for particular file/module/itemtype/item combination
 */

function filemanager_userapi_db_count_assocations( $args )
{

    extract($args);

    $where = array();

    if (isset($fileId)) {
        $whereList[] = ' (xar_fileEntry_id = ?) ';
        $bindvars[]  = (int) $fileId;
    }

    if (isset($modid)) {
        $whereList[] .= ' AND (xar_modid = ?)';
        $bindvars[]   = (int) $modid;

        if (isset($itemtype)) {
            $whereList[] .= ' AND (xar_itemtype = ?)';
            $bindvars[]   = (int) $itemtype;

            if (isset($itemid)) {
                $whereList[] .= ' AND (xar_objectid = ?)';
                $bindvars[]   = (int) $itemid;
            }
        }
    }

    if (count($whereList)) {
        $where = 'WHERE ' . implode(' AND ', $whereList);
    } else {
        $where = '';
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

        // table and column definitions
    $file_assoc_table = $xartable['file_associations'];

    $sql = "SELECT COUNT(xar_fileEntry_id) AS total
              FROM $file_assoc_table
            $where";

    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result)  {
        return FALSE;
    }

    // if no record found, return an empty array
    if ($result->EOF) {
        return (int) 0;
    }

    $row = $result->GetRowAssoc(false);

    return $row['total'];
}

?>