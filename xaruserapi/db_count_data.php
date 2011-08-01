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
 *  Retrieve the total count of data blocks stored for a particular file
 *
 * @author  Carl P. Corliss
 * @author  Micheal Cortez
 * @access  public
 * @param   integer  fileId     (Optional) grab file with the specified file id
 * @return integer             The total number of DATA Blocks stored for a particular file
 */

function uploads_userapi_db_count_data( $args )
{

    extract($args);

    $where = array();

    if (!isset($fileId)) {
        $msg = xarML('Missing parameter [#(1)] for API function [#(2)] in module [#(3)]',
                     'fileId','db_count_data','uploads');
        throw new Exception($msg);             
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

        // table and column definitions
    $fileEntry_table = $xartable['file_data'];

    $sql = "SELECT COUNT(xar_fileData_id) AS total
              FROM $fileEntry_table
             WHERE xar_fileEntry_id = $fileId";

    $result = $dbconn->Execute($sql);

    if (!$result)  {
        return FALSE;
    }

    // if no record found, return an empty array
    if ($result->EOF) {
        return (integer) 0;
    }

    $row = $result->GetRowAssoc(false);

    return $row['total'];
}

?>