<?php

/* File: $Id
 * ----------------------------------------------------------------------
 * Xaraya eXtensible Management System
 * Copyright (C) 2002 by the Xaraya Development Team.
 * http://www.xaraya.org
 * ----------------------------------------------------------------------
 * Original Author of file: Marie Altobelli (Ladyofdragons)
 * Purpose of file:  uploads user API
 * ---------------------------------------------------------------------- 
 */ 
 
define('_UPLOADS_STORE_FILESYSTEM', 1);
define('_UPLOADS_STORE_DATABASE',2);

define('_UPLOADS_STATUS_SUBMITTED',1);
define('_UPLOADS_STATUS_APPROVED',2);
define('_UPLOADS_STATUS_REJECTED',3);

/**
 *  Retrieve the metadata stored for a particular file based on either 
 *  the file id or the file name.
 * 
 * @author Carl P. Corliss
 * @author Micheal Cortez
 * @access public
 * @param  integer  file_id     (Optional) grab file with the specified file id
 * @param  array    file_ids    (Optional) grab files with the specified file ids
 * @param  string   fileName    (Optional) grab file(s) with the specified file name
 * @param  integer  status      (Optional) grab files with a specified status  (SUBMITTED, APPROVED, REJECTED)
 * @param  integer  user_id     (Optional) grab files uploaded by a particular user
 * @param  integer  store_type  (Optional) grab files with the specified store type (FILESYSTEM, DATABASE)
 * @param  integer  mime_type   (Optional) grab files with the specified mime type 
 *
 * @returns array   All of the metadata stored for the particular file
 */
 
