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
  *  @param  integer    $subtypeId      the subtype ID to add an extension for
  *  @param  string     $extensionName  the extension name to add
  *  returns array      An array of (subtypeId, extension) or an empty array
  */

function mime_userapi_add_extension($args)
{
    extract($args);

    if (!isset($subtypeId)) {
        $msg = xarML(
            'Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
            'subtypeId',
            'userapi_add_extension',
            'mime'
        );
        throw new Exception($msg);
    }

    if (!isset($extensionName)) {
        $msg = xarML(
            'Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
            'extensionName',
            'userapi_add_extension',
            'mime'
        );
        throw new Exception($msg);
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable     =& xarDB::getTables();

    // table and column definitions
    $extension_table =& $xartable['mime_extension'];
    $extensionId     = $dbconn->genID($extension_table);

    $sql = "INSERT
              INTO $extension_table
                 ( subtype_id,
                   id,
                   name
                 )
            VALUES (?, ?, ?)";
    $bindvars = [(int) $subtypeId, $extensionId, (string) strtolower($extensionName)];

    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return false;
    } else {
        return $dbconn->PO_Insert_ID($extension_table, 'id');
    }
}
