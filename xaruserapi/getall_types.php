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
  *  @param  integer    typeId    the ID of the mime type to lookup   (optional)
  *  @param  integer    typeName  the Name of the mime type to lookup (optional)
  *  returns array      An array of (typeId, typeName) or an empty array
  */

function mime_userapi_getall_types($args)
{
    extract($args);

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable     = xarDB::getTables();

    // table and column definitions
    $type_table =& $xartable['mime_type'];

    if (isset($state) && is_array($state)) {
        $where = 'state in (' . implode(', ', $state) . ')';
    }
    if (isset($state) && !is_array($state)) {
        $where = 'state = ' . (int)$state;
    }
    $sql = "SELECT id,
                   name
              FROM $type_table";
    $sql .= (isset($where) ? ' WHERE ' . $where : '');
    $sql .=   " ORDER BY name";

    $result = $dbconn->Execute($sql);

    if (!$result)  {
        return array();
    }

    // if no record found, return an empty array
    if ($result->EOF) {
        return array();
    }

    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);

        $typeInfo[$row['id']]['typeId'] = $row['id'];
        $typeInfo[$row['id']]['typeName'] = $row['name'];

        $result->MoveNext();
    }

    $result->Close();
    return $typeInfo;
}
?>