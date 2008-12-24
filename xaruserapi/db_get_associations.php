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
 *  Retrieve a list of file assocations for a particular file/module/itemtype/item combination
 *
 * @author Carl P. Corliss
 * @access public
 * @param   integer modid     The id of module this file is associated with
 * @param   integer itemtype  The item type within the defined module
 * @param   integer itemid    The id of the item types item
 * @param   integer fileId    The id of the file we are going to associate with an item
 *
 * @return array   A list of associations, including the fileId -> (fileId + modid + itemtype + itemid)
 */

function uploads_userapi_db_get_associations( $args )
{

    extract($args);

    $whereList = array();
    $bindvars = array();

    if (isset($fileId)) {
        $whereList[] = ' (xar_fileEntry_id = ?) ';
        $bindvars[] = (int) $fileId;
    }

    if (isset($modid)) {
        $whereList[] = ' (xar_modid = ?) ';
        $bindvars[] = (int) $modid;

        if (isset($itemtype)) {
            $whereList[] = ' (xar_itemtype = ?) ';
            $bindvars[] = (int) $itemtype;

            if (isset($itemid)) {
                $whereList[] = ' (xar_objectid = ?) ';
                $bindvars[] = (int) $itemid;
            }
        }
    }

    if (count($whereList)) {
        $where = 'WHERE ' . implode(' AND ', $whereList);
    } else {
        $where = '';
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

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

    $fileList = array();
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);

        $fileAssoc['fileId']   = $row['xar_fileEntry_id'];
        $fileAssoc['modid']    = $row['xar_modid'];
        $fileAssoc['itemtype'] = $row['xar_itemtype'];
        $fileAssoc['itemid']   = $row['xar_objectid'];

        // Note: only one association is returned per file here !
        $fileList[$fileAssoc['fileId']] = $fileAssoc;
        $result->MoveNext();
    }
    return $fileList;
}

?>
