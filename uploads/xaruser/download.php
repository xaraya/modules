<?php
function uploads_user_download()
{
    //get filter
    //TODO: Place correct filters, only fileId OR filename is required, not both.  filename is a string
    if (!xarVarFetch('fileId', 'int:1:', $fileId)) return;

    $fileInfo = xarModAPIFunc('uploads','user','db_get_file', array('fileId' => $fileId));
    
    if (empty($fileInfo) || !count($fileInfo)) {
        $msg = xarML('Unable to retrieve information on file [#(1)]', $fileId);
        xarExceptionSet(XAR_USER_EXCEPTION, 'UPLOADS_ERR_NO_FILE', new SystemException($msg));
        return;
    }
    
    // the file should be the first indice in the array
    $fileInfo = end($fileInfo);
    
    $instance[0] = $fileInfo['fileTypeInfo']['typeId'];
    $instance[1] = $fileInfo['fileTypeInfo']['subtypeId'];
    $instance[2] = xarSessionGetVar('uid');
    $instance[3] = $fileId;
    
    $instance = implode(':', $instance);
    
    if (xarSecurityCheck('ViewUploads', 1, 'File', $instance)) {
        if ($fileInfo['storeType'] & _UPLOADS_STORE_FILESYSTEM) {
            if (!file_exists($fileInfo['fileLocation'])) {
                $msg = xarML('File [#(1)] does not exist in FileSystem.', $fileInfo['fileName']);
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_ERR_NO_FILE', new SystemException($msg));
                return;
            }
        } elseif ($fileInfo['storeType'] & _UPLOADS_STORE_DB_FULL) {
            if (!xarModAPIFunc('uploads', 'user', 'db_count_data', array('fileId' => $fileInfo['fileId']))) {
                $msg = xarML('File [#(1)] does not exist in Database.', $fileInfo['fileName']);
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_ERR_NO_FILE', new SystemException($msg));
                return;
            }
        }
        $result = xarModAPIFunc('uploads', 'user', 'file_push', $fileInfo);
        
        if (!$result || xarCurrentErrorType() !== XAR_NO_EXCEPTION) {
            // now just return and let the error bubble up
            return FALSE;
        } 
    
    } else {
        return FALSE;
    }
}
?>