function uploads_userapi_db_get_fileEntry( $args )  {
    
    extract($args);
    
    if (!isset($file_id) && !isset($fileName) && !isset($file_ids) && 
        !isset($status)  && !isset($user_id)  && !isset($mime_type) && !isset($store_type)) {            
        $msg = xarML('Missing parameters for function [(#(1)] in module [#(2)]', 'get', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }        
    
    $where = '';
    
    if (isset($file_ids) && is_array($file_ids)) {
        $where[] 'xar_fileEntry_id IN (' . implode(',', $file_ids) . ')';
    }
    
    if(isset($file_id) && !isset($file_ids)) {
        $where[] = "xar_fileEntry_id = $file_id";
    }

    if (isset($fileName)) {
        $where[] = "(xar_filename = '$fileName')";
    }
        
    if (isset($status)) {
        $where[] = "(xar_status = $status)";
    }

    if (isset($user_id)) {
        $where[] = "(xar_user_id = $user_id)";
    }

    if (isset($store_type)) {
        $where[] = "(xar_store_type = $store_type)";
    }
    
    if (isseT($mime_type)) {
        $where[] = "(xar_mime_type = $mime_type)";
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
    
   return $result->GetRowAssoc(false);
}

/**
 * Retrieve the metadata stored for all files in the database
 * 
 * @author Carl P. Corliss
 * @author Micheal Cortez
 * @access public
 *
 * @returns array   All of the metadata stored for the particular file
 */
 
function uploads_userapi_db_get_all_fileEntries( /* VOID */ ) {
    
    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable     = xarDBGetTables();
        
        // table and column definitions
    $fileEntry_table = $xartable['file_entry'];

    $sql = "SELECT xar_fileEntry_id, 
                   xar_user_id, 
                   xar_filename, 
                   xar_location, 
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
    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        $row['xar_user_name'] = xarUserGetVar('name',$row['xar_user_id']);
        $filelist[] = $row;
        $result->MoveNext();
    }

    $result->Close();

                                                                    
   return $filelist;
}

/** 
 *  Adds a file (fileEntry) entry to the database. This entry just contains metadata 
 *  about the file and not the actual DATA (contents) of the file.
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer user_id    The id of the user whom submitted the file
 *  @param   string  filename   The name of the file (minus any path information)
 *  @param   string  location   The complete path to the file including the filename (obfuscated if so chosen)
 *  @param   string  mime_type   The mime content-type of the file
 *  @param   integer status     The status of the file (APPROVED, SUBMITTED, READABLE, REJECTED)
 *  @param   integer store_type The manner in which the file is to be stored (filesystem, database)
 *
 *  @returns integer The id of the fileEntry that was added, or FALSE on error
 */

function uploads_userapi_db_add_fileEntry( $args ) {
    
    extract($args);
    
    if (!isset($filename)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module (#3)]', 
                     'filename','db_add_fileEntry','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (!isset($location)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module (#3)]', 
                     'location','db_add_fileEntry','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (!isset($user_id)) {
        $user_id = xarSessionGetVar('uid');
    }
    
    if (!isset($status)) {
        $status = _UPLOADS_SUBMITTED;
    }
    
    if (!isset($store_type)) {
        $store_stype = _UPLOADS_STORE_FILESYSTEM;
    }
    
    if (!isset($mime_type)) {
        $mime_type = xarModAPIFunc('mime','user','analyze_file', array('fileName' => $location));
        if (empty($mime_type)) {
            $mime_type = 'application/octet-stream';
        }
    }
    
    //add to uploads table
    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();


    // table and column definitions
    $fileEntry_table = $xartable['file_entry'];
    $file_id    = $dbconn->GenID($fileEntry_table);

    // insert value into table
    $sql = "INSERT INTO $fileEntry_table 
                      ( 
                        xar_fileEntry_id, 
                        xar_user_id, 
                        xar_filename, 
                        xar_location, 
                        xar_status,
                        xar_filesize,
                        xar_store_type, 
                        xar_mime_type
                      ) 
               VALUES 
                      (
                        $file_id,
                        $user_id,'" .
                        xarVarPrepForStore($filename) . "', '" .
                        xarVarPrepForStore($location) . "', 
                        $status, " .
                        filesize($location) . ", '" .
                        xarVarPrepForStore($mime_type) . "', 
                        $store_type
                      )";
                      
    $result = &$dbconn->Execute($sql);

    if (!$result) {
        return FALSE;
    } else {
        $id = $dbconn->PO_Insert_ID($xartable['file_entry'], 'xar_cid');
        return $id;
    }
}

/** 
 *  Remove a file entry from the database. This just removes any metadata about a file 
 *  that we might have in store. The actual DATA (contents) of the file (ie., the file 
 *  itself) are removed via either file_delete() or db_delete_fileData() depending on 
 *  how the DATA is stored.
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer file_id    The id of the file we are deleting
 *
 *  @returns integer The number of affected rows on success, or FALSE on error
 */

function uploads_userapi_db_delete_fileEntry( $args ) {
    extract($args);
    
    if (!isset($file_id)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module (#3)]', 
                     'file_id','db_delete_fileEntry','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    //add to uploads table
    // Get database setup
    list($dbconn)   = xarDBGetConn();
    $xartable       = xarDBGetTables();

    // table and column definitions
    $fileEntry_table   = $xartable['file_entry'];
    
    // insert value into table
    $sql = "DELETE FROM $fileEntry_table
                  WHERE xar_fileEntry_id = $file_id";
                  
                      
    $result = &$dbconn->Execute($sql);
    
    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->Affected_Rows();
    }

}

/** 
 *  Modifies a file's metadata stored in the database
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer file_id    The id of the file we are modifying
 *  @param   integer user_id    (optional) The id of the user whom submitted the file
 *  @param   string  filename   (optional) The name of the file (minus any path information)
 *  @param   string  location   (optional) The complete path to the file including the filename (obfuscated if so chosen)
 *  @param   integer status     (optional) The status of the file (APPROVED, SUBMITTED, READABLE, REJECTED)
 *  @param   string  mime_type  (optional) The mime content-type of the file
 *  @param   integer store_type (optional) The manner in which the file is to be stored (filesystem, database)
 * 
 *  @returns integer The number of affected rows on success, or FALSE on error
 */

function uploads_userapi_db_modify_fileEntry( $args ) {
    extract($args);
    
    $update_fields = array();
    
    if (!isset($file_id)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module (#3)]', 
                     'file_id','db_modify_fileEntry','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (isset($filename)) {
        $update_fields[] = "xar_filename='$filename'";
    }
    
    
    if (isset($location)) {
        $updtae_fields[] = "xar_location='$location;";
    }
    
    if (isset($user_id)) {
        $update_fields[] = "xar_user_id = $user_id";
    }
    
    if (isset($status)) {
        $update_fields[] = "xar_status = $status";
    }
    
    if (isset($store_type)) {
        $update_fields[] = "xar_store_type = $store_type";
    }
    
    if (isset($mime_type)) {
        $update_fields[] = "xar_mime_type = '$mime_type'";
    }
    
    if (!count($update_fields)) {
        return TRUE;
    }
    
    //add to uploads table
    // Get database setup
    list($dbconn)    = xarDBGetConn();
    $xartable        = xarDBGetTables();

    $fileEntry_table = $xartable['file_entry'];
    
    $update_string   = implode(', ', $update_fields);
                          
    $sql             = "UPDATE $fileEntry_table 
                           SET $update_string
                         WHERE xar_fileEntry_id = $file_id";
    
        
    $result          = &$dbconn->Execute($sql);

    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->Affected_Rows();
    }

}

/** 
 *  Process a newly uploaded file, verifying that it meets any requirements we might have
 *  imposed on it (based on it's content-type, move the file to it's proper place (/dev/null if 
 *  need be), and update the database.
 *
 *
 *  Basically 3 steps involved here:
 *  BEGIN
 *      if not importing
 *          - check validity of upload
 *              - check for errors uploading
 *              - check that we have space to save file (quota check?)
 *              - check content-type
 *              - run checks based on content-type
 *      - process file
 *          - if file not accepted:
 *              - delete the temp file GOTO END
 *          - otherwise:
 *              - obfuscate filename
 *              - move file from /tmp to uploads directory
 *      - update database
 *          - add entry for upload in database
 * (on hold)- if we're storing the file in the db
 *            add the contents of the file to the blob table
 *  END
 *
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   boolean obfuscate_fileName   whether or not to obfuscate the filename
 *  @param   string  savePath             Complete path to directory in which we want to save this file
 *  @param   boolean import               Is this an import or not? (dictates whether or not the file is validated or not)
 *  @param   array   fileInfo             An array containing (name, type, tmp_name, error and size):
 *                   fileInfo['name']     The name of the file (minus any path information)
 *                   fileInfo['type']     The mime content-type of the file
 *                   fileInfo['tmp_name'] The temporary file name (complete path) of the file
 *                   fileInfo['error']    Number representing any errors that were encountered during the upload
 *                   fileInfo['size']     The size of the file (in bytes)
 *  @returns boolean                      TRUE on success, FALSE on failure
 */

function uploads_userapi_prcoess_upload( &$args ) {

    extract ( $args );

    if (!isset($import)) {
        $import = FALSE;
    }
    
    if (!isset($obfuscate_fileName)) {
        $obfuscate_fileName = xarModGetVar('uploads','file.obfuscate-name');;
    }
    
    if (!isset($savePath)) {
        if ($import) {
            $savePath = xarModGetVar('uploads', 'path.imports-directory');
        } else {
            $savePath = xarModGetVar('uploads', 'path.uploads-directory');
        }
    }
         
    if (!isset($fileInfo) || !is_array($fileInfo)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'fileInfo','process_upload','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
        
    $fileInfo['filename'] = $fileInfo['tmp_name'];
    
    // Check to see if we're importing and, if not, check the file and ensure that it 
    // meets any requirements we might have for it
    if ((FALSE == $import) && 
            !xarModAPIFunc('uploads','user','validate_upload', 
                            array('fileInfo' => $fileInfo))) {
        // doh - looks like the file didn't pass the validation tests so now
        // we delete it from the /tmp directory. Note: if it fails, we don't need to
        // set an exception as the file_delete() function will do that for us :)
        xarModAPIFunc('uploads','user','file_delete', 
                       array('fileName' => $fileInfo['tmp_name']));
        return FALSE;
    } else {
        
        if ($store_type == _UPLOADS_STORE_FILESYSTEM) {
            // Check to see if we were asked to obfuscate the file's name
            if ($obfuscate_fileName) {
                $obf_fileName = xarModAPIFunc('uploads','user','file_obfuscate_name', 
                                            array('fileName' => $fileInfo['name']));

                if (empty($obf_fileName) || FALSE === $obf_fileName) {
                    $msg = xarML('Could not obfuscate filename!');
                    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_OBFUSCATE', new SystemException($msg));
                    return FALSE;
                } else {
                    $fileInfo['filename'] = $obf_fileName;
                }

            } else {
                // if we're not obfuscating it, 
                // just use the name of the uploaded file
                $fileInfo['filename'] = $fileInfo['name'];
            }

            // Move the file from the TEMP directory to it's final resting spot
            // if it fails just return - the error will be set by the file_move() function
            // TODO: Think about catching the error, if possible, and handling it 
            if (!xarModAPIFunc('uploads','user','file_move', 
                                array('fileSource' => $fileInfo['tmp_name'], 
                                      'fileDestination'   => $savePath . '/' . $fileInfo['filename']))) {
                return FALSE;
            }
        }  
        
        // Create the entry in the database for this file, returning false if it could
        // not be created. The function db_create() will take care of setting the exception
        // for us, so no need to do that here.
        $file_id = xarModAPIFunc('uploads','user','db_add_fileEntry', array('fileInfo' => $fileInfo));
        
        if (!$file_id) {
            return FALSE;
        } else {
            // If we are storing this file in the database, go ahead and drop it in
            // again, if there is an error, we just return as the function called will
            // set the appropriate exception
            if (_UPLOADS_STORE_DATABASE == $store_type) {
                if (!xarModAPIFunc('uploads','user','db_file_store', array('fileInfo' => $fileInfo))) {
                    return;
                }
            }
        }                
    
        
        // Everything worked out perfectly - return true :)
        return TRUE;
    }
}

/** 
 *  Validates file based on criteria specified by hooked modules (well, that's the intended future 
 *  functionality anyhow - which won't be available until the hooks system has been revamped......
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   array   fileInfo             An array containing (name, type, tmp_name, error and size):
 *                   fileInfo['name']     The name of the file (minus any path information)
 *                   fileInfo['type']     The mime content-type of the file
 *                   fileInfo['tmp_name'] The temporary file name (complete path) of the file
 *                   fileInfo['error']    Number representing any errors that were encountered during the upload
 *                   fileInfo['size']     The size of the file (in bytes)
 *  @returns boolean                      TRUE if checks pass, FALSE otherwise 
 */

function uploads_userapi_validate_upload( $args ) {

    extract ($args);
    
    if (!isset($fileInfo)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'fileInfo','validate_upload','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }        

    // Check to see if the mime-type is allowed
    $censored_mime_types = unserialize(xarModGetVar('uploads','file.censored-mime-types'));
    $mime_type = xarModAPIFunc('mime','user','analyze_file', 
                                array('fileInfo' => $fileInfo));
    if (in_array($mime_type, $censored_mime_tyeps)) {
        $msg = xarML('Unable to save uploaded file - File type is not allowed!');
        xarExceptionSet(XAR_USER_EXCEPTION, 'UPLOADS_FILE_NOT_ALLOWED', new SystemException($msg));
        return FALSE;
    }
    
    // Run the file specific validation routines. validate_file will set an exception
    // if the check doesn't pass so no need to set an exception here :)
    if (!xarModAPIFunc('uploads','user','validate_file', array('fileInfo' => $fileInfo))) {
        return FALSE;
    }
    
    // future functionality - ...
    // if (!xarModCallHooks('item', 'validation', array('type' => 'file', 'fileInfo' => $fileInfo))) {
    //     return FALSE;
    // }
    
    return TRUE;
}

/**
 *  Check an uploaded file for valid mime-type, and any errors that might 
 *  have been encountered during the upload
 *
 *  @author  Carl P. Corliss
 *  @access  private
 *  @param   array   fileInfo             An array containing (name, type, tmp_name, error and size):
 *                   fileInfo['name']     The name of the file (minus any path information)
 *                   fileInfo['type']     The mime content-type of the file
 *                   fileInfo['tmp_name'] The temporary file name (complete path) of the file
 *                   fileInfo['error']    Number representing any errors that were encountered during the upload
 *                   fileInfo['size']     The size of the file (in bytes)
 *  @returns boolean            TRUE if it passed the checks, FALSE otherwise
 */
function uploads_userapi_validate_file ( $args ) {
                
    extract ($args);
    
    if (!isset($fileInfo)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'fileInfo','validate_file','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }        
    
    // TODO: add functionality to validate properly formatted filename
    
    switch ($fileInfo['error'])  {
        
        case 1: // The uploaded file exceeds the upload_max_filesize directive in php.ini 
            $msg = xarML('File size exceeds the maximum allowable based on your system settings.');
            xarExceptionSet(XAR_USER_EXCEPTION, 'UPLOAD_ERR_INI_SIZE', new SystemException($msg));
            return FALSE;
        
        case 2: // The uploadehttp://www.cnn.com/d file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form
            $msg = xarML('File size exceeds the maximum allowable.');
            xarExceptionSet(XAR_USER_EXCEPTION, 'UPLOAD_ERR_FORM_SIZE', new SystemException($msg));
            return FALSE;
        
        case 3: // The uploaded file was only partially uploaded
            $msg = xarML('The file was only partially uploaded.');
            xarExceptionSet(XAR_USER_EXCEPTION, 'UPLOAD_ERR_PARTIAL', new SystemException($msg));
            return FALSE;
        
        case 4: // No file was uploaded
            $msg = xarML('No file was uploaded..');
            xarExceptionSet(XAR_USER_EXCEPTION, 'UPLOAD_ERR_NO_FILE', new SystemException($msg));
            return FALSE;
        default:
        case 0  // no error
            break;
    }


    if (!is_uploaded_file($fileInfo['tmp_name'])) {
        $msg = xarML('Possible attempted malicious file upload.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'UPLOAD_ERR_MAL_ATTEMPT', new SystemException($msg));
        return FALSE;
    }        
    
    return TRUE;
}


/** 
 *  Obscures the given filename for added security
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   <type>
 *  @returns <type> 
 */

function uploads_userapi_file_obfuscate_name( $args ) {

    extract ($args);
    
    if (!isset($fileName) || empty($fileName)) {
        return FALSE;
    }
    $hash = crypt($fileName, substr(md5(time() . $fileName . getmypid()), 0, 2));
    $hash = substr(md5($hash), 0, 8) . time() . getmypid();
    
    return $hash;
    
}

/** 
 *  Move a file from one location to another
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string fileSource      Complete path to source file
 *  @param   string fileDestination Complete path to destination 
 *  @returns boolean    TRUE on success, FALSE otherwise
 */

function uploads_userapi_file_move( $args ) { 

    extract ($args);
    
    if (!isset($fileSource)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'fileSource','file_move','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }        

    if (!isset($fileDestination)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'fileDestination','file_move','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (!file_exists($fileSource)) {
        $msg = xarML('Unable to move file - Source file does not exist!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NOT_EXIST', new SystemException($msg));
        return FALSE;
    }        
        
    if (!is_readable($fileSource)) {
        $msg = xarML('Unable to move file - Source file is unreadable!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_READ', new SystemException($msg));
        return FALSE;
    }        
        
    if (!file_exists(dirname($fileDestination))  {
        $msg = xarML('Unable to move file - Destination directory does not exist!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NOT_EXIST', new SystemException($msg));
        return FALSE;
    }        
        
    if (is_writable(dirname($fileDestination)) {
        $msg = xarML('Unable to move file - Destination directory is not writable!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_WRITE', new SystemException($msg));
        return FALSE;
    }        
        
    if (disk_free_space(dirname($fileDestination)) <= filesize($fileSource)) {
        $msg = xarML('Unable to move file - Destination drive does not have enough free space!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_SPACE', new SystemException($msg));
        return FALSE;
    }        
    
    if (file_exists($fileDestination) && $force == TRUE) {
        $msg = xarML('Unable to move file - Destination file already exists!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_OVERWRITE', new SystemException($msg));
        return FALSE;
    }
    
    if (!move_uploaded_file($fileSource, $fileDestination)) {
        $msg = xarML('Unable to move file [#(1)] to destination [#(2)].',$fileSource, $fileDestination);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_MOVE', new SystemException($msg));
        return FALSE
    }
    
    return TRUE;
}

/** 
 *  Delete a file from the filesystem
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string fileName    The complete path to the file being deleted
 *
 *  @returns TRUE on success, FALSE on error
 */

function uploads_userapi_file_delete( $args ) { 

    extract ($args);
    
    if (!isset($fileName)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'fileName','file_move','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (!file_exists($fileName)) {
        // if the file doesn't exist, then we don't need
        // to worry about deleting it - so return true :)
        return TRUE;
    }
    
    if (!unlink($fileName)) {
        $msg = xarML('Unable to remove file: [#(1)].', $fileName);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_DELETE', new SystemException($msg));
        return FALSE;
    }
    
    return TRUE;
}

/** 
 *  Rename a file. (alias for file_move)
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   <type>
 *  @returns <type> 
 */

function uploads_userapi_file_rename( $args ) { 
    return xarModAPIFunc('uploads','user','file_move', $args);
}

?>
