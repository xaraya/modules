<?php

/**
 *  Retrieve the metadata stored for a particular file based on either 
 *  the file id or the file name.
 * 
 * @author Carl P. Corliss
 * @author Micheal Cortez
 * @access public
 * @param  integer  file_id     (Optional) grab file with the specified file id
 * @param  string   fileName    (Optional) grab file(s) with the specified file name
 * @param  integer  status      (Optional) grab files with a specified status  (SUBMITTED, APPROVED, REJECTED)
 * @param  integer  user_id     (Optional) grab files uploaded by a particular user
 * @param  integer  store_type  (Optional) grab files with the specified store type (FILESYSTEM, DATABASE)
 * @param  integer  mime_type   (Optional) grab files with the specified mime type 
 *
 * @returns array   All of the metadata stored for the particular file
 */
 
function uploads_userapi_db_get_file( $args )  {
    
    extract($args);
    
    if (!isset($fileId) && !isset($fileName) && !isset($fileStatus) && 
        !isset($userId)  && !isset($fileType) && !isset($store_type)) {            
        $msg = xarML('Missing parameters for function [#(1)] in module [#(2)]', 'db_get_file', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }        
    
    $where = array();
    
    if (isset($fileIds) && is_array($fileIds)) {
        $where[] = 'xar_fileEntry_id IN (' . implode(',', $fileIds) . ')';
    }
    
    if(isset($fileId) && !isset($fileIds)) {
        $where[] = "xar_fileEntry_id = $fileId";
    }

    if (isset($fileName)) {
        $where[] = "(xar_filename LIKE '$fileName')";
    }
        
    if (isset($fileStatus)) {
        $where[] = "(xar_status = $fileStatus)";
    }

    if (isset($userId)) {
        $where[] = "(xar_user_id = $userId)";
    } 

    if (isset($store_type)) {
        $where[] = "(xar_store_type = $store_type)";
    }
    
    if (isseT($fileType)) {
        $where[] = "(xar_mime_type LIKE '$fileType')";
    }

    if (count($where) > 1) {
        $where = implode(' AND ', $where);
    } else {
        $where = implode('', $where);
    }
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
              FROM $fileEntry_table
             WHERE $where";
    
    $result = $dbconn->Execute($sql);

    if (!$result)  {
        return;
    }

    // if no record found, return an empty array        
    if ($result->EOF) {
        return array();
    }
    
    $row = $result->GetRowAssoc(false);
    
    $importDir = xarModGetVar('uploads','path.imports-directory');
    $uploadDir = xarModGetVar('uploads','path.uploads-directory');
    
    // remove the '/' from the path
    $importDir = eregi_replace('/$', '', $importDir);
    $uploadDir = eregi_replace('/$', '', $uploadDir);
    
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
        
        switch(strtolower(dirname($fileInfo['fileLocation']))) {
            case $importDir: 
                $fileInfo['fileDirectory'] = 'IMPORTS';
                $fileInfo['fileHashName']  = basename($fileInfo['fileLocation']);
                break;
            case $uploadDir:
                $fileInfo['fileDirectory'] = 'UPLOADS';
                $fileInfo['fileHashName']  = basename($fileInfo['fileLocation']);
                break;
            default:
                $fileInfo['fileDirectory'] = dirname($fileInfo['fileLocation']);
                $fileInfo['fileHashName']  = basename($fileInfo['fileLocation']);
                break;
        }
        
        $fileList[] = $fileInfo;
        $result->MoveNext();
    }

    $result->Close();

                                                                    
   return $fileList;
}

?>
