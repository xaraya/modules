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
 *  Create an assocation between a (stored) file and a module/itemtype/item
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer fileId    The id of the file we are going to associate with an item
 *  @param   integer modid     The id of module this file is associated with
 *  @param   integer itemtype  The item type within the defined module
 *  @param   integer itemid    The id of the item types item
 *
 *  @return integer The id of the file that was associated, FALSE with exception on error
 */

function uploads_userapi_db_add_association( $args )
{
    extract($args);

    if (!isset($fileId)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileId','db_add_assocation','uploads');
        throw new Exception($msg);             
    }

    if (!isset($modid)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'modid','db_add_assocation','uploads');
        throw new Exception($msg);             
    }

    if (!isset($itemtype)) {
        $itemtype = 0;
    }

    if (!isset($itemid)) {
        $itemid = 0;
    }

    //add to uploads table
    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

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
                      ( ?, ?, ?, ? )";

    $bindvars = array((int)$fileId,(int)$modid,(int)$itemtype,(int)$itemid);
    $result = &$dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return FALSE;
    } else {
        return $fileId  ;
    }
}

?>
