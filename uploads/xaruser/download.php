<?php
function uploads_user_download()
{
    //get filter
    //TODO: Place correct filters, only fileId OR filename is required, not both.  filename is a string
    if (!xarVarFetch('fileId', 'int:1:', $fileId)) return;

    $fileInfo = xarModAPIFunc('uploads','user','db_get_file', array('fileId' => $fileId));
    
    if (!isset($fileInfo[0]) || empty($fileInfo)) {
        $msg = xarML('Unable to retrieve information on file [#(1)]', $fileId);
        xarExceptionSet(XAR_USER_EXCEPTION, 'INVALID_fileId', new SystemException($msg));
        return;
    }
    
    // the file should be the first indice in the array
    $fileInfo = $fileInfo[0];
    
    if (!file_exists($fileInfo['fileLocation'])) {
        $msg = xarML('File [#(1)] does not exist!', $fileInfo['fileName']);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_ERR_NO_FILE', new SystemException($msg));
        xarModAPIFunc('uploads','user','db_delete_file', array('fileId' => $fileId));
        return;
    }
        
    // WARNING: if gzip is active, don't remove that layer
    ob_end_clean();
    
    // Setup headers for browser

//  TODO: Need to look into caching headers -- they should change depending on the browser
//  header("Cache-Control: no-cache, must-revalidate");
//  header("Pragma: no-cache");

    header("Pragma: ");
    header("Cache-Control: ");

    header("Content-type: $fileInfo[fileType]"); 

    header("Content-disposition: attachment; filename=\"$fileInfo[fileName]\"");
    header("Content-length: $fileInfo[fileSize]");

    // Feed file to browser, sending 1K bytes at a time
    /// TODO: Possibly impliment throttling, or customization of the transfer size.
    $fp = fopen($fileInfo['fileLocation'],"rb");
    if(is_resource($fp))   {
        while(!feof($fp))
            echo fread($fp, 4096);
        
    }
    fclose($fp);
    
    /// TODO: Close more cleanly, need to close off core functions, like Logging
    exit();
}
?>
