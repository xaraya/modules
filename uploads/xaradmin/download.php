<?php
function uploads_admin_download()
{
    //get filter
    //TODO: Place correct filters, only file_id OR filename is required, not both.  filename is a string
    if (!xarVarFetch('file_id', 'int:1:', $file_id)) return;

    $fileInfo = xarModAPIFunc('uploads','user','db_get_file', array('file_id' => $file_id));
    
    if (empty($fileInfo)) {
        $msg = xarML('Unable to retrieve information on file [#(1)]', $file_id);
        xarExceptionSet(XAR_USER_EXCEPTION, 'INVALID_FILE_ID', new SystemException($msg));
        return;
    }
    
    if (!file_exists($fileInfo['xar_location'])) {
        $msg = xarML('File [#(1)] does not exist!', $fileInfo['xar_filename']);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_ERR_NO_FILE', new SystemException($msg));
        xarModAPIFunc('uploads','user','db_delete_file', array('file_id' => $file_id));
        return;
    }
        
//    echo "<br /><pre>fileInfo => ";print_r($fileInfo); echo "</pre>"; exit();
    // for them and deal with them.  WARNING: if gzip is active, don't remove that layer
    ob_end_clean();
    
    
    // Setup headers for browser

    /// TODO: Need to look into caching headers -- they should change depending on the browser
//     header("Cache-Control: no-cache, must-revalidate");
//     header("Pragma: no-cache");

    header("Pragma: ");
    header("Cache-Control: ");

    header("Content-type: $fileInfo[xar_mime_type]"); 

    header("Content-disposition: attachment; filename=\"$fileInfo[xar_filename]\"");
    header("Content-length: $fileInfo[xar_filesize]");

    // Feed file to browser, sending 1K bytes at a time
    /// TODO: Possibly impliment throttling, or customization of the transfer size.
    $fp = fopen($fileInfo['xar_location'],"rb");
    if(is_resource($fp))   {
        while(!feof($fp))
            echo fread($fp, 4096);
        
    }
    fclose($fp);
    
    /// TODO: Close more cleanly, need to close off core functions, like Logging
    exit();
}
?>
