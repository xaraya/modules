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
  *  @param  integer    matgicId     the magicId of the magic # to lookup   (optional)
  *  @param  string     magicValue   the magic value of the magic # to lookup (optional)
  *  returns array      An array of (subtypeid, magicId, magic, offset, length) or an empty array
  */

function mime_userapi_get_magic( $args )
{

    extract($args);

    if (!isset($magicId) && !isseT($magicValue)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module[#(3)].', 'magicId','userapi_get_magic','mime');
        throw new Exception($msg);
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable     = xarDB::getTables();

    $where = ' WHERE ';

    if (isset($magicId)) {
        $where .= ' id = ' . $magicId;
    } else {
        $where .= " value = '".strtolower($magicValue)."'";
    }

    // table and column definitions
    $magic_table =& $xartable['mime_magic'];

    $sql = "SELECT subtype_id,
                   id,
                   value,
                   offset,
                   length
              FROM $magic_table
            $where";

    $result = $dbconn->Execute($sql);

    if (!$result || $result->EOF)  {
        return array();
    }

    $row = $result->GetRowAssoc(false);

    return array('subtypeId'   => $row['subtype_id'],
                 'magicId'     => $row['id'],
                 'magicValue'  => $row['value'],
                 'magicOffset' => $row['offset'],
                 'magicLength' => $row['length']);
}

?>
