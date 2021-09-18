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
  *  Get the name of a mime type
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  integer    typeId    the ID of the mime type to lookup   (optional)
  *  @param  integer    typeName  the Name of the mime type to lookup (optional)
  *  returns array      An array of (typeId, typeName) or an empty array
  */

function mime_userapi_get_type($args)
{
    extract($args);

    if (!isset($typeId) && !isset($typeName)) {
        $msg = xarML('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime', 'userapi', 'get_type');
        throw new Exception($msg);
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable     =& xarDB::getTables();

    $where = ' WHERE ';

    if (isset($typeId)) {
        $where .= ' id = ' . $typeId;
    } else {
        $where .= " name = '".strtolower($typeName)."'";
    }

    // table and column definitions
    $type_table =& $xartable['mime_type'];

    $sql = "SELECT id,
                   name
              FROM $type_table
            $where";

    $result = $dbconn->Execute($sql);

    if (!$result || $result->EOF) {
        return [];
    }

    $row = $result->GetRowAssoc(false);

    return ['typeId'   => (int)$row['id'],
                 'typeName' => $row['name'], ];
}
