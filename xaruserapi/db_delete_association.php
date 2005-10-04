<?php

/**
 *  Remove an assocation between a particular file and module/itemtype/item.
 *  <br />
 *  If just the fileId is passed in, all assocations for that file will be deleted.
 *  If the fileId and modId are supplied, any assocations for the given file and modId
 *  will be removed. The same holds true for itemType and objectId.
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer fileId    The id of the file we are going to remove association with
 *  @param   integer modid     The id of module this file is associated with
 *  @param   integer itemtype  The item type within the defined module
 *  @param   integer itemid    The id of the item types item
 *
 *  @returns integer The number of affected rows on success, or FALSE on error
 */

function filemanager_userapi_db_delete_association( $args )
{

    extract($args);

    if (!isset($fileId) || (!is_numeric($fileId) && !is_array($fileId))) {
        $fileId = 0;
        $where = '';
    } else {
        // Looks like we have an array of file ids, so change them all
       if (is_array($fileId) && count($fileId)) {

            $list = array();

            foreach ($fileId as $id) {
                $list[]     = '?';
                $bindvars[] = (int) $id;
            }

            $where = 'WHERE  xar_fileEntry_id IN (' . implode(',', $list) . ') ';

        } else {
            $where = 'WHERE  xar_fileEntry_id = ? ';
            $bindvars[] = (int) $fileId;
        }

    }

    if (isset($modid)) {
        if (empty($where)) {
            $where .= 'WHERE (xar_modid = ?) ';
        } else {
            $where .= ' AND (xar_modid = ?) ';
        }
        $bindvars[] = (int) $modid;

        if (isset($itemtype)) {
            $where .= ' AND (xar_itemtype = ?) ';
            $bindvars[] = (int) $itemtype;

            if (isset($itemid)) {
                $where .= ' AND (xar_objectid = ?) ';
                $bindvars[] = (int) $itemid;
            }
        }
    }

    if (0 == $fileId && (!isset($itemid) || !isset($itemtype) || !isset($modid))) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileId','db_delete_assocation','filemanager');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    } 
        
    //add to filemanager table
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // table and column definitions
    $file_assoc_table   = $xartable['file_associations'];

    // insert value into table
    $sql = "DELETE
              FROM $file_assoc_table
             $where";

    $result = &$dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->Affected_Rows();
    }

}

?>
