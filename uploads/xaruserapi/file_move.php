<?php

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

?>