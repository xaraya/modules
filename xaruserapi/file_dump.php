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
 *  Dump a files contents into the database.
 *
 *  @author  Carl P. corliss
 *  @access  public
 *  @param   string  fileSrc   The location of the file whose contents we want to dump into the database
 *  @param   integer fileId    The file entry id of the file's meta data in the database
 *  returns  integer           The total bytes stored or boolean FALSE on error
 */

function uploads_userapi_file_dump( $args )
{

    extract($args);

    if (!isset($unlink)) {
        $unlink = TRUE;
    }
    if (!isset($fileSrc)) {
        $msg = xarML('Missing parameter [#(1)] for API function [#(2)] in module [#(3)].',
                      'fileSrc', 'file_dump', 'uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($fileId)) {
        $msg = xarML('Missing parameter [#(1)] for API function [#(2)] in module [#(3)].',
                      'fileId', 'file_dump', 'uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!file_exists($fileSrc)) {
        $msg = xarML('Unable to locate file [#(1)]. Are you sure it\'s there??', $fileSrc);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NOT_EXIST', new SystemException($msg));
        return FALSE;
    }

    if (!is_readable($fileSrc) || !is_writable($fileSrc)) {
        $msg = xarML('Cannot read and/or write to file [#(1)]. File will be read from and deleted afterwards. Please ensure that this application has sufficient access to do so.', $fileSrc);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_READWRITE', new SystemException($msg));
        return FALSE;
    }

    $fileInfo = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $fileId));
    $fileInfo = end($fileInfo);

    if (!count($fileInfo) || empty($fileInfo)) {
        $msg = xarML('FileId [#(1)] does not exist. File [#(2)] does not have a corresponding metadata entry in the databsae.',
                     $fileId, $fileSrc);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_DB_ENTRY', new SystemException($msg));
        return FALSE;
    } else {
        $dataBlocks = xarModAPIFunc('uploads', 'user', 'db_count_data', array('fileId' => $fileId));

        if ($dataBlocks > 0) {
            // we don't support non-truncated overwrites nor appends
            // so truncate the file and then save it
            if (!xarModAPIFunc('uploads', 'user', 'db_delete_file_data', array('fileId' => $fileId))) {
                $msg = xarML('Unable to truncate file [#(1)] in database.', $fileInfo['fileName']);
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_DB_ENTRY', new SystemException($msg));
                return FALSE;
            }
        }

        // Now we copy the contents of the file into the database
        if (($srcId = fopen($fileSrc, 'rb')) !== FALSE) {

            do {
                // Read 16K in at a time
                $data = fread($srcId, (64 * 1024));
                if (0 == strlen($data)) {
                    fclose($srcId);
                    break;
                }
                if (!xarModAPIFunc('uploads', 'user', 'db_add_file_data', array('fileId' => $fileId, 'fileData' => $data))) {
                    // there was an error, so close the input file and delete any blocks
                    // we may have written, unlink the file (if specified), and return an exception
                    fclose($srcId);
                    if ($unlink) {
                        @unlink($fileSrc); // fail silently
                    }
                    xarModAPIFunc('uploads', 'user', 'db_delete_file_data', array('fileId' => $fileId));
                    $msg = xarML('Unable to save file contents to database.');
                    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_DB_WRITE', new SystemException($msg));
                    return FALSE;
                }
            } while (TRUE);
       } else {
            $msg = xarML('Cannot read and/or write to file [#(1)]. File will be read from and deleted afterwards. Please ensure that this application has sufficient access to do so.', $fileSrc);
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_READWRITE', new SystemException($msg));
            return FALSE;
       }
    }

    if ($unlink) {
        @unlink($fileSrc);
    }
    return TRUE;
}

?>