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
  *  Get the name of a mime type
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  integer    $typeId      the type ID of the mime type to attch subtypes to
  *  @param  string     $subtypeName the name of the subtype to add
  *  @param  string     $subtypeDesc the description of the subtype to add
  *  returns array      false on error, the sub type id otherwise
  */

function mime_userapi_add_subtype( $args )
{

    extract($args);

    if (!isset($typeId)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                     'typeId','userapi_add_subtypes','mime');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    }

    if (!isset($subtypeName)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                     'subtypeName','userapi_add_subtype','mime');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    }

    if (!isset($subtypeDesc) || !is_string($subtypeDesc)) {
        $subtypeDesc = NULL;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    // table and column definitions
    $subtype_table =& $xartable['mime_subtype'];
    $subtypeId     = $dbconn->GenID($subtype_table);

    $sql = "INSERT
              INTO $subtype_table
                 (
                   xar_mime_type_id,
                   xar_mime_subtype_id,
                   xar_mime_subtype_name,
                   xar_mime_subtype_desc
                 )
            VALUES (?, ?, ?, ?)";

    $bindvars = array((int)$typeId, $subtypeId, (string)$subtypeName, (string)$subtypeDesc);

    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->PO_Insert_ID($subtype_table, 'xar_mime_subtype_id');
    }
}

?>
