<?php

/**
 *  Prepares a list of files that have been uploaded, creating a structure for
 *  each file with the following parts:
 *      * fileType  - mimetype
 *      * fileSrc   - the source location of the file
 *      * fileSize  - the filesize of the file
 *      * fileName  - the file's basename
 *      * fileDest  - the (potential) destination for the file (filled in even if stored in the db and not filesystem)
 *  Any file that has errors will have it noted in the same structure with error number and message in:
 *      * errors[]['errorMesg']
 *      * errors[]['errorId']
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string  savePath             Complete path to directory in which we want to save this file
 *  @returns boolean                      TRUE on success, FALSE on failure
 */


function uploads_userapi_prepare_uploads( $args )
{
    $fileList = array();
    $fileInfo = NULL;
    $savePath = NULL;
    $totalBytes = 0;
    $list  =  array();
    extract($args);
    
    if (!xarVarFetch('', 'array:1:', $_FILES)) return;
        
    // Refactor the _FILES array into one simple 
    // numerically indexed array if needbe
    if (isset($_FILES) && is_array($_FILES)) {
        
        foreach ($_FILES as $key => $file) {
            if (isset($file['name']) && !empty($file['name'])) {

                // Check for multiple uploads under
                // the same input tag attribute name
                if (is_array($file['name'])) {
                    // If we have multiple, reorganize them into:
                    // uploads[0..n][{name,type,tmp_name,error,size}]
                    // instead of the way php does it.
                    foreach ($_FILES[$key] as $infoName => $infoList) {
                        foreach($infoList as $id => $info) {
                            // Keep a running tally of the total 
                            // size of bytes used during upload
                            if ('size' == $infoName) {
                                $totalBytes += $info;
                            }
                            // recreate the fileList array
                            $fileList[$id][$infoName] = $info;
                        }
                    }
                } else {
                    
                    // Otherwise, add the file to the file list
                    // and add the byte size to the total
                    $fileList[] = $file;
                    $totalBytes += $file['size'];
                }
            } 
        }
    } else {
        // If there are no files, return an empty array
        return $fileList;
    }

    foreach ($fileList as $id => $file) {

        // If there is no temp file name, then there is no file
        // so unset the indice and continue on...
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            unset($fileList[$id]);
            continue;
        } else {
            $file['source'] = $file['tmp_name'];
            unset($file['tmp_name']);
        }
            
        // PHP 4.1.2 doesn't support this field yet
        if (!isset($file['error'])) {
            $file['error'] = 0;
        }
    
        if (!isset($saveFSPath)) {
            $saveFSPath = xarModGetVar('uploads', 'path.untrust');
        }
    
        // Check to see if we're importing and, if not, check the file and ensure that it
        // meets any requirements we might have for it. If it doesn't pass the tests,
        // then return FALSE
        if (!xarModAPIFunc('uploads','user','validate_upload', array('fileInfo' => $file))) {
            $errorObj = xarCurrentError();
    
            if (is_object($errorObj)) {
                $fileError['errorMesg'] = $errorObj->getShort();
                $fileError['errorId']   = $errorObj->getID();
            } else {
                $fileError['errorMesg'] = 'Unknown Error!';
                $fileError['errorId']   = _UPLOADS_ERROR_UNKNOWN;
            }
            $file['errors']      = $fileError;
    
            // clear the exception
            xarErrorHandled();
        }
    
        $obf_fileName = xarModAPIFunc('uploads', 'fs', 'obfuscate_name',
                                       array('name' => $file['name']));
    
        $file['destination'] = $saveFSPath . '/' . $obf_fileName;
        $file['isUpload'] = TRUE;
        $file['nameHash'] = $obf_fileName;
    
        $fileList[$id] = $file;
    }

    return $fileList;
}
?>
