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
  *  Get the name of a mime type
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  integer    typeId    the ID of the mime type to lookup   (optional)
  *  @param  integer    typeName  the Name of the mime type to lookup (optional)
  *  returns array      An array of (typeId, typeName) or an empty array
  */

function mime_userapi_get_type( $args )
{

    extract($args);

    if (!isset($typeId) && !isset($typeName)) {
        $msg = xarML('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime','userapi','get_type');
        throw new Exception($msg);
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable     = xarDB::getTables();

    $where = ' WHERE ';

    if (isset($typeId)) {
        $where .= ' xar_mime_type_id = ' . $typeId;
    } else {
        $where .= " xar_mime_type_name = '".strtolower($typeName)."'";
    }

    // table and column definitions
    $type_table =& $xartable['mime_type'];

    $sql = "SELECT xar_mime_type_id,
                   xar_mime_type_name
              FROM $type_table
            $where";

    $result = $dbconn->Execute($sql);

    if (!$result || $result->EOF)  {
        return array();
    }

    $row = $result->GetRowAssoc(false);

    return array('typeId'   => $row['xar_mime_type_id'],
                 'typeName' => $row['xar_mime_type_name']);
}

?>
