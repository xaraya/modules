<?php

/**
 *  Retrieve a list of file assocations for a particular file/module/itemtype/item combination
 * 
 * @author Carl P. Corliss
 * @access public
 * @param   integer fileId    The id of the file we are going to associate with an item
 * @param   integer modId     The id of module this file is associated with
 * @param   integer itemType  The item type within the defined module 
 * @param   integer itemId    The id of the item types item
 *
 * @returns array   A list of associations, including the fileId -> ModId -> ItemType -> ItemId
 */
 
function uploads_userapi_db_get_file( $args )  {
    
    extract($args);
    
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
        return;
    }

    // if no record found, return an empty array        
    if ($result->EOF) {
        return array();
    }
    
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        
        $fileAssoc['fileId']   = $row['xar_fileentry_id'];
        $fileAssoc['modId']    = $row['xar_modid'];
        $fileAssoc['itemType'] = $row['xar_itemtype'];
        $fileAssoc['itemId']   = $row['xar_itemid'];
        
        $fileList[$fileAssoc['fileId']] = $fileAssoc;
        $result->MoveNext();
    }
    
    return $fileList;
}

?>
