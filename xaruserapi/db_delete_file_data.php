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
 *  Remove a file's data contents from the database. This just removes any data (contents)
 *  that we might have in store for this file. The actual metadata (FILE ENTRY) for the file
 *  itself is removed via db_delete_file() .
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer fileId    The id of the file who's contents we are removing
 *
 *  @return integer The number of affected rows on success, or FALSE on error
 */

function uploads_userapi_db_delete_file_data( $args )
{
    extract($args);

    if (!isset($fileId)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileId','db_delete_file_data','uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    //add to uploads table
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // table and column definitions
    $fileData_table   = $xartable['file_data'];

    // insert value into table
    $sql = "DELETE
              FROM $fileData_table
             WHERE xar_fileEntry_id = $fileId";


    $result = &$dbconn->Execute($sql);

    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->Affected_Rows();
    }

}

?>