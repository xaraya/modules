<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 *  Check if files defined in associations still exist
 *
 *  @author  mikespub
 *  @access  public
 *
 *  @return mixed list of associations with missing files on success, void with exception on error
 */

function uploads_adminapi_check_associations( $args )
{
    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // table definitions
    $file_assoc_table = $xartable['file_associations'];
    $file_entry_table = $xartable['file_entry'];

// CHECKME: verify this for different databases
    // find file associations without corresponding file entry
    $sql = "SELECT
                   $file_assoc_table.xar_fileEntry_id,
                   $file_assoc_table.xar_modid,
                   $file_assoc_table.xar_itemtype,
                   $file_assoc_table.xar_objectid
              FROM $file_assoc_table
         LEFT JOIN $file_entry_table
                ON $file_assoc_table.xar_fileEntry_id = $file_entry_table.xar_fileEntry_id
             WHERE $file_entry_table.xar_filename IS NULL";

    $result = $dbconn->Execute($sql);

    if (!$result) {
        return array();
    }

    $list = array();
    while (!$result->EOF) {
        list($fileId,$modid,$itemtype,$itemid) = $result->fields;
        // simple item - file array
        if (!isset($list[$fileId])) {
            $list[$fileId] = 0;
        }
        $list[$fileId]++;
        $result->MoveNext();
    }
    return $list;
}

?>
