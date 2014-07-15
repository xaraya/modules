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
        throw new Exception($msg);
    }

    if (!isset($magicValue)) {
        $msg =  xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                      'magicValue','userapi_add_magic','mime');
        throw new Exception($msg);
    }

    if (!isset($magicOffset)) {
        $msg =  xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                      'magicOffset','userapi_add_magic','mime');
        throw new Exception($msg);
    }

    if (!isset($magicLength)) {
        $msg =  xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                      'magicLength','userapi_add_magic','mime');
        throw new Exception($msg);
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable     =& xarDB::getTables();

    // table and column definitions
    $magic_table =& $xartable['mime_magic'];
    $magicId     =  $dbconn->genID($magic_table);

    $sql = "INSERT
              INTO $magic_table
                 (
                   id,
                   subtype_id,
                   value,
                   offset,
                   length
                 )
            VALUES (?, ?, ?, ?, ?)";

    $bindvars = array((int) $magicId, $subtypeId, (string) $magicValue, (int) $magicOffset, (int) $magicLength);

    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->PO_Insert_ID($magic_table, 'id');
    }
}

?>
