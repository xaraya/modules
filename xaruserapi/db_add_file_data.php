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
 *  Adds a file's  contents to the database. This only takes 4K (4096 bytes) blocks.
 *  So a file's data could potentially be contained amongst many records. This is done to
 *  ensure that we are able to actually save the whole file in the db.
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer fileId     The ID of the file this data belongs to
 *  @param   string  fileData   A line of data from the file to be stored (no greater than 65535 bytes)
 *
 *  @return integer The id of the fileData that was added, or FALSE on error
 */

function uploads_userapi_db_add_file_data($args)
{
    extract($args);

    if (!isset($fileId)) {
        $msg = xarML(
            'Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
            'fileId',
            'db_add_file_data',
            'uploads'
        );
        throw new Exception($msg);
    }

    if (!isset($fileData)) {
        $msg = xarML(
            'Missing parameter [#(1)] for function [#(2)] in module (#3)]',
            'location',
            'db_add_file_data',
            'uploads'
        );
        throw new Exception($msg);
    }

    if (sizeof($fileData) >= (1024 * 64)) {
        $msg = xarML('#(1) exceeds maximum storage limit of 64KB per data chunk.', 'fileData');
        throw new Exception($msg);
    }

    $fileData = base64_encode($fileData);

    //add to uploads table
    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();


    // table and column definitions
    $fileData_table = $xartable['file_data'];
    $fileDataID    = $dbconn->genID($fileData_table);

    // insert value into table
    $sql = "INSERT INTO $fileData_table
                      (
                        xar_fileEntry_id,
                        xar_fileData_id,
                        xar_fileData
                      )
               VALUES
                      (
                        $fileId,
                        $fileDataID,
                        '$fileData'
                      )";
    $result = &$dbconn->Execute($sql);

    if (!$result) {
        return false;
    } else {
        $id = $dbconn->PO_Insert_ID($xartable['file_data'], 'id');
        return $id;
    }
}
