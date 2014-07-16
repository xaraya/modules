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
  *  Get details for mime subtypes
  *
  *  @author Carl P. Corliss
  *  @author Jason Judge
  *  @access public
  *  @param  integer    typeId the type ID of the mime type to grab subtypes for
  *  @param  integer    subtypeId the subtype ID of the mime type, which should fetch just one subtype
  *  @param  string     subtypeName the subtype name of the mime type, which should fetch just one subtype
  *  @param  string     typeName the type name of the mime type
  *  @param  string     mimeName the full two-part mime name
  *  returns array      An array of (typeid, subtypeId, subtypeName, subtypeDesc) or an empty array
  */

function mime_userapi_getall_subtypes($args)
{
    extract($args);

    $where = array();
    $bind = array();

    // The complete mime name can be passed in (type/subtype) and this
    // will be split up here for convenience.
    if (isset($mimeName) && is_string($mimeName)) {
        $parts = explode('/', strtolower(trim($mimeName)), 2);
        if (count($parts) == 2) {
            list($typeName, $subtypeName) = $parts;
        }
    }

    if (isset($typeId) && is_int($typeId)) {
        $where[] = 'subtype_tab.type_id = ?';
        $bind[] = (int)$typeId;
    }

    if (isset($subtypeId) && is_int($subtypeId)) {
        $where[] = 'subtype_tab.id = ?';
        $bind[] = (int)$subtypeId;
    }

    if (isset($subtypeName) && is_string($subtypeName)) {
        $where[] = 'subtype_tab.name = ?';
        $bind[] = strtolower($subtypeName);
    }

    if (isset($typeName) && is_string($typeName)) {
        $where[] = 'type_tab.name = ?';
        $bind[] = strtolower($typeName);
    }

    if (isset($typeName) && is_string($typeName)) {
        $where[] = 'type_tab.name = ?';
        $bind[] = strtolower($typeName);
    }
    if (isset($state) && !is_array($state)) {
        $where[] = 'subtype_tab.state = ?';
        $bind[] = (int)$state;
    }
    if (isset($state) && is_array($state)) {
        $where[] = 'subtype_tab.state in (' . implode(', ', $state) . ')';
        $bind[] = $state;
    }
    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // table and column definitions
    $subtype_table =& $xartable['mime_subtype'];
    $type_table =& $xartable['mime_type'];

    $sql = 'SELECT subtype_tab.type_id AS type_id, subtype_tab.id AS id,'
        . ' subtype_tab.name AS name, subtype_tab.description,'
        . ' type_tab.name AS type_name'
        . ' FROM ' . $subtype_table . ' subtype_tab'
        . ' INNER JOIN ' . $type_table . ' type_tab ON type_tab.id = subtype_tab.type_id'
        . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '')
        . ' ORDER BY subtype_tab.type_id, subtype_tab.name ASC';

    $result = $dbconn->Execute($sql, $bind);

    // Return NULL in the event of an error.
    if (!$result) {return;}

    $subtypeInfo = array();
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);

        $subtypeInfo[$row['id']]['subtypeId']      = $row['id'];
        $subtypeInfo[$row['id']]['subtypeName']    = $row['name'];
        $subtypeInfo[$row['id']]['subtypeDesc']    = $row['description'];
        $subtypeInfo[$row['id']]['typeId']         = $row['type_id'];
        $subtypeInfo[$row['id']]['typeName']       = $row['type_name'];

        $result->MoveNext();
    }
    $result->Close();

    return $subtypeInfo;
}
?>