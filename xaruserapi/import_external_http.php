<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 *  Retrieves an external file using the http scheme
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   array  uri         the array containing the broken down url information
 *  @param   boolean obfuscate  whether or not to obfuscate the filename
 *  @param   string  savePath   Complete path to directory in which we want to save this file
 *  @return array          FALSE on error, otherwise an array containing the fileInformation
 */

function uploads_userapi_import_external_http( $args )
{

    extract($args);

    if (!isset($uri)) {
        return; // error
    }

    /**
     *  Initial variable checking / setup
     */
    if (isset($obfuscate) && $obfuscate) {
        $obfuscate_fileName = TRUE;
    } else {
        $obfuscate_fileName = xarModGetVar('uploads','file.obfuscate-on-upload');
    }

    if (!isset($savePath)) {
        $savePath = xarModGetVar('uploads', 'path.uploads-directory');
    }

    // if no port, use the default port (21)
    if (!isset($uri['port'])) {
        if ($uri['scheme'] === 'https') {
            $uri['port'] = 443;
        } else {
            $uri['port'] = 80;
        }
    }

    if (!isset($uri['path'])) {
        $uri['path'] = '';
    }

    if (!isset($uri['query'])) {
        $uri['query'] = '';
    }

    if (!isset($uri['fragment'])) {
        $uri['fragment'] = '';
    }
    $total = 0;
    $maxSize = xarModGetVar('uploads', 'file.maxsize');

    // create the URI in the event we don't have the http library
    $httpURI = "$uri[scheme]://$uri[host]:$uri[port]$uri[path]$uri[query]$uri[fragment]";

    // Set the connection up to not terminate
    // the script if the user aborts
    ignore_user_abort(TRUE);

    // Create a temporary file for storing
    // the contents of this new file
    $tmpName = tempnam(NULL, 'xarul');

// TODO: handle duplicates - cfr. prepare_uploads()

    // Set up the fileInfo array
    $fileInfo['fileName']     = basename($uri['path']);
    if (empty($fileInfo['fileName'])) {
        $fileInfo['fileName'] = str_replace('.','_', $uri['host']) . '.html';
    }
    $fileInfo['fileType']     = 'text/plain';
    $fileInfo['fileLocation'] = $tmpName;
    $fileInfo['fileSize']     = 0;

    if (($httpId = fopen($httpURI, 'rb')) === FALSE) {
        $msg = xarML('Unable to connect to host [#(1):#(2)] to retrieve file [#(3)]',
                    $uri['host'], $uri['port'], basename($uri['path']));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, '_UPLOADS_ERR_NO_CONNECT', new SystemException($msg));
    } else {
        if (($tmpId = fopen($tmpName, 'wb')) === FALSE) {
            $msg = xarML('Unable to open temp file to store remote host [#(1):#(2)] file [#(3)]',
                        $uri['host'], $uri['port'], basename($uri['path']));
            xarErrorSet(XAR_SYSTEM_EXCEPTION, '_UPLOADS_ERR_NO_OPEN', new SystemException($msg));
        } else {

            // Note that this is a -blocking- process - the connection will
            // NOT resume until the file transfer has finished - hence, the
            // much needed  'ignore_user_abort()' up above
            do {
                $data = fread($httpId, 65536);
                if (0 == strlen($data)) {
                    break;
                } else {
                    $total += strlen($data);
                    if ($total > $maxSize) {
                        $msg = xarML('File size is greater than the maximum allowable.');
                        xarErrorSet(XAR_SYSTEM_EXCEPTION, '_UPLOADS_ERR_NO_WRITE', new SystemException($msg));
                        break;
                    } elseif (fwrite($tmpId, $data, strlen($data)) !== strlen($data)) {
                        $msg = xarML('Unable to write to temp file!');
                        xarErrorSet(XAR_SYSTEM_EXCEPTION, '_UPLOADS_ERR_NO_WRITE', new SystemException($msg));
                        break;
                    }
                }
            } while (TRUE);

            // if we haven't hit an exception, then go ahead and close everything up
            if (xarCurrentErrorType() === XAR_NO_EXCEPTION) {
                if (is_resource($tmpId)) {
                    @fclose($tmpId);
                }
                $fileInfo['fileType'] = xarModAPIFunc('mime', 'user', 'analyze_file',
                                                       array('fileName' => $fileInfo['fileLocation']));

                $fileInfo['fileSize'] = filesize($tmpName);
            }
        }
    }

    if (is_resource($tmpId)) {
        @fclose($tmpId);
    }

    if (is_resource($httpId)) {
        @fclose($httpId);
    }

    if (xarCurrentErrorType() !== XAR_NO_EXCEPTION) {

        unlink($tmpName);

        while (xarCurrentErrorType() !== XAR_NO_EXCEPTION) {

            $errorObj = xarCurrentError();

            if (is_object($errorObj)) {
                $fileError = array('errorMesg' => $errorObj->getShort(),
                                   'errorId'   => $errorObj->getID());
            } else {
                $fileError = array('errorMesg' => 'Unknown Error!',
                                   'errorId'   => _UPLOADS_ERROR_UNKNOWN);
            }

            if (!isset($fileInfo['errors'])) {
                $fileInfo['errors'] = array();
            }

            $fileInfo['errors'][] = $fileError;

            // Clear the exception because we've handled it already
            xarErrorHandled();

        }
    } else {
        $fileInfo['fileSrc'] = $fileInfo['fileLocation'];

        // remoe any trailing slash from the Save Path
        $savePath = preg_replace('/\/$/', '', $savePath);

        if ($obfuscate_fileName) {
            $obf_fileName = xarModAPIFunc('uploads','user','file_obfuscate_name',
                                        array('fileName' => $fileInfo['fileName']));
            $fileInfo['fileDest'] = $savePath . '/' . $obf_fileName;
        } else {
            // if we're not obfuscating it,
            // just use the name of the uploaded file
            $fileInfo['fileDest'] = $savePath . '/' . xarVarPrepForOS($fileInfo['fileName']);
        }
        $fileInfo['fileLocation'] = $fileInfo['fileDest'];

    }
    return array($fileInfo['fileLocation'] => $fileInfo);
}

?>
