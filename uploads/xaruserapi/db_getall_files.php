<?php

/**
 * Retrieve the metadata stored for all files in the database
 * 
 * @author Carl P. Corliss
 * @author Micheal Cortez
 * @access public
 *
 * @returns array   All of the metadata stored for the particular file
 */
 
function uploads_userapi_db_getall_files( /* VOID */ ) {
    
    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable     = xarDBGetTables();
        
    // table and column definitions
    $fileEntry_table = $xartable['file_entry'];

    $sql = "SELECT xar_fileEntry_id, 
                   xar_user_id, 
                   xar_filename, 
                   xar_location, 
                   xar_filesize,
                   xar_status, 
                   xar_store_type, 
                   xar_mime_type
              FROM $fileEntry_table";
    
    $result = $dbconn->Execute($sql);

    if (!$result)  {
        return;
    }

    // if no record found, return an empty array        
    if ($result->EOF) {
        return array();
    }
    
    $importDir = xarModGetVar('uploads','path.imports-directory');
    $uploadDir = xarModGetVar('uploads','path.uploads-directory');
    
    // remove the '/' at the end of the path
    $importDir = eregi_replace('/$', '', $importDir);
    $uploadDir = eregi_replace('/$', '', $uploadDir);

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        
        $fileInfo['fileId']       = $row['xar_fileentry_id'];
        $fileInfo['userId']       = $row['xar_user_id'];
        $fileInfo['userName']     = xarUserGetVar('name',$row['xar_user_id']);
        $fileInfo['fileName']     = $row['xar_filename'];
        $fileInfo['fileLocation'] = $row['xar_location'];
        $fileInfo['fileSize']     = $row['xar_filesize'];
        $fileInfo['fileStatus']   = $row['xar_status'];
        $fileInfo['fileType']     = $row['xar_mime_type'];
        $fileInfo['storeType']    = $row['xar_store_type'];
        
        $row = $result->GetRowAssoc(false);

        if (stristr($fileInfo['fileLocation'], $importDir)) {
            $fileInfo['fileDirectory'] = dirname(str_replace($importDir, 'IMPORTS', $fileInfo['fileLocation']));
            $fileInfo['fileHashName']  = basename($fileInfo['fileLocation']);
        } elseif (stristr($fileInfo['fileLocation'], $uploadDir)) {
            $fileInfo['fileDirectory'] = dirname(str_replace($uploadDir, 'UPLOADS', $fileInfo['fileLocation']));
            $fileInfo['fileHashName']  = basename($fileInfo['fileLocation']);
        } else {
            $fileInfo['fileDirectory'] = dirname($fileInfo['fileLocation']);
            $fileInfo['fileHashName']  = basename($fileInfo['fileLocation']);
        }
       
        $fileList[] = $fileInfo;
        $result->MoveNext();
    }

    $result->Close();

                                                                    
   return $fileList;
}

?>
