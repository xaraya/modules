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
 
function uploads_userapi_db_get_file( $args )  
{
    
    extract($args);
    
    if (!isset($fileId) && !isset($fileName) && !isset($fileStatus) && !isset($fileLocation) &&
        !isset($userId)  && !isset($fileType) && !isset($store_type)) {            
        $msg = xarML('Missing parameters for function [#(1)] in module [#(2)]', 'db_get_file', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }        
    
    $where = array();
    
    if (!isset($inverse)) {
        $inverse = FALSE;
    }
    
    if (isset($fileId)) {
        if (is_array($fileId)) {
            $where[] = 'xar_fileEntry_id IN (' . implode(',', $fileId) . ')';
        } elseif (!empty($fileId)) {
            $where[] = "xar_fileEntry_id = $fileId";
        }
    }
    
    if (isset($fileName) && !empty($fileName)) {
        $where[] = "(xar_filename LIKE '$fileName')";
    }

    if (isset($fileStatus) && !empty($fileStatus)) {
        $where[] = "(xar_status = $fileStatus)";
    }

    if (isset($userId) && !empty($userId)) {
        $where[] = "(xar_user_id = $userId)";
    } 

    if (isset($store_type) && !empty($store_type)) {
        $where[] = "(xar_store_type = $store_type)";
    }
    
    if (isset($fileType) && !empty($fileType)) {
        $where[] = "(xar_mime_type LIKE '$fileType')";
    }

    if (isset($fileLocation) && !empty($fileLocation)) {
        $where[] = "(xar_location LIKE '$fileLocation')";
    }

    if (count($where) > 1) {
        if ($inverse)  {
            $where = implode(' OR ', $where);
        } else {
            $where = implode(' AND ', $where);
        }
    } else {
        $where = implode('', $where);
    }
    
    if ($inverse) {
        $where = "NOT ($where)";
    }
    
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
    
    $importDir = xarModGetVar('uploads','path.imports-directory');
    $uploadDir = xarModGetVar('uploads','path.uploads-directory');
    
    // remove the '/' from the path
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
    
    return $fileList;
}

?>
