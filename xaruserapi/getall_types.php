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
  *  Get all mime types
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  integer    typeId    the ID of the mime type to lookup   (optional)
  *  @param  integer    typeName  the Name of the mime type to lookup (optional)
  *  returns array      An array of (typeId, typeName) or an empty array
  */

function mime_userapi_getall_types( /* VOID */ )
{

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable     = xarDBGetTables();

    // table and column definitions
    $type_table =& $xartable['mime_type'];

    $sql = "SELECT xar_mime_type_id,
                   xar_mime_type_name
              FROM $type_table
          ORDER BY xar_mime_type_name";

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

        $typeInfo[$row['xar_mime_type_id']]['typeId'] = $row['xar_mime_type_id'];
        $typeInfo[$row['xar_mime_type_id']]['typeName'] = $row['xar_mime_type_name'];

        $result->MoveNext();
    }

    $result->Close();
    return $typeInfo;

}

?>
