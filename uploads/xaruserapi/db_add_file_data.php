<?php

/** 
 *  Adds a file's  contents to the database. This only takes 4K (4096 bytes) blocks.
 *  So a file's data could potentially be contained amongst many records. This is done to 
 *  ensure that we are able to actually save the whole file in the db.
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer fileId         The ID of the file this data belongs to
 *  @param   string  fileName       The name of the file (minus any path information)
 *  @param   string  fileLocation   The complete path to the file including the filename (obfuscated if so chosen)
 *  @param   string  fileType       The mime content-type of the file
 *  @param   integer fileStatus     The status of the file (APPROVED, SUBMITTED, READABLE, REJECTED)
 *  @param   integer store_type     The manner in which the file is to be stored (filesystem, database)
 *
 *  @returns integer The id of the fileData that was added, or FALSE on error
 */

function uploads_userapi_db_add_file_data( $args ) {
    
    extract($args);
    
    if (!isset($fileId)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]', 
                     'fileId','db_add_file_data','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (!isset($fileData)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module (#3)]', 
                     'location','db_add_file_data','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (sizeof($fileData) >= (1024 * 64)) {
        $msg = xarML('#(1) exceeds maximum storage limit of 64KB per data chunk.', 'fileData');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATA_GT_BUFFER', SystemException($msg));
        return FALSE;
    }

    $fileData = base64_encode($fileData);
    
    //add to uploads table
    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();


    // table and column definitions
    $fileData_table = $xartable['file_data'];
    $fileDataID    = $dbconn->GenID($fileData_table);

    // insert value into table
    $sql = "INSERT INTO $fileData_table 
                      ( 
                        xar_fileEntry_id, 
                        xar_fileData_id, 
                        xar_fileData 
                      ) 
               VALUES 
                      (
                        $fileId,
                        $fileDataID,
                        '$fileData'
                      )";
    $result = &$dbconn->Execute($sql);

    if (!$result) {
        return FALSE;
    } else {
        $id = $dbconn->PO_Insert_ID($xartable['file_data'], 'xar_cid');
        return $id;
    }
}

?>
