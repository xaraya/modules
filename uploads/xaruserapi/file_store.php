<?php

/**
 *  Takes a files metadata for input and creates the file's entry in the database
 *  as well as storing it's contents in either the filesystem or database
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   array      fileInfo                The metadata for the file being stored
 *  @param   string     fileInfo.fileType       The MIME type for the file
 *  @param   string     fileInfo.fileName       The file's basename 
 *  @param   string     fileInfo.fileSrc        The source location for the file
 *  @param   string     fileInfo.fileDest       The (potential) destination for the file (filled in even if stored in the db and not filesystem)
 *  @param   integer    fileInfo.fileSize       The filesize of the file
 *  @returns array      returns the array passed into it modified with the extra attributes received through the storage 
 *                      process. If the file wasn't added successfully, fileInfo.errors is set appropriately
 */

xarModAPILoad('uploads', 'user');

function uploads_userapi_file_store( $args ) {

    extract ( $args );
    
    if (!isset($fileInfo)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]', 
                     'fileInfo','file_store','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($store_type)) {
            $store_type = _UPLOADS_STORE_FSDB;
    }
        
        echo "<br /><pre> inside: fileInfo => ";print_r($fileInfo); echo "</pre>";
        echo"<br />STORE_TYPE: $store_type";
    // first, make sure the file isn't already stored in the db/filesystem
    // if it is, then don't add it.
    // FIXME: need to rethink how this is handled - maybe give the user a choice
    //        to rename the file ... (rabbitt)
    $fInfo = xarModAPIFunc('uploads', 'user', 'db_get_file', 
                            array('fileName' => $fileInfo['fileName'],
                                  'fileSize' => $fileInfo['fileSize']));
    
    // If we already have the file, then return the info we have on it
    if (is_array($fInfo) && count($fInfo)) {
        return $fInfo;
    }
    
    // If this is just a file dump, return the dump
    if ($store_type & _UPLOADS_STORE_TEXT) {
        $fileInfo['fileData'] = xarModAPIFunc('uploads','user','file_dump', $fileInfo);
    }
    // If the store db_entry bit is set, then go ahead 
    // and set up the database meta information for the file
    if ($store_type & _UPLOADS_STORE_DB_ENTRY) {
            
        $fileInfo['fileLocation'] =& $fileInfo['fileDest'];

        $fileId = xarModAPIFunc('uploads','user','db_add_file', $fileInfo);

        if ($fileId) {
            $fileInfo['fileId'] = $fileId;
        }
    } 

    if ($store_type & _UPLOADS_STORE_FILESYSTEM) {

        $args = array('fileSrc'    => $fileInfo['fileSrc'], 
                      'fileDest'   => $fileInfo['fileDest']);

        if ($fileInfo['fileSrc'] != $fileInfo['fileDest']) {
            $result = xarModAPIFunc('uploads','user','file_move', $args);            
        } else {
            $result = TRUE;
        }
        
        if ($result) {
            $fileInfo['fileLocation'] =& $fileInfo['fileDest'];
        } else {
            // if it wasn't moved successfully, then we should remove 
            // the database entry (if there is one) so that we don't have
            // a corrupted file entry
            if (isset($fileId) && !empty($fileId)) {
                xarModAPIFunc('uploads', 'user', 'db_delete_file', array('fileId' => $fileId));
            } 
            
            $fileInfo['fileLocation'] =& $fileInfo['fileSrc'];
        }
    }

    if ($store_type & _UPLOADS_STORE_DB_DATA) {
        
        if (!xarModAPIFunc('uploads', 'user', 'file_db_store_contents', $fileInfo)) {
            // If we couldn't add the files contents to the database,
            // then remove the file metadata as well
            if (isset($fileId) && !empty($fileId))  {
                xarModAPIFunc('uploads', 'user' ,'db_delete_file', array('fileId' => $fileId));
            }
        }
    }

    // If there were any errors generated while attempting to add this file, 
    // we run through and grab them, adding them to this file
    while (xarCurrentErrorType() !== XAR_NO_EXCEPTION) {

        $errorObj = xarExceptionValue();

        if (is_object($errorObj)) {
            $fileError = array('errorMsg'   => $errorObj->getShort(),
                               'errorID'    => $errorObj->getID());
        } else {
            $fileError = array('errorMsg'   => 'Unknown Error!',
                               'errorID'    => _UPLOADS_ERROR_UNKNOWN);
        }

        if (!isset($fileInfo['errors'])) {
            $fileInfo['errors'] = array();
        }
        
        $fileInfo['errors'][] = $fileError;
        
        // Clear the exception because we've handled it already
        xarExceptionHandled();
        
    }    
    
    return $fileInfo;
}
?>