<?php

/**
 * File: $Id$
 *
 * download a file directly from the repository
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function bkview_user_downloadfile($args) 
{
    xarVarFetch('file','str::',$filename);
    xarVarFetch('repoid','id::',$repoid);
    extract($args);

    // Get the file from the repo and sent it to the browser
    $repo_info = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    $repo =& $repo_info['repo'];
    
    // Gather info about the file and download it
    $fullname = $repo->_root . $filename;
    $basename = basename($filename);
    $size = filesize($fullname);

    // Try to determine mime-type
    // Try different methods
    // 1. php function
    // 2. using mime module
    // TODO: make this configurable
    $mime_type = "application/x-download";
    if(function_exists('mime_content_type')) {
        $mime_type = mime_content_type($fullname);
    } elseif (xarModIsAvailable('mime')) {
        // Use the mime module to determine the mime type
        $mime_type = xarModAPIFunc('mime','user','analyze_file',array('fileName' => $fullname));
    }
    
    $fp = @fopen($fullname, 'rb');
    if(is_resource($fp))   {
        do {
            // Read the data in chunks, apparently this is needed
            // as fpassthru did not seem to work for me
            // TODO: look at htpp 302 to let webserver handle it
            $data = fread($fp, 4096);
            if (strlen($data) == 0) {
                break;
            } else {
                print("$data");
            }
        } while (TRUE);
        
        fclose($fp);
    } else {
        $msg = xarML('Error occurred while opening file: #(1)',$fullname);
        // invalid_file is the closes system exception i could find
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'INVALID_FILE', $msg);
        return;
    }

    // Headers can be here, so we can do error checking in 
    // the code above someday       
    $basename = basename($fullname);
    header("Pragma: ");
    header("Cache-Control: ");
    header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
    header("Content-Description: $basename");
    header("Content-type: $mime_type"); 
    header("Content-disposition: attachment; filename=\"$basename\"");
    header("Content-length: $size");
    header("Connection: close");
    // Flush it an we're at the end
    // TODO: make this a bit more intelligent (for example, displaying it
    // in the module area with inline if we can.
    exit();
}
?>
