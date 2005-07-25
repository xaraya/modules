<?php

/**
 *  Retrieve a list of file assocations for a particular file/module/itemtype/item combination
 *
 * @author Carl P. Corliss
 * @access public
 * @param   integer fileid    The id of the file we are going to associate with an item
 * @param   integer modid     The id of module this file is associated with
 * @param   integer itemtype  The item type within the defined module
 * @param   integer objectId    The id of the item types item
 *
 * @returns array   A list of associations, including the fileid -> modid -> itemtype -> objectId
 */

function uploads_userapi_db_get_associations( $args )
{

    extract($args);

    $whereList = array();
    $bindvars = array();
    $hash = '';

    if (isset($fileid)) {
        $whereList[] = " (xar_fileEntry_id = ?) ";
        $bindvars[]  = (int) $fileid;
        $hash = $fileid;
    }

    if (isset($modid)) {
        $whereList[] .= " (xar_modid = ?) ";
        $bindvars[]  = (int) $modid;
        $hash .= ".$modid";

        if (isset($itemtype)) {
            $whereList[] .= " (xar_itemtype = ?) ";
            $bindvars[]  = (int) $itemtype;
            $hash .= ".$itemtype";

            if (isset($itemid)) {
                $whereList[] .= " (xar_objectid = ?) ";
                $bindvars[]   = (int) $itemid;
                $hash .= ".$itemid";
            }
        }
    }

    $associations = xarVarGetCached('uploads', 'cache.associations');

    if (isset($associations[$hash])) {
        return $associations[$hash];
    } else {

        if (isset($modid)) {
            $whereList[] .= " (xar_modid = ?) ";
            $bindvars[]  = (int) $modid;

            if (isset($itemtype)) {
                $whereList[] .= " (xar_itemtype = ?) ";
                $bindvars[]  = (int) $itemtype;

                if (isset($itemid)) {
                    $whereList[] .= " (xar_objectid = ?) ";
                    $bindvars[]   = (int) $itemid;
                }
            }
        }

        if (count($whereList)) {
            $where = 'WHERE ' . implode(' AND ', $whereList);
        } else {
            $where = '';
        }

        // Get database setup
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();

            // table and column definitions
        $file_assoc_table = $xartable['file_associations'];

        $sql = "SELECT
                        xar_fileEntry_id,
                        xar_modid,
                        xar_itemtype,
                        xar_objectid
                FROM $file_assoc_table
                $where";

        $result = $dbconn->Execute($sql, $bindvars);

        if (!$result)  {
            return array();
        }

        // if no record found, return an empty array
        if ($result->EOF) {
            return array();
        }

        while (!$result->EOF) {
            $row = $result->GetRowAssoc(false);

            $fileAssoc['fileid']   = $row['xar_fileentry_id'];
            $fileAssoc['modid']    = $row['xar_modid'];
            $fileAssoc['itemtype'] = $row['xar_itemtype'];
            $fileAssoc['itemid']   = $row['xar_objectid'];

            $fileList[$fileAssoc['fileid']] = $fileAssoc;
            $fileCache[$hash] = $fileAssoc;
            $result->MoveNext();
        }
        xarVarSetCached('uploads', 'file.associations', $fileCache);
        return $fileList;
    }
}

?>