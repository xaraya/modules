<?php

/** 
 *  Remove an assocation between a particular file and module/itemtype/item.
 *  <br />
 *  If just the fileId is passed in, all assocations for that file will be deleted.
 *  If the fileId and modId are supplied, any assocations for the given file and modId
 *  will be removed. The same holds true for itemType and objectId. 
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer fileId    The id of the file we are going to remove association with
 *  @param   integer modId     The id of module this file is associated with
 *  @param   integer itemType  The item type within the defined module 
 *  @param   integer objectId    The id of the item types item
 *
 *  @returns integer The number of affected rows on success, or FALSE on error
 */

function uploads_userapi_db_delete_assocation( $args ) {
    
    extract($args);

    if (!isset($fileId)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]', 
                     'fileId','db_delete_assocation','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    } elseif (is_array($fileId)) {
        $where = 'WHERE (xar_fileEntry_id IN (' . implode(',', $fileId) . ')';
    } else {
        $where = 'WHERE (xar_fileEntry_id = $fileId) ';
    }
    
    if (isset($modId)) {
        $where .= ' AND (xar_modid = $modId)';
        
        if (isset($itemType)) {
            $where .= ' AND (xar_itemtype = $itemType)';
            
            if (isset($objectId)) {
                $where .= ' AND (xar_objectid = $objectId)';
            }
        } 
    }
    
    //add to uploads table
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // table and column definitions
    $file_assoc_table   = $xartable['file_associations'];
    
    // insert value into table
    $sql = "DELETE 
              FROM $file_assoc_table
            $where";
                  
                      
    $result = &$dbconn->Execute($sql);
    
    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->Affected_Rows();
    }

}

?>
