<?php

/** 
 *  Processes incoming files (uploades / imports)
 *
 *  @author Carl P. Corliss (aka Rabbitt)
 *  @access public
 *  @param
 */
 
function uploads_userapi_process_files( $args ) {

    extract($args);
    // If we have an import then verify the information given
    if (!isset($importFrom)) {
        $importFrom = NULL;
        $import_path_override = NULL;
    } else {
        if (isset($import_path_override) && file_exists($import_path_override)) {
            $import_directory = $import_path_override;
    } else {
            $import_directory = xarModGetVar('uploads','path.imports-directory');
        }
    }
    
    // if there is an upload_path_override, use that
    if (isset($upload_path_override) && file_exists($upload_path_override)) {
        $upload_directory = $upload_path_override;
    } else {
        $upload_directory = xarModGetVar('uploads','path.uploads-directotry');
    }
    
    if (isset($upload_obfuscate_override) && $upload_obfuscate_override) {
        $upload_obfuscate = TRUE;
    } else {
        $upload_obfuscate = FALSE;
    }
    
    if (isset($import_obfuscate_override) && $import_obfuscate_override) {
        $import_obfuscate = TRUE;
    } else {
        $import_obfuscate = FALSE;
    }
    
    
    if (!isset($store_type)) {
        $store_type = _UPLOADS_STORE_DB_ENTRY | _UPLOADS_STORE_FILESYSTEM;
    }
    
    /**
     * Prepare the uploaded filelist
     */

    $fileList = array();
    
    if (is_array($_FILES) && count($_FILES) > 0) {
        foreach ($_FILES as $file) {
            $file = xarModAPIFunc('uploads','user','process_upload', 
                                   array('fileInfo'  => $file,
                                         'savePath'  => $upload_directory,
                                         'obfuscate' => $upload_obfuscate));
    
            if (!$file) {
                return; // Pass the exception up.
            } else { 
                $fileList[] = $file;
            }            
        }
    }
    
    $imports = array();
    
    /**
     * Prepare the filelist of imports
     */    
    if (isset($importFrom) && strlen($importFrom)) {
        
        /**
         * if the importFrom is an url, then
         * we can't descend (obviously) so set it to FALSE
         */
        if (eregi('^([a-z]*)?\:\/\/', $importFrom)) {
            $descend = FALSE;
        } else {
            $descend = TRUE;
        }
                   
        $imports = xarModAPIFunc('uploads','user','import_get_filelist',
                                  array('fileLocation' => $importFrom,
                                        'descend'  => $descend));
          
        $imports = xarModAPIFunc('uploads','user','import_prepare_files',
                                  array('fileList'  => $imports,
                                        'savePath'  => $import_directory,
                                        'obfuscate' => $import_obfuscate));
        
        // TODO: think about the return values - if there 
        //       is an error in one of the two api funcs above, we
        //       need to think about catching them
    }
    $fileList = array_merge($fileList, $imports);
    $fileErrors = array();
    $filesAdded = array();
    //    echo "<br /><pre> fileList => "; print_r($fileList); echo "</pre>"; exit();        
    foreach ($fileList as $fileName => $fileInfo) {
        // If this is just a file dump, return the dump
        if ($store_type & _UPLOADS_STORE_TEXT) {
            return xarModAPIFunc('uploads','user','file_dump', $fileInfo);
        }
        
        if ($store_type & _UPLOADS_STORE_FILESYSTEM) {
            if (!xarModAPIFunc('uploads','user','file_move', 
                                array('fileSrc'        => $fileInfo['fileSrc'], 
                                      'fileDest'   => $fileInfo['fileDest']))) {
                // Catch the exception, and create an error list for each file that
                // has an error associated with it 
                $errorObj = xarExceptionValue();

                if (is_object($errorObj)) {
                    $fileErrors[$fileInfo['fileSrc']] = array('fileName' => $fileInfo['fileName'],
                                                              'errMsg'   => $errorObj->getShort(),
                                                              'errID'    => $errorObj->getID());
                } else {
                    $fileErrors[$fileInfo['fileSrc']] = array('fileName' => $fileInfo['fileName'],
                                                              'errMsg'   => 'Unknown Error!',
                                                              'errID'    => -1);
                }
                // clear the exception
                xarExceptionHandled();
               
                continue;
            } else {
                // Now add the file to the array of added files
                $filesAdded[$fileInfo['fileSrc']] = array('fileLocation' => $fileInfo['fileDest'], 
                                                          'fileName'     => $fileInfo['fileName']);
            }
        }
        
        // If the store db_entry bit is set, then go ahead 
        // and set up the database meta information for the file
        if ($store_type & _UPLOADS_STORE_DB_ENTRY) {

            $fileInfo['fileLocation'] =& $fileInfo['fileDest'];
            $fileInfo['store_type']   = $store_type ^ _UPLOADS_STORE_DB_ENTRY;
                        
            $fileId = xarModAPIFunc('uploads','user','db_add_file', $fileInfo);
            

            // If there wasn't a fileID returned it means there was an error, 
            // so, record the error for that particular file and continue on
            // Errors will be tabulated at the end and passed on to the user
            // for manual inspection
            if (!$fileId) {
                // store the file that had an error (and it's error) in an array for later display
                $errorObj = xarExceptionValue();
                
                if (is_object($errorObj)) {
                    $fileErrors[$fileInfo['fileSrc']] = array('fileName' => $fileInfo['fileName'],
                                                              'errMsg'   => $errorObj->getShort(),
                                                              'errID'    => $errorObj->getID());
                } else {
                    $fileErrors[$fileInfo['fileSrc']] = array('fileName' => $fileInfo['fileName'],
                                                              'errMsg'   => 'Unknown Error!',
                                                              'errID'    => -1);
                }
                // Clear the exception because we've handled it already
                xarExceptionHandled();
                
                // Remove the file cuz we we weren't able to add it to the db
                if (isset($filesAdded[$fileInfo['fileSrc']])) {
                    $file = $filesAdded[$fileInfo['fileSrc']]['fileLocation'];
                    if (!xarModAPIFunc('uploads','user','delete', array('fileName' => $file))) {
                        return;
                    }
                }
                
                continue;
                
            } else {
                // store the added files fileID in an array for later use
                $filesAdded[$fileInfo['fileSrc']] = array('fileName' => $fileInfo['fileName'], 
                                                          'fileId'   => $fileId);
            }
        } 
        
        if ($store_type & _UPLOADS_STORE_DB_DATA) {
            // TODO: add the file's contents to the database
            continue;
        }
    }
    
    return array('errors' => $fileErrors,
                 'added'  => $filesAdded);
}

?>
