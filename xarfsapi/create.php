<?php

/**
 *  Creates a file on the filesystem in the specified location with
 *  the specified contents and adds an entry to the new file in the
 *  file_entry table after creations. 
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string     filename        The name of the file
 *  @param   string     fileLocation    The complete path to the file including the filename
 *  @param   string     contents        The contents of the new file
 *  @param   string     fromType        If contents are empty, where to grab the contents from (file|db)
 *  @param   resource   fromLocation    If fromtype is file, this is the location of the file to use as the contents
 *  @param   integer    fromDbFileId    IF fromtype is db, then we're dumping a file from the db into a real file
 *
 *  @return  integer The fileId of the newly created file, or ZERO (FALSE) on error
 */

function uploads_fsapi_create( $args )
{

    $fileName       = NULL;
    $fileLocation   = NULL;
    $contents       = NULL;
    $fromType       = NULL;

    extract($args);
    
    if (file_exists($fileLocation)) {
        $msg = xarML('File [#(1)] already exists.', $fileLocation);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_FILE_EXIST', new SystemException($msg));
        return FALSE;
    }

    $path = dirname($fileLocation);

    if (!is_readable($path) || !is_writable($path)) {
        $msg = xarML('Cannot read and/or write file [#(1)] to directory [#(2)]. Are you sure you have access to read/write to the directory?', $fileName, $path);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_READWRITE', new SystemException($msg));
        return FALSE;
    }
/*    
    if (empty($contents)) {
        if (!isset($fromType)) {
            $msg = xarML('Cannot create a zero byte file.');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_CREATE', new SystemException($msg));
            return FALSE;
        } else {
            if (!eregi('^(file|db)', strtolower($fromType))) {
                $msg = xarML('Incorrect value for paramater #(1) in module #(2) function #(3). Value was: [#(4)] - expected #(5) or #(6).',
                             'fromType', 'uploads', 'fsapi_create', $fromType, 'file', 'db');
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_CREATE', new SystemException($msg));
                return FALSE;
            } else {
                switch (strtolower($fromType)) {
                    case 'file':
                        
*/                
}

?>
