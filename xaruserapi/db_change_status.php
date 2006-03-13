 <?php

/**
 *  Change the status on a file, or group of files based on the file id(s) or filetype
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *
 *  @return integer The number of affected rows on success, or FALSE on error
 */

function filemanager_userapi_db_change_status( $args )
{
    extract($args);

    if (!isset($fileId) && !isset($fileType)) {
        $msg = xarML('Missing identifying parameters (#(1)/#(2)) for function [#(3)] in module [#(4)]',
                     'fileId', 'fileType', 'db_change_status','filemanager');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($newStatus)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'newStatus','db_change_status','filemanager');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($inverse)) {
        $inverse = FALSE;
    }

    $bindvars = array();

    if (isset($fileId)) {
        // Looks like we have an array of file ids, so change them all
       if (is_array($fileId) && count($fileId)) {

            $list = array();

            foreach ($fileId as $id) {
                $list[]     = '?';
                $bindvars[] = (int) $id;
            }

            $where = ' WHERE xar_fileEntry_id IN (' . implode(',', $list) . ')';

        } elseif (is_numeric($fileId)) {
            $where = " WHERE xar_fileEntry_id = ?";
            $bindvars[] = (int) $fileId;
        }

    // Otherwise, we're changing based on MIME type
    } else {
        if (!$inverse) {
            $where = " WHERE xar_mime_type LIKE ?";
            $bindvars[] = (string) $fileType;
        } else {
            $where = " WHERE xar_mime_type NOT LIKE ?";
            $bindvars[] = (string) $fileType;
        }
    }

    if (isset($curStatus) && is_numeric($curStatus)) {
        $where .= " AND xar_status = ?";
        $bindvars[] = (int) $curStatus;
    }

    //add to filemanager table
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $fileEntry_table = $xartable['file_entry'];

    $sql             = "UPDATE $fileEntry_table
                           SET xar_status = $newStatus
                        $where";

    $result          = &$dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->Affected_Rows();
    }

}

?>
