<?php

/**
 *  Retrieve the filename for a particular file based on the file id
 *
 * @author Carl P. Corliss
 * @access public
 * @param  integer  fileId     (Optional) grab file with the specified file id
 *
 * @returns array   All of the metadata stored for the particular file
 */

function filemanager_userapi_db_get_filename( $args )
{

    extract($args);

    if (!isset($fileId) || (!is_numeric($fileId) && !is_array($fileId))) {
        $msg = xarML('Missing [#(1)] parameter for function [#(2)] in module [#(3)]', 'fileId', 'db_get_filename', 'filemanager');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    $fileCache = xarVarGetCached('filemanager', 'file.list');
    if (!isset($fileCache) || empty($fileCache)) {
        $fileCache = array();
    } elseif (is_numeric($fileId) && isset($fileCache[$fileId])) {
        return $fileCache[$fileId]['name'];
    }
    if (is_array($fileId) && count($fileId)) {

        $list = array();

        foreach ($fileId as $id) {
            if (in_array($id, array_keys($fileCache))) {
                $fileList[$id] = $fileCache[$id];
            } else {
                $list[]     = '?';
                $bindvars[] = (int) $id;
            }
        }
        if (empty($list)) {
            return $fileList;
        } else {
            $where = 'xar_fileEntry_id IN (' . implode(',', $list) . ')';
        }

    } else {
        if (in_array($fileId, array_keys($fileCache))) {
            return $fileCache[$id]['name'];
        }
        $where = "xar_fileEntry_id = ?";
        $bindvars[] = (int) $fileId;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

        // table and column definitions
    $fileEntry_table = $xartable['file_entry'];

    $sql = "SELECT xar_fileEntry_id AS id, xar_filename AS name
              FROM $fileEntry_table
             WHERE $where";

    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result)  {
        return;
    }

    // if no record found, return an empty array
    if ($result->EOF) {
        return '';
    }

    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        $fileList[$row['id']] = $row['name'];
        $result->MoveNext();
    }

    if (is_numeric($fileId) && count($fileList) == 1) {
        return $fileList[$fileId];
    } else {
        return $fileList;
    }
}

?>