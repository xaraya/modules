<?php

/**
 *  Create an assocation between a (stored) file and a module/itemtype/item
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer fileid    The id of the file we are going to associate with an item
 *  @param   integer modid     The id of module this file is associated with
 *  @param   integer itemtype  The item type within the defined module
 *  @param   integer objectId    The id of the item types item
 *
 *  @returns integer The id of the file that was associated, FALSE with exception on error
 */

function uploads_userapi_db_add_association( $args )
{
    extract($args);

    if (!isset($fileid)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileid','db_add_assocation','uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    } else {
        $bindvars[] = (int) $fileid;
    }

    if (!isset($modid)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'modid','db_add_assocation','uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    } else {
        $bindvars[] = (int) $modid;
    }

    if (!isset($itemtype)) {
        $itemtype = 0;
    }
    $bindvars[] = (int) $itemtype;

    if (!isset($itemid)) {
        $itemid = 0;
    }
    $bindvars[] = (int) $itemid;

    //add to uploads table
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();


    // table and column definitions
    $file_assoc_table = $xartable['file_associations'];

    // insert value into table
    $sql = "INSERT INTO $file_assoc_table
                      (
                        xar_fileEntry_id,
                        xar_modid,
                        xar_itemtype,
                        xar_objectid
                      )
               VALUES
                      (
                        ?, ?, ?, ?
                      )";

    $result = &$dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return FALSE;
    } else {
        return $fileid;
    }
}

?>