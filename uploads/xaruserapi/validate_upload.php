<?php

/** 
 *  Validates file based on criteria specified by hooked modules (well, that's the intended future 
 *  functionality anyhow - which won't be available until the hooks system has been revamped......
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   array   fileInfo               An array containing (fileName, fileType, fileSrc, fileSize, error):
 *                   fileInfo['fileName']   The (original) name of the file (minus any path information)
 *                   fileInfo['fileType']   The mime content-type of the file
 *                   fileInfo['fileSrc']    The temporary file name (complete path) of the file
 *                   fileInfo['error']      Number representing any errors that were encountered during the upload
 *                   fileInfo['fileSize']   The size of the file (in bytes)
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

?>
