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

function mime_userapi_getall_magic($args)
{
    extract($args);

    if (isset($subtypeId)) {
        if (is_int($subtypeId)) {
            $where = " WHERE subtype_id = $subtypeId";
        } else {
            $msg = xarML(
                'Supplied parameter [#(1)] for function [#(2)], is not an integer!',
                'subtypeId',
                'mime_userapi_getall_magic'
            );
            throw new Exception($msg);
        }
    } else {
        $where = '';
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable     =& xarDB::getTables();

    // table and column definitions
    $magic_table =& $xartable['mime_magic'];

    $sql = "SELECT subtype_id,
                   id,
                   value,
                   offset,
                   length
              FROM $magic_table
            $where
          ORDER BY subtype_id,
                   offset";

    $result = $dbconn->Execute($sql);

    if (!$result | $result->EOF) {
        return array();
    }

    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);

        $subtypeInfo[$row['id']]['magicId']     = $row['id'];
        $subtypeInfo[$row['id']]['subtypeId']   = $row['subtype_id'];
        $subtypeInfo[$row['id']]['magicValue']  = $row['value'];
        $subtypeInfo[$row['id']]['magicOffset'] = $row['offset'];
        $subtypeInfo[$row['id']]['magicLength'] = $row['length'];

        $result->MoveNext();
    }
    $result->Close();

    return $subtypeInfo;
}
