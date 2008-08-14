<?php
/*
 *
 * Mime Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage mime
 * @author Carl P. Corliss
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
                   xar_mime_type_id,
                   xar_mime_type_name
                 )
            VALUES (?, ?)";

    $bindvars = array($typeId, (string) $typeName);

    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->PO_Insert_ID($type_table, 'xar_mime_type_id');
    }
}

?>
