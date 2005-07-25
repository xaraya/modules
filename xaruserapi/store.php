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
 *  @param   string     fileInfo.source        The source location for the file
 *  @param   string     fileInfo.destination       The (potential) destination for the file (filled in even if stored in the db and not filesystem)
 *  @param   integer    fileInfo.fileSize       The filesize of the file
 *  @returns array      returns the array passed into it modified with the extra attributes received through the storage 
 *                      process. If the file wasn't added successfully, fileInfo.errors is set appropriately
 */

function uploads_userapi_store( $args ) 
{

    extract ( $args );
    
    if (!isset($fileList)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]', 
                     'fileInfo','file_store','uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($vpath)) {
        $dirId = xarModGetVar('uploads', 'folders.public-files');
    } else {
        $vpathInfo = xarModAPIFunc('uploads', 'vdir', 'path_decode', array('vpath' => $vpath));
        $dirId = $vpathInfo['dirId'];
    }
    
    foreach ($fileList as $file) {
        $typeInfo = xarModAPIFunc('mime', 'user', 'get_rev_mimetype', array('mimeType' => $file['type']));
        $instance = array();
        $instance[0] = $typeInfo['typeId'];
        $instance[1] = $typeInfo['subtypeId'];
        $instance[2] = xarSessionGetVar('uid');
        $instance[3] = 'All';
        $instance = implode(':', $instance);
    
        if ( (isset($file['fileStatus']) && $file['fileStatus'] == _UPLOADS_STATUS_APPROVED) || 
             xarSecurityCheck('AddUploads', 1, 'File', $instance)) 
        {
    
            if (!isset($storeType)) {
                $storeType = _UPLOADS_STORE_FSDB;
            }
            
            if (!isset($file['dirId'])) {
                $file['dirId'] = $dirId;
            }
    

            // Always move the file so we can perform operations
            // on it as needed (ie: analyze_file, etc)
            if ($file['source'] != $file['destination']) {
                $result = xarModAPIFunc('uploads', 'fs', 'move', $file);
            }
            
            // Only proceed if the file was successfully moved.
            if (TRUE == $result) {
                
                // If we've made it this far then the source
                // should be the same as the destination
                $file['source'] = $file['destination'];
                
                
                // Next, make sure the file isn't already stored in the db/filesystem
                // if it is, then change it's location to conflict://[dirId]/[file]
                $fInfo = @end(
                    xarModAPIFunc('uploads', 'user', 'db_get_file_entry', 
                        array(
                            'fileLocation'  => 'xarfs://' . $dirId . '/%',
                            'fileName'      => $file['name']
                        )
                    )
                );
        
                // If we already have the file, then return the info we have on it
                if (is_array($fInfo) && count($fInfo)) {
                    $conflict = TRUE;
                } else {
                    $conflict = FALSE;
                }
            
                // If the store db_entry bit is set, then go ahead 
                // and set up the database meta information for the file
                if ($storeType & _UPLOADS_STORE_DB_ENTRY) {
        
                    $file['store_type'] = $storeType;
                    $fileId = xarModAPIFunc('uploads','user','db_add_file', $file);
        
                    if ($fileId) {
                        $file['fileId'] = $fileId;
                    }
                } 
        
                if ($storeType & _UPLOADS_STORE_DB_DATA) {
                    if (!xarModAPIFunc('uploads', 'fs', 'dump', $file)) {
                        // If we couldn't add the files contents to the database,
                        // then remove the file metadata as well
                        if (isset($fileId) && !empty($fileId))  {
                            xarModAPIFunc('uploads', 'user' ,'db_delete_file', array('fileId' => $fileId));
                        }
                    } else {
                        // if it was successfully added, then change the stored location 
                        // to DATABASE instead of uploads/blahblahblah
                        xarModAPIFunc('uploads', 'user', 'db_modify_file', 
                            array(
                                'fileId' => $fileId, 
                                'fileLocation' => 'xardb://'.$fileId.'/'
                            )
                        );
                    }
                }
                
                // Now that we are done adding the file, we process the conflict 
                // if there was one. Files having conflicts are marked as such by 
                // their location being changed to:
                // conflict://<directory id>/<old location scheme>[/<path to file>]
                // If there was no conflict, then we go ahead and set up the 
                // file's association with the containing folder
                if ($fileId) {
                    if (!$conflict) {
                        xarModAPIFunc('uploads', 'user', 'db_add_association',
                            array(
                                'fileid'    => $fileId,
                                'modid'     => xarModGetIDFromName('categories'),
                                'itemtype'  => 0,
                                'itemid'    => $dirId
                            )
                        );
                    } else {
                        if ($storeType & _UPLOADS_STORE_DB_DATA) {
                            $location = 'conflict://' . $dirId . '/xardb';
                        } else {
                            $location = 'conflict://' . $dirId .'/xarfs/' . $file['nameHash'];
                        }
                        echo "<br />Conflict found - setting location to: <b>$location</b>";
                        xarModAPIFunc('uploads', 'user', 'db_modify_file',
                            array(
                                'fileId' => $fileId,
                                'fileLocation' => $location
                            )
                        );
                    }
                }                        
            }
        }
        
        $file['path'] = xarModAPIFunc('uploads', 'vdir', 'path_encode', array('vdir_id' => $dirId)) . "/$file[name]";
        
        // Let's make sure that we have at least an empty errors array
        $file['errors'] = array();
        
        // If there were any errors generated while attempting to add this file, 
        // we run through and grab them, adding them to this file
        while (xarCurrentErrorType() !== XAR_NO_EXCEPTION) {
    
            $errorObj = xarCurrentError();
    
            if (is_object($errorObj)) {
                $fileError = array('errorMesg'   => $errorObj->getShort(),
                                   'errorId'    => $errorObj->getID());
            } else {
                $fileError = array('errorMesg'   => 'Unknown Error!',
                                   'errorId'    => _UPLOADS_ERROR_UNKNOWN);
            }
    
            if (!isset($file['errors'])) {
                $file['errors'] = array();
            }
            
            $file['errors'][] = $fileError;
            
            // Clear the exception because we've handled it already
            xarErrorHandled();
            
        }
        $list[] = $file;
    }    
    return $list;
}
?>