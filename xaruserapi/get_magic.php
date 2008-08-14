<?php
/*
 *
 * Mime Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
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
        $where .= ' xar_mime_magic_id = ' . $magicId;
    } else {
        $where .= " xar_mime_magic_value = '".strtolower($magicValue)."'";
    }

    // table and column definitions
    $magic_table =& $xartable['mime_magic'];

    $sql = "SELECT xar_mime_subtype_id,
                   xar_mime_magic_id,
                   xar_mime_magic_value,
                   xar_mime_magic_offset,
                   xar_mime_magic_length
              FROM $magic_table
            $where";

    $result = $dbconn->Execute($sql);

    if (!$result || $result->EOF)  {
        return array();
    }

    $row = $result->GetRowAssoc(false);

    return array('subtypeId'   => $row['xar_mime_subtype_id'],
                 'magicId'     => $row['xar_mime_magic_id'],
                 'magicValue'  => $row['xar_mime_magic_value'],
                 'magicOffset' => $row['xar_mime_magic_offset'],
                 'magicLength' => $row['xar_mime_magic_length']);
}

?>
