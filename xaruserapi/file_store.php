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
 *  Takes a files metadata for input and creates the file's entry in the database
 *  as well as storing it's contents in either the filesystem or database
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   array      fileInfo                The metadata for the file being stored
 *  @param   string     fileInfo.fileType       The MIME type for the file
 *  @param   string     fileInfo.fileName       The file's basename
 *  @param   string     fileInfo.fileSrc        The source location for the file
 *  @param   string     fileInfo.fileDest       The (potential) destination for the file (filled in even if stored in the db and not filesystem)
 *  @param   integer    fileInfo.fileSize       The filesize of the file
 *  @return array      returns the array passed into it modified with the extra attributes received through the storage
 *                      process. If the file wasn't added successfully, fileInfo.errors is set appropriately
 */

xarModAPILoad('uploads', 'user');

function uploads_userapi_file_store( $args )
{

    extract ( $args );

    if (!isset($fileInfo)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileInfo','file_store','uploads');
        throw new Exception($msg);             
    }

    $typeInfo = xarModAPIFunc('mime', 'user', 'get_rev_mimetype', array('mimeType' => $fileInfo['fileType']));
    $instance = array();
    $instance[0] = $typeInfo['typeId'];
    $instance[1] = $typeInfo['subtypeId'];
    $instance[2] = xarSession::getVar('uid');
    $instance[3] = 'All';

    $instance = implode(':', $instance);

    if ((isset($fileInfo['fileStatus']) && $fileInfo['fileStatus'] == _UPLOADS_STATUS_APPROVED) ||
         xarSecurityCheck('AddUploads', 1, 'File', $instance)) {

        if (!isset($storeType)) {
                $storeType = _UPLOADS_STORE_FSDB;
        }

        if (!empty($fileInfo['isDuplicate']) && $fileInfo['isDuplicate'] == 2) {
            // we *want* to overwrite a duplicate here

        } else {
            // first, make sure the file isn't already stored in the db/filesystem
            // if it is, then don't add it.
            $fInfo = xarModAPIFunc('uploads', 'user', 'db_get_file',
                                   array('fileLocation' => $fileInfo['fileLocation'],
                                         'fileSize' => $fileInfo['fileSize']));

            // If we already have the file, then return the info we have on it
            if (is_array($fInfo) && count($fInfo)) {
                // Remember, db_get_file returns the files it finds (even if just one)
                // as an array of files, so - considering we are only expecting one file
                // return the first one in the list - indice 0
                return end($fInfo);
            }
        }

        // If this is just a file dump, return the dump
        if ($storeType & _UPLOADS_STORE_TEXT) {
            $fileInfo['fileData'] = xarModAPIFunc('uploads','user','file_dump', $fileInfo);
        }
        // If the store db_entry bit is set, then go ahead
        // and set up the database meta information for the file
        if ($storeType & _UPLOADS_STORE_DB_ENTRY) {

            $fileInfo['store_type'] = $storeType;

            if (!empty($fileInfo['isDuplicate']) && $fileInfo['isDuplicate'] == 2 &&
                !empty($fileInfo['fileId'])) {
                // we *want* to overwrite a duplicate here
                xarModAPIFunc('uploads','user','db_modify_file', $fileInfo);

                $fileId = $fileInfo['fileId'];

            } else {
                $fileId = xarModAPIFunc('uploads','user','db_add_file', $fileInfo);

                if ($fileId) {
                    $fileInfo['fileId'] = $fileId;
                }
            }
        }

        if ($storeType & _UPLOADS_STORE_FILESYSTEM) {

            if ($fileInfo['fileSrc'] != $fileInfo['fileDest']) {
                $result = xarModAPIFunc('uploads','user','file_move', $fileInfo);
            } else {
                $result = TRUE;
            }

            if ($result) {
                $fileInfo['fileLocation'] =& $fileInfo['fileDest'];
            } else {
                // if it wasn't moved successfully, then we should remove
                // the database entry (if there is one) so that we don't have
                // a corrupted file entry
                if (isset($fileId) && !empty($fileId)) {
                    xarModAPIFunc('uploads', 'user', 'db_delete_file', array('fileId' => $fileId));

                    // Don't forget to remove the fileId from fileInfo
                    // because it's non existant now ;-)
                    if (isset($fileInfo['fileId'])) {
                        unset($fileInfo['fileId']);
                    }
                }

                $fileInfo['fileLocation'] =& $fileInfo['fileSrc'];
            }
        }

        if ($storeType & _UPLOADS_STORE_DB_DATA) {
            if (!xarModAPIFunc('uploads', 'user', 'file_dump', $fileInfo)) {
                // If we couldn't add the files contents to the database,
                // then remove the file metadata as well
                if (isset($fileId) && !empty($fileId))  {
                    xarModAPIFunc('uploads', 'user' ,'db_delete_file', array('fileId' => $fileId));
                }
            } else {
                // if it was successfully added, then change the stored fileLocation
                // to DATABASE instead of uploads/blahblahblah
                xarModAPIFunc('uploads', 'user', 'db_modify_file', array('fileId' => $fileId, 'fileLocation' => xarML('DATABASE')));
            }
        }
    }
    // If there were any errors generated while attempting to add this file,
    // we run through and grab them, adding them to this file
/*    while (xarCurrentErrorType() !== XAR_NO_EXCEPTION) {

        $errorObj = xarCurrentError();

        if (is_object($errorObj)) {
            $fileError = array('errorMesg'   => $errorObj->getShort(),
                               'errorId'    => $errorObj->getID());
        } else {
            $fileError = array('errorMesg'   => 'Unknown Error!',
                               'errorId'    => _UPLOADS_ERROR_UNKNOWN);
        }

        if (!isset($fileInfo['errors'])) {
            $fileInfo['errors'] = array();
        }

        $fileInfo['errors'][] = $fileError;

        // Clear the exception because we've handled it already
        xarErrorHandled();

    }
*/
    return $fileInfo;
}
?>
