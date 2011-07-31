<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Carl Corliss <rabbitt@xaraya.com>
 */

 /**
  *  Get all mime types
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  integer    typeName  the Name of the mime type to lookup (optional)
  *  returns array      An array of (typeId, typeName) or an empty array
  */

function mime_userapi_add_type( $args )
{

    extract( $args );

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable     = xarDB::getTables();

    if (!isset($typeName) || empty($typeName)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                     'typeName','userapi_add_type','mime');
        throw new Exception($msg);
    }

    // table and column definitions
    $type_table =& $xartable['mime_type'];
    $typeId = $dbconn->genID($type_table);

    $sql = "INSERT
              INTO $type_table
                 (
                   id,
                   name
                 )
            VALUES (?, ?)";

    $bindvars = array($typeId, (string) $typeName);

    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->PO_Insert_ID($type_table, 'id');
    }
}

?>
