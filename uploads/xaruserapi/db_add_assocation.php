<?php

/** 
 *  Create an assocation between a (stored) file and a module/itemtype/item
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer fileId    The id of the file we are going to associate with an item
 *  @param   integer modId     The id of module this file is associated with
 *  @param   integer itemType  The item type within the defined module 
 *  @param   integer itemId    The id of the item types item
 *
 *  @returns integer The id of the file that was associated, FALSE with exception on error
 */

function uploads_userapi_db_add_association( $args ) {
    
    extract($args);

    if (!isset($fileId)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]', 
                     'fileId','db_add_assocation','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (!isset($modId)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]', 
                     'modId','db_add_assocation','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (!isset($itemType)) {
        $itemType = 0;
    }
    
    if (!isset($itemId)) {
        $itemId = 0;
    }
    
    //add to uploads table
    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();


    // table and column definitions
    $file_assoc_table = $xartable['file_assocations'];

    // insert value into table
    $sql = "INSERT INTO $file_assoc_table 
                      ( 
                        xar_fileEntry_id, 
                        xar_modid,
                        xar_itemtype,
                        xar_itemid
                      ) 
               VALUES 
                      (
                        $fileId,
                        $modId,
                        $itemType,
                        $itemId
                      )";
                      
    $result = &$dbconn->Execute($sql);

    if (!$result) {
        return FALSE;
    } else {
        return $fileId  ;
    }
}

?>
