<?php

/**
 *  Retrieve the total count assocations for a particular file/module/itemtyp/item combination
 * 
 * @author  Carl P. Corliss
 * @access  public
 * @param   integer fileId    The id of the file 
 * @param   integer modId     The id of module this file is associated with
 * @param   integer itemType  The item type within the defined module 
 * @param   integer itemId    The id of the item types item
 
 * @returns integer           The total number of assocations for particular file/module/itemtype/item combination
 */
 
function uploads_userapi_db_count_assocations( $args )  {
    
    extract($args);
    
    $where = array();
    
    if (isset($fileId)) {
        $whereList[] = ' (xar_fileEntry_id = $fileId) ';
    }
    
    if (isset($modId)) {
        $whereList[] .= ' AND (xar_modid = $modId)';
        
        if (isset($itemType)) {
            $whereList[] .= ' AND (xar_itemtype = $itemType)';
            
            if (isset($itemId)) {
                $whereList[] .= ' AND (xar_itemid = $itemId)';
            }
        } 
    }
    
    if (count($whereList)) {
        $where = 'WHERE ' . implode(' AND ', $whereList);
    } else {
        $where = '';
    }
    
    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable     = xarDBGetTables();
        
        // table and column definitions
    $file_assoc_table = $xartable['file_associations'];
    
    $sql = "SELECT COUNT(xar_fileEntry_id) AS total
              FROM $file_assoc_table
            $where";
    
    $result = $dbconn->Execute($sql);

    if (!$result)  {
        return FALSE;
    }

    // if no record found, return an empty array        
    if ($result->EOF) {
        return (integer) 0;
    }
    
    $row = $result->GetRowAssoc(false);
    
    return $row['total'];
}

?>
