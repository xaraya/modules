<?php

/**
 * Returns the size (in bytes) of the file or files who's ID's where passed in
 *
 * @author Carl P. Corliss (ccorliss@schwabfoundation.org)
 * @param integer/array $fileId  Id (or list of ids) of the file(s) who's size we want
 * @return long integer
 * @returns The total of all file sizes requested, NULL on error
 */

function uploads_userapi_db_get_filesize( $args )
{
    extract($args);

    if (!isset($fileId) || (!is_numeric($fileId) && !is_array($fileId))) {
        $msg = xarML('Missing [#(1)] parameter for function [#(2)] in module [#(3)]', 'fileId', 'db_get_filename', 'uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (is_array($fileId) && count($fileId)) {

        $list = array();

        foreach ($fileId as $id) {
            $list[]     = '?';
            $bindvars[] = (int) $id;
        }

        $where = 'xar_fileEntry_id IN (' . implode(',', $list) . ')';

    } else {
        $where = "xar_fileEntry_id = ?";
        $bindvars[] = (int) $fileId;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

        // table and column definitions
    $fileEntry_table = $xartable['file_entry'];

    $sql = "SELECT SUM(xar_filesize) AS totalsize
              FROM $fileEntry_table
             WHERE $where";

    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result)  {
        return;
    }

    if (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        return $row['totalsize'];
    } else {
        return 0;
    }
}

?>
