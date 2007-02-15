<?php
/**
 * Mime Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage mime
 * @author Carl P. Corliss
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
        $where[] = 'subtype_tab.xar_mime_type_id = ?';
        $bind[] = (int)$typeId;
    }

    if (isset($subtypeId) && is_int($subtypeId)) {
        $where[] = 'subtype_tab.xar_mime_subtype_id = ?';
        $bind[] = (int)$subtypeId;
    }

    if (isset($subtypeName) && is_string($subtypeName)) {
        $where[] = 'subtype_tab.xar_mime_subtype_name = ?';
        $bind[] = strtolower($subtypeName);
    }

    if (isset($typeName) && is_string($typeName)) {
        $where[] = 'type_tab.xar_mime_type_name = ?';
        $bind[] = strtolower($typeName);
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    // table and column definitions
    $subtype_table =& $xartable['mime_subtype'];
    $type_table =& $xartable['mime_type'];

    $sql = 'SELECT subtype_tab.xar_mime_type_id, subtype_tab.xar_mime_subtype_id,'
        . ' subtype_tab.xar_mime_subtype_name, subtype_tab.xar_mime_subtype_desc,'
        . ' type_tab.xar_mime_type_name'
        . ' FROM ' . $subtype_table . ' subtype_tab'
        . ' INNER JOIN ' . $type_table . ' type_tab ON type_tab.xar_mime_type_id = subtype_tab.xar_mime_type_id'
        . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '')
        . ' ORDER BY subtype_tab.xar_mime_type_id, subtype_tab.xar_mime_subtype_name ASC';

    $result = $dbconn->Execute($sql, $bind);

    // Return NULL in the event of an error.
    if (!$result) {return;}

    $subtypeInfo = array();
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);

        $subtypeInfo[$row['xar_mime_subtype_id']]['typeId']         = $row['xar_mime_type_id'];
        $subtypeInfo[$row['xar_mime_subtype_id']]['typeName']       = $row['xar_mime_type_name'];
        $subtypeInfo[$row['xar_mime_subtype_id']]['subtypeId']      = $row['xar_mime_subtype_id'];
        $subtypeInfo[$row['xar_mime_subtype_id']]['subtypeName']    = $row['xar_mime_subtype_name'];
        $subtypeInfo[$row['xar_mime_subtype_id']]['subtypeDesc']    = $row['xar_mime_subtype_desc'];

        $result->MoveNext();
    }
    $result->Close();

    return $subtypeInfo;
}

?>
