<?php

/**
 *  Adds a file's contents to the database. This defaults to a maximum of 8K (8192 bytes) blocks.
 *  So a file's data could potentially be contained amongst many records. This is done to
 *  ensure that we are able to actually save the whole file in the db.
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer fileId     The ID of the file this data belongs to
 *  @param   string  fileData   A line of data from the file to be stored (no greater than 65535 bytes)
 *
 *  @returns integer The id of the fileData that was added, or FALSE on error
 */

function uploads_userapi_db_add_file_data( $args )
{

    extract($args);

    if (!isset($fileId)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileId','db_add_file_data','uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($fileData)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module (#3)]',
                     'location','db_add_file_data','uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    $blockSize = xarModGetVar('uploads', 'db.blocksize');
    if (!isset($blockSize) || empty($blockSize)) {
        $blockSize = (8 * 1024); // 8k hard default
    }

    if (strlen($fileData) > $blockSize) {
        // If the size exceeds the maxblockSize
        // attemnpt to break it up into smaller chunks.

        $start = 0;
        $totalSize = strlen($fileData);
        $remaining = $totalSize;

        while ($start < $totalSize) {

            $length = ($remaining < $blockSize) ? $remaining : $blockSize;
            $key = $start + 1;

            $fileChunk[] = substr($fileData, $start, $length);

            $start += $blockSize;
            $remaining = $totalSize - $start;
        }
    } else {
        $fileChunk[] = $fileData;
    }


    //add to uploads table
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();


    // table and column definitions
    $fileData_table = $xartable['file_data'];

    foreach ($fileChunk as $chunk) {
        $fileDataID    = $dbconn->GenID($fileData_table);

        // insert value into table
        $sql = "INSERT INTO $fileData_table
                        (
                            xar_fileEntry_id,
                            xar_fileData_id,
                            xar_fileData
                        )
                VALUES
                        ( ?, ?, ? )";

        $bindvars = array(
                            (int) $fileId,
                            (int) $fileDataID,
                            (string) $chunk
                        );

        $result = &$dbconn->Execute($sql, $bindvars);

        if (!$result) {
            return FALSE;
        } else {
            $ids[] = $dbconn->PO_Insert_ID($xartable['file_data'], 'xar_cid');
        }
    }
    return $ids;
}

?>