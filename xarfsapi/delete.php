<?php

/** 
 *  Delete a file from the filesystem
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string fileName    The complete path to the file being deleted
 *
 *  @returns TRUE on success, FALSE on error
 */

function filemanager_fsapi_delete( $args ) 
{ 

    extract ($args);
    
    if (!isset($fileName)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'fileName','file_move','filemanager');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (!file_exists($fileName)) {
        // if the file doesn't exist, then we don't need
        // to worry about deleting it - so return true :)
        return TRUE;
    }
    
    if (!unlink($fileName)) {
        $msg = xarML('Unable to remove file: [#(1)]. Please make sure you have sufficient privileges to do so.', $fileName);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_DELETE', new SystemException($msg));
        return FALSE;
    }
    
    return TRUE;
}

?>