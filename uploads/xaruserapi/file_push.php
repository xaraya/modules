<?php

/**
 *  Pushes a file to the client browser
 * 
 *  @author   Carl P. Corliss
 *  @access   public
 *  @param    string    fileName        The name of the file
 *  @param    string    fileLocation    The full path to the file
 *  @param    string    fileType        The mimetype of the file
 *  @param    long int  fileSize        The size of the file (in bytes)
 *  @returns  boolean                   This function will exit upon succes and, returns False and throws an exception otherwise
 *  @throws   BAD_PARAM                 missing or invalid parameter
 *  @throws   UPLOADS_ERR_NO_READ       couldn't read from the specified file
 */
 
function uploads_userapi_file_push( $args ) {
    
    extract ( $args );
    
    if (!isset($fileName)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]', 
                     'fileName','file_push','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (!isset($fileLocation)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]', 
                     'fileLocation','file_push','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (!isset($fileType)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]', 
                     'fileType','file_push','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    if (!isset($fileSize)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]', 
                     'fileSize','file_push','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    // Close the buffer, saving it's current contents for possible future use
    // then restart the buffer to store the file
    while (ob_get_level()) {
        $pageBuffer[ob_get_level() - 1] = ob_get_contents();
        ob_end_clean();    
    }
    
    // Start buffering for the file
    ob_start();
    
    $fp = @fopen($fileLocation, 'rb');
    if(is_resource($fp))   {
        
        do {
            $data = fread($fp, 65536);
            if (strlen($data) == 0) {
                break;
            } else {
                print("$data");
            }
        } while (TRUE);
        
        fclose($fp);
        
        // Headers -can- be sent after the actual data 
        // Why do it this way? So we can capture any errors and return if need be :)
        // not that we would have any errors to catch at this point but, mine as well
        // do it incase I think of some errors to catch 
        header("Pragma: ");
        header("Cache-Control: ");
        header("Content-type: $fileType"); 
        header("Content-disposition: attachment; filename=\"$fileName\"");
        header("Content-length: $fileSize");
        
        // TODO: evaluate registering shutdown functions to take care of 
        //       ending Xaraya in a safe manner 
        exit();   
        
    } 
    
    // make sure we're starting with a fresh and clean buffer space
    while(@ob_end_clean());
    
    // rebuffer the old page data   
    for ($i = 0, $total = count($pageBuffer); $i < $total; $i++) {
        ob_start();
        echo $pageBuffer[$i];
    } 
    unset($pageBuffer);

    $msg = xarML('Could not open file [#(1)] for reading', $fileName);
    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_READ', new SystemException($msg));
    return FALSE;

}

?>   