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
 *  Remove an assocation between a particular file and module/itemtype/item.
 *  <br />
 *  If just the fileId is passed in, all assocations for that file will be deleted.
 *  If the fileId and modid are supplied, any assocations for the given file and modid
 *  will be removed. The same holds true for itemtype and itemid.
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer fileId    The id of the file we are going to remove association with
 *  @param   integer modid     The id of module this file is associated with
 *  @param   integer itemtype  The item type within the defined module
 *  @param   integer itemid    The id of the item types item
 *
 *  @return bool TRUE on success, FALSE with exception on error
 */

function uploads_userapi_db_delete_association( $args )
{
    extract($args);

    $whereList = array();
    $bindvars = array();

    if (!isset($fileId)) {

    } elseif (is_array($fileId)) {
        $whereList[] = ' (xar_fileEntry_id IN (' . implode(',', $fileId) . ') ) ';

    } else {
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

    //add to uploads table
    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

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
        return TRUE;
    }
}

?>
