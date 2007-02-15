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
  *  Get the magic number(s) for a particular mime subtype
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  integer    subtypeId   the magicId of the magic # to lookup   (optional)788888888888888888888890
  *  returns array      An array of (subtypeid, magicId, magic, offset, length) or an empty array
  */

function mime_userapi_add_magic( $args )
{

    extract($args);

    if (!isset($subtypeId)) {
        $msg =  xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                      'subtypeId','userapi_add_magic','mime');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    }

    if (!isset($magicValue)) {
        $msg =  xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                      'magicValue','userapi_add_magic','mime');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    }

    if (!isset($magicOffset)) {
        $msg =  xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                      'magicOffset','userapi_add_magic','mime');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    }

    if (!isset($magicLength)) {
        $msg =  xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                      'magicLength','userapi_add_magic','mime');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable     = xarDBGetTables();

    // table and column definitions
    $magic_table =& $xartable['mime_magic'];
    $magicId     =  $dbconn->GenID($magic_table);

    $sql = "INSERT
              INTO $magic_table
                 (
                   xar_mime_subtype_id,
                   xar_mime_magic_id,
                   xar_mime_magic_value,
                   xar_mime_magic_offset,
                   xar_mime_magic_length
                 )
            VALUES (?, ?, ?, ?, ?)";

    $bindvars = array((int) $subtypeId, $magicId, (string) $magicValue, (int) $magicOffset, (int) $magicLength);

    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->PO_Insert_ID($magic_table, 'xar_mime_magic_id');
    }
}

?>
