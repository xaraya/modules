<?php

/**
 *  Retrieves an external file using the FTP scheme
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   array  uri     the array containing the broken down url information
 *  @returns array          FALSE on error, otherwise an array containing the fileInformation
 */
 
 function uploads_userapi_import_external_ftp( $args ) {
         
    extract($args);
    
    if (!isset($uri)) {
        return; // error
    }
    
    // if no port, use the default port (21)
    if (!isset($uri['port'])) {
        $uri['port'] = 21;
    }
    
    // if user is not set, set it to anonymous and make a best guess
    // at the password based on the user's email address 
    if (!isset($uri['user'])) {
        $uri['user'] = 'anonymous';
        $uri['pass'] = xarUserGetVar('email');
        if (empty($password)) {
            $uri['pass'] = xarModGetVar('mail', 'adminmail');
        }
    } else {
        // otherwise, if the uname is there but the
        // pass isn't, try to use the user's email address
        if (!isset($uri['pass'])) {
            xarUserGetVar('email');
        }
    }
    
    // Attempt to 'best guess' the mimeType 
    $mimeType = xarModAPIFunc('mime', 'user', 'extension_to_mime', array('fileName' => basename($uri['path'])));
    
    // create the URI in the event we don't have the FTP library
    $ftpURI = "$uri[scheme]://$uri[user]:".urlencode($uri['pass'])."@$uri[host]:$uri[port]$uri[path]";
    
    // Set the connection up to not terminate
    // the script if the user aborts
    ignore_user_abort(TRUE);
    
    // Create a temporary file for storing 
    // the contents of this new file
    $tmpName = tempnam(NULL, 'xarul');
    
    // Set up the fileInfo array
    $fileInfo['fileName']     = basename($uri['path']);
    $fileInfo['fileType']     = $mimeType;
    $fileInfo['fileLocation'] = $tmpName;
    $fileInfo['fileSize']     = 0;
    
    
    if (!extension_loaded('ftp')) {
        if (strtoupper(substr(PHP_OS, 0,3) == 'WIN')) {
            if (!@dl('php_ftp.dll')) {
                $ftpLoaded = FALSE;
            }
        } else {
            if (!@dl('ftp.so')) {
                $ftpLoaded = FALSE;
            } 
        }
    } else {
        $ftpLoaded = TRUE;
    }
    
    if ($ftpLoaded) {
        // Conect to the Server and Log in using the credentials we set up
        $ftpId = ftp_connect($uri['host'], $uri['port']); 
        $result = ftp_login($ftpId, $uri['user'], $uri['pass']); 

        if (!$ftpId || !$result) {
            // if the connection failed unlink 
            // the temporary file and log and return an exception
            unlink($tmpName);
            $msg = xarML('Unable to connect to host [#(1):#(2)] to retrieve file [#(3)]', 
                          $uri['host'], $uri['port'], basename($uri['path']));
            xarExceptionSet(XAR_SYSTEM_EXECEPTION, '_UPLOADS_ERR_NO_CONNECT', new SystemException($msg));
        } else {
            if (($tmpId = fopen($tmpName, 'wb')) === FALSE) {
                unlink($tmpName);
                ftp_close($ftpId);
                $msg = xarML('Unable to open temp file to store remote host [#(1):#(2)] file [#(3)]', 
                            $uri['host'], $uri['port'], basename($uri['path']));
                xarExceptionSet(XAR_SYSTEM_EXECEPTION, '_UPLOADS_ERR_NO_OPEN', new SystemException($msg));
            } else {

                // Note: this is a -blocking- process - the connection will NOT resume
                // until the file transfer has finished - hence, the
                // much needed 'ignore_user_abort()' up above
                if (!ftp_fget($ftpId, $tmpId, $uri['path'], $ftpMode)) {
                    unlink($tmpName);
                    ftp_close($ftpId);
                    $msg = xarML('Unable to connect to host [#(1):#(2)] to retrieve file [#(3)]', 
                                $uri['host'], $uri['port'], basename($uri['path']));
                    xarExceptionSet(XAR_SYSTEM_EXECEPTION, '_UPLOADS_ERR_NO_READ', new SystemException($msg));
                } else {
                    ftp_close($ftpId);
                    $fileInfo['size'] = filesize($tmpName);
                }
            }
        }
    // Otherwise we have to do it the "hard" way ;-)
    } else {
        if (($ftpId = fopen($ftpURI, 'rb')) === FALSE) {
            unlink($tmpName);
            $msg = xarML('Unable to connect to host [#(1):#(2)] to retrieve file [#(3)]', 
                          $uri['host'], $uri['port'], basename($uri['path']));
            xarExceptionSet(XAR_SYSTEM_EXECEPTION, '_UPLOADS_ERR_NO_CONNECT', new SystemException($msg));
        } else {
            if (($tmpId = fopen($tmpName, 'wb')) === FALSE) {
                unlink($tmpName);
                fclose($ftpId);
                $msg = xarML('Unable to open temp file to store remote host [#(1):#(2)] file [#(3)]', 
                            $uri['host'], $uri['port'], basename($uri['path']));
                xarExceptionSet(XAR_SYSTEM_EXECEPTION, '_UPLOADS_ERR_NO_OPEN', new SystemException($msg));
            } else {

                // Note: this is a -blocking- process - the connection will NOT resume
                // until the file transfer has finished - hence, the
                // much needed 'ignore_user_abort()' up above
                // much needed 'ignore_user_abort()' up above
                do {
                    $data = fread($ftpId, 65536);
                    if (0 == strlen($data)) {
                        break;
                    } else {
                        if (fwrite($tmpId, $data, strlen($data)) === FALSE) {
                            fclose($ftpId);
                            fclose($tmpId);
                            unlink($tmpName);
                            $msg = xarML('Unable to write to temp file!');
                            xarExceptionSet(XAR_SYSTEM_EXECEPTION, '_UPLOADS_ERR_NO_WRITE', new SystemException($msg));
                            break;
                        }
                    }
                } while (TRUE);
                if (xarCurrentErrorType() === XAR_NO_EXCEPTION) {
                    fclose($ftpId);
                    fclose($tmpId);
                    $fileInfo['fileSize'] = filesize($tmpName);
                }
            }
        }
                    
    }
    $fileInfo['fileSrc'] = $fileInfo['fileLocation'];
        
    $obfuscate_fileName = xarModGetVar('uploads','file.obfuscate-on-upload');
    $savePath = xarModGetVar('uploads', 'path.uploads-directory');
    
    // remoe any trailing slash from the Save Path
    $savePath = preg_replace('/\/$/', '', $savePath);
    
    if ($obfuscate_fileName) {
        $obf_fileName = xarModAPIFunc('uploads','user','file_obfuscate_name', 
                                    array('fileName' => $fileInfo['fileName']));
        $fileInfo['fileDest'] = $savePath . '/' . $obf_fileName;
    } else {
        // if we're not obfuscating it, 
        // just use the name of the uploaded file
        $fileInfo['fileDest'] = $savePath . '/' . $fileInfo['fileName'];
    }
    
    return array($fileInfo['fileLocation'] => $fileInfo);
 }
 
 ?>
