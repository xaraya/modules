<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

/**
 * Takes a list of files and deletes them

 *
 * @author  Carl P. Corliss
 * @access  public
 * @param   array   fileList    List of files to delete containing complete fileName => fileInfo arrays
 * @return boolean             true if successful, false otherwise
 */

function uploads_userapi_purge_files($args)
{
    extract($args);

    if (!isset($fileList)) {
        $msg = xarML(
            'Missing required parameter [#(1)] for API function [#(2)] in module [#(3)]',
            'fileList',
            'purge_files',
            'uploads'
        );
        throw new Exception($msg);
    }

    foreach ($fileList as $fileName => $fileInfo) {
        if ($fileInfo['storeType'] & _UPLOADS_STORE_FILESYSTEM) {
            xarModAPIFunc('uploads', 'user', 'file_delete', array('fileName' => $fileInfo['fileLocation']));
        }

        if ($fileInfo['storeType'] & _UPLOADS_STORE_DB_DATA) {
            xarModAPIFunc('uploads', 'user', 'db_delete_file_data', array('fileId' => $fileInfo['fileId']));
        }

        // go ahead and delete the file from the database.
        xarModAPIFunc('uploads', 'user', 'db_delete_file', array('fileId' => $fileInfo['fileId']));
    }

    return true;
}
