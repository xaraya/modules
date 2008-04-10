<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 *  Validates file based on criteria specified by hooked modules (well, that's the intended future
 *  functionality anyhow - which won't be available until the hooks system has been revamped......
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   array   fileInfo               An array containing (fileName, fileType, fileSrc, fileSize, error):
 *                   fileInfo['fileName']   The (original) name of the file (minus any path information)
 *                   fileInfo['fileType']   The mime content-type of the file
 *                   fileInfo['fileSrc']    The temporary file name (complete path) of the file
 *                   fileInfo['error']      Number representing any errors that were encountered during the upload (>= PHP 4.2.0)
 *                   fileInfo['fileSize']   The size of the file (in bytes)
 *  @return boolean                      TRUE if checks pass, FALSE otherwise
 */

function uploads_userapi_validate_upload( $args )
{

    extract ($args);

    if (!isset($fileInfo)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'fileInfo','validate_upload','uploads');
        throw new Exception($msg);             
    }

    switch ($fileInfo['error'])  {

        case 1: // The uploaded file exceeds the upload_max_filesize directive in php.ini
            $msg = xarML('File size exceeds the maximum allowable based on the server\'s settings.');
            throw new Exception($msg);             

        case 2: // The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form
            $msg = xarML('File size exceeds the maximum allowable defined by the website administrator.');
            throw new Exception($msg);             

        case 3: // The uploaded file was only partially uploaded
            $msg = xarML('The file was only partially uploaded.');
            throw new Exception($msg);             

        case 4: // No file was uploaded
            $msg = xarML('No file was uploaded..');
            throw new Exception($msg);             
        default:
        case 0:  // no error
            break;
    }

    $maxsize = xarModVars::get('uploads', 'file.maxsize');
    $maxsize = $maxsize > 0 ? $maxsize : 0;

    if ($fileInfo['size'] > $maxsize) {
        $msg = xarML('File size exceeds the maximum allowable defined by the website administrator.');
            throw new Exception($msg);             
    }

    if (!is_uploaded_file($fileInfo['fileSrc'])) {
        $msg = xarML('Possible attempted malicious file upload.');
            throw new Exception($msg);             
    }

    // future functionality - ...
    // if (!xarModCallHooks('item', 'validation', array('type' => 'file', 'fileInfo' => $fileInfo))) {
    //     return FALSE;
    // }
    return TRUE;
}

?>
