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
 
function uploads_userapi_db_getall_files( /* VOID */ ) 
{
    
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
        
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

    if(xarServerGetVar('PATH_TRANSLATED')) {
        $base_directory = dirname(realpath(xarServerGetVar('PATH_TRANSLATED')));
    } elseif(xarServerGetVar('SCRIPT_FILENAME')) {
        $base_directory = dirname(realpath(xarServerGetVar('SCRIPT_FILENAME')));
    } else {
        $base_directory = './';
    }
        
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        
        $fileInfo['fileId']        = $row['xar_fileentry_id'];
        $fileInfo['userId']        = $row['xar_user_id'];
        $fileInfo['userName']      = xarUserGetVar('name',$row['xar_user_id']);
        $fileInfo['fileName']      = $row['xar_filename'];
        $fileInfo['fileLocation']  = $row['xar_location'];
        $fileInfo['fileSize']      = $row['xar_filesize'];
        $fileInfo['fileStatus']    = $row['xar_status'];
        $fileInfo['fileType']      = $row['xar_mime_type'];
        $fileInfo['fileTypeInfo']  = xarModAPIFunc('mime', 'user', 'get_rev_mimetype', array('mimeType' => $fileInfo['fileType']));
        $fileInfo['storeType']     = $row['xar_store_type'];
        $fileInfo['mimeImage']     = xarModAPIFunc('mime', 'user', 'get_mime_image', array('mimeType' => $fileInfo['fileType']));
        $fileInfo['fileURL']       = xarServerGetBaseURL() . str_replace($base_directory, '', $fileInfo['fileLocation']);
        $fileInfo['fileDownload']  = xarModURL('uploads', 'user', 'download', array('fileId' => $fileInfo['fileId']));
        $fileInfo['DownloadLabel'] = xarML('Download file: #(1)', $fileInfo['fileName']);
        
        if (stristr($fileInfo['fileLocation'], $importDir)) {
            $fileInfo['fileDirectory'] = dirname(str_replace($importDir, 'imports', $fileInfo['fileLocation']));
            $fileInfo['fileHash']  = basename($fileInfo['fileLocation']);
        } elseif (stristr($fileInfo['fileLocation'], $uploadDir)) {
            $fileInfo['fileDirectory'] = dirname(str_replace($uploadDir, 'uploads', $fileInfo['fileLocation']));
            $fileInfo['fileHash']  = basename($fileInfo['fileLocation']);
        } else {
            $fileInfo['fileDirectory'] = dirname($fileInfo['fileLocation']);
            $fileInfo['fileHash']  = basename($fileInfo['fileLocation']);
        }
        
        $fileInfo['fileHashName']     = $fileInfo['fileDirectory'] . '/' . $fileInfo['fileHash'];
        $fileInfo['fileHashRealName'] = $fileInfo['fileDirectory'] . '/' . $fileInfo['fileName'];
        
        switch($fileInfo['fileStatus']) {
            case _UPLOADS_STATUS_REJECTED:
                $fileInfo['fileStatusName'] = xarML('Rejected');
                break;
            case _UPLOADS_STATUS_APPROVED: 
                $fileInfo['fileStatusName'] = xarML('Approved');
                break;
            case _UPLOADS_STATUS_SUBMITTED: 
                $fileInfo['fileStatusName'] = xarML('Submitted');
                break;
            default:
                $fileInfo['fileStatusName'] = xarML('Unknown!');
                break;
        }
        
        $fileList[$fileInfo['fileId']] = $fileInfo;
        $result->MoveNext();
    }
    
    $result->Close();

    return $fileList;
}

?>
