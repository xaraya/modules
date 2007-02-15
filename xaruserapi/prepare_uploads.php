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
 *  Prepares a list of files that have been uploaded, creating a structure for
 *  each file with the following parts:
 *      * fileType  - mimetype
 *      * fileSrc   - the source location of the file
 *      * fileSize  - the filesize of the file
 *      * fileName  - the file's basename
 *      * fileDest  - the (potential) destination for the file (filled in even if stored in the db and not filesystem)
 *  Any file that has errors will have it noted in the same structure with error number and message in:
 *      * errors[]['errorMesg']
 *      * errors[]['errorId']
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   boolean obfuscate            whether or not to obfuscate the filename
 *  @param   string  savePath             Complete path to directory in which we want to save this file
 *  @return boolean                      TRUE on success, FALSE on failure
 */


function uploads_userapi_prepare_uploads( $args )
{

    extract ( $args );

    // if there are no files, return an empty array.
    if (!isset($fileInfo)) {
        if (empty($_FILES) || !is_array($_FILES) || count($_FILES) <= 0) {
            return array();
        } else {
            $fileInfo = $_FILES[0];
        }
    }

    $fileList = array();

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

    // If we don't have the right data structure, then we can't do much
    // here, so return immediately with an exception set
    if ((!isset($fileInfo)          || !is_array($fileInfo))      ||
         !isset($fileInfo['name'])  || !isset($fileInfo['type'])  ||
         !isset($fileInfo['size'])  || !isset($fileInfo['tmp_name']))  {

            $fileInfo['fileType']   = 'unknown';
            $fileInfo['fileSrc']    = 'missing';
            $fileInfo['fileSize']   = 0;
            $fileInfo['fileName']   = xarML('Missing File!');
            $fileInfo['errors'][0]['errorMesg'] = xarML('Invalid data format for upload ID: [#(1)]', 'upload');
            $fileInfo['errors'][0]['errorId']  = _UPLOADS_ERROR_BAD_FORMAT;
            return array("$fileInfo[fileName]" => $fileInfo);
    }

    $fileInfo['fileType']   = $fileInfo['type'];
    $fileInfo['fileSrc']    = $fileInfo['tmp_name'];
    $fileInfo['fileSize']   = $fileInfo['size'];
    $fileInfo['fileName']   = $fileInfo['name'];

    // PHP 4.1.2 doesn't support this field yet
    if (!isset($fileInfo['error'])) {
        $fileInfo['error'] = 0;
    }

    // Check to see if we're importing and, if not, check the file and ensure that it
    // meets any requirements we might have for it. If it doesn't pass the tests,
    // then return FALSE
    if (!xarModAPIFunc('uploads','user','validate_upload', array('fileInfo' => $fileInfo))) {
        $errorObj = xarCurrentError();

        if (is_object($errorObj)) {
            $fileError['errorMesg'] = $errorObj->getShort();
            $fileError['errorId']   = $errorObj->getID();
        } else {
            $fileError['errorMesg'] = 'Unknown Error!';
            $fileError['errorId']   = _UPLOADS_ERROR_UNKOWN;
        }
        $fileInfo['errors']      = array($fileError);

        // clear the exception
        xarErrorHandled();

        // continue on to the next uploaded file in the list
        return array("$fileInfo[fileName]" => $fileInfo);
    }

    /**
    *  Start the process of adding an uploaded file
    */

    unset($fileInfo['tmp_name']);
    unset($fileInfo['size']);
    unset($fileInfo['name']);
    unset($fileInfo['type']);
    unset($fileInfo['error']);

// FIXME: do this after the file has been moved
    $fileInfo['fileType']   = xarModAPIFunc('mime','user','analyze_file',
                                            array('fileName' => $fileInfo['fileSrc'], 'altFileName'=>$fileInfo['fileName']));


    // If duplicate and we have a file location, overwrite existing file here (cfr. process_files)
    if (!empty($fileInfo['isDuplicate']) && $fileInfo['isDuplicate'] == 2 &&
        !empty($fileInfo['fileLocation'])) {
        // Note: this could be some dummy location for database storage, or the file might be gone
        $fileInfo['fileDest'] = $fileInfo['fileLocation'];
    }

    // Check if we have a valid destination (i.e. not removed and not stored in the database)
    if (!empty($fileInfo['fileDest']) && file_exists($fileInfo['fileDest'])) {
        // OK then

    // Check to see if we need to obfuscate the filename
    } elseif ($obfuscate_fileName) {
        $obf_fileName = xarModAPIFunc('uploads','user','file_obfuscate_name',
                                    array('fileName' => $fileInfo['fileName']));

        if (empty($obf_fileName) || FALSE === $obf_fileName) {
            // If the filename was unable to be obfuscated,
            // set an error, but don't die - let the caller
            // do what they want with this.
            $fileError['errorMesg'] = 'Unable to obfuscate filename!';
            $fileError['errorId']   = _UPLOADS_ERROR_NO_OBFUSCATE;
            $fileInfo['errors']      = array($fileError);
        } else {
            $fileInfo['fileDest'] = $savePath . '/' . $obf_fileName;
        }

    } else {
        // if we're not obfuscating it,
        // just use the name of the uploaded file
        $filename = xarVarPrepForOs($fileInfo['fileName']);
        $fileInfo['fileDest'] = $savePath . '/' . $filename;
        // But first make sure we don't already have a file by that name
        $i = 0;
        while(file_exists($fileInfo['fileDest'])){
            $i++;
            $fileInfo['fileDest'] = $savePath. '/' .$filename. '_' . $i;
        }
    }

    if (isset($fileInfo['fileDest'])) {
        $fileInfo['fileLocation'] = $fileInfo['fileDest'];
        $fileInfo['isUpload'] = TRUE;
    }

    return array("$fileInfo[fileName]" => $fileInfo);
}

?>
