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

        if (!file_exists($fileInfo['fileLocation'])) {
            $msg = xarML('File [#(1)] does not exist!', $fileInfo['fileName']);
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_ERR_NO_FILE', new SystemException($msg));
            xarModAPIFunc('uploads','user','db_delete_file', array('fileId' => $fileId));
            return;
        }

        $result = xarModAPIFunc('uploads', 'user', 'file_push', array('fileSize'     => $fileInfo['fileSize'], 
                                                                      'fileType'     => $fileInfo['fileType'], 
                                                                      'fileName'     => $fileInfo['fileName'], 
                                                                      'fileLocation' => $fileInfo['fileLocation']));
        
        if (!$result || xarCurrentErrorType() !== XAR_NO_EXCEPTION) {
            // now just return and let the error bubble up
            return FALSE;
        } 
    
    } else {
        return FALSE;
    }
}
?>
