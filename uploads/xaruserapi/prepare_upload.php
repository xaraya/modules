<?php

/** 
 *  Process a newly uploaded file, verifying that it meets any 
 *  requirements we might have imposed on it 
 *
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   boolean obfuscate            whether or not to obfuscate the filename
 *  @param   string  savePath             Complete path to directory in which we want to save this file
 *  @param   array   fileInfo             An array containing (name, type, tmp_name, error and size):
 *                   fileInfo['name']     The name of the file (minus any path information)
 *                   fileInfo['type']     The mime content-type of the file
 *                   fileInfo['tmp_name'] The temporary file name (complete path) of the file
 *                   fileInfo['error']    Number representing any errors that were encountered during the upload
 *                   fileInfo['size']     The size of the file (in bytes)
 *  @returns boolean                      TRUE on success, FALSE on failure
 */

function uploads_userapi_process_upload( &$args ) {

    extract ( $args );

    
    /**
     *  Initial variable checking / setup 
     */
    if (isset($obfuscate) && $obfuscate) {
        $obfuscate_fileName = TRUE;
    } else {
        $obfuscate_fileName = xarModGetVar('uploads','file.obfuscate-on-upload');
    }    
    
    if (!isset($savePath)) {
        $savePath = xarModGetVar('uploads', 'path.uploads-directory');
    }
    
    if ((!isset($fileInfo)          || !is_array($fileInfo))      || 
         !isset($fileInfo['name'])  || !isset($fileInfo['type'])  || 
         !isset($fileInfo['error']) || !isset($fileInfo['size'])  || 
         !isset($fileInfo['tmp_name']))  {
            $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                        'fileInfo','process_upload','uploads');
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return FALSE;
    }

    $fileInfo['fileType'] = $fileInfo['type'];
    $fileInfo['fileSrc']    = $fileInfo['tmp_name'];
    $fileInfo['fileSize']   = $fileInfo['size'];
    $fileInfo['fileName']   = $fileInfo['name'];

    // Check to see if we're importing and, if not, check the file and ensure that it 
    // meets any requirements we might have for it. If it doesn't pass the tests,
    // then return FALSE
    if (!xarModAPIFunc('uploads','user','validate_upload', array('fileInfo' => $fileInfo))) {
        return;
        
    } else {
            
        /** 
         *  Start the process of adding an uploaded file
         */
     
        unset($fileInfo['tmp_name']);
        unset($fileInfo['size']);
        unset($fileInfo['name']);
        unset($fileInfo['type']);
        
        $fileInfo['fileType']   = xarModAPIFunc('mime','user','analyze_file', 
                                                 array('fileName' => $fileInfo['fileSrc']));
        
        // Check to see if we need to obfuscate the filename
        if ($obfuscate_fileName) {
            $obf_fileName = xarModAPIFunc('uploads','user','file_obfuscate_name', 
                                           array('fileName' => $fileInfo['fileName']));

            if (empty($obf_fileName) || FALSE === $obf_fileName) {
                $msg = xarML('Could not obfuscate filename!');
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_OBFUSCATE', new SystemException($msg));
                return;
            } else {
                $fileInfo['fileDest'] = $savePath . '/' . $obf_fileName;
            }
        } else {
            // if we're not obfuscating it, 
            // just use the name of the uploaded file
            $fileInfo['fileDest'] = $savePath . '/' . $fileInfo['fileName'];
        }

        // Move the file from the TEMP directory to it's final resting spot
        // if it fails just return - the error will be set by the file_move() function
        // TODO: Think about catching the error, if possible, and handling it 

        // Everything worked out perfectly - return true :)
        return $fileInfo;
    }
    
    return;
}
 
?>
