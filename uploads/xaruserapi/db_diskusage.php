<?php

/**
 *  Retrieve the total size of disk usage for selected files based on the filters passed in
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
 * @returns integer             The total amount of diskspace used by the current set of selected files
 */
 
function uploads_userapi_db_diskusage( $args )  {
    
    extract($args);
    
    $where = array();
    
    if (!isset($inverse)) {
        $inverse = FALSE;
    }
    
    if (isset($fileId)) {
        if (is_array($fileId)) {
            $where[] = 'xar_fileEntry_id IN (' . implode(',', $fileIds) . ')';
        } elseif (!empty($fileId)) {
            $where[] = "xar_fileEntry_id = $fileId";
        }
    }
    
    if (isset($fileName) && !empty($fileName)) {
        $where[] = "(xar_filename LIKE '$fileName')";
    }

    if (isset($fileStatus) && !empty($fileStatus) && is_numeric($fileStatus)) {
        $where[] = "(xar_status = $fileStatus)";
    }

    if (isset($userId) && !empty($userId) && is_numeric($userId)) {
        $where[] = "(xar_user_id = $userId)";
    } 

    if (isset($store_type) && !empty($store_type) && is_numeric($store_type)) {
        $where[] = "(xar_store_type = $store_type)";
    }
    
    if (isseT($fileType) && !empty($fileType)) {
        $where[] = "(xar_mime_type LIKE '$fileType')";
    }

    if (count($where) > 1) {
        if ($inverse) {
            $where = 'WHERE NOT (' . implode(' OR ', $where) .')';
        } else {
            $where = 'WHERE ' . implode(' AND ', $where);
        }
    } elseif (count($where) == 1) {
        if ($inverse) {
            $where = 'WHERE NOT (' . implode('', $where) . ')';
        } else {
            $where = 'WHERE ' . implode('', $where);
        }
    } else {
        $where = '';
    }
    
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable     = xarDBGetTables();
        
        // table and column definitions
    $fileEntry_table = $xartable['file_entry'];
    
    $sql = "SELECT SUM(xar_filesize) AS disk_usage
              FROM $fileEntry_table
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
    
    return $row['disk_usage'];
}

?>
