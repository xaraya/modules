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
  *  Get the name of a mime type
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  integer    subtypeId       the subtype ID of the type to grab extensions for
  *  returns array      An array of (subtypeId, extension) or an empty array
  */

function mime_userapi_getall_extensions( $args )
{

    extract($args);

    if (isset($subtypeId)) {
        if (is_int($subtypeId)) {
            $where = " WHERE xar_mime_subtype_id = $subtypeId";
        } else {
            $msg = xarML('Supplied parameter [#(1)] for function [#(2)], is not an integer!',
                         'subtypeId','mime_userapi_getall_extensions');
            throw new Exception($msg);
        }
    } else {
        $where = '';
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable     = xarDB::getTables();

    // table and column definitions
    $extension_table =& $xartable['mime_extension'];

    $sql = "SELECT xar_mime_subtype_id,
                   xar_mime_extension_id,
                   xar_mime_extension_name
              FROM $extension_table
            $where
          ORDER BY xar_mime_subtype_id,
                   xar_mime_extension_name";

    $result = $dbconn->Execute($sql);

    if (!$result | $result->EOF)  {
        return array();
    }

    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);

        $subtypeInfo[$row['xar_mime_extension_id']]['extensionId']   = $row['xar_mime_extension_id'];
        $subtypeInfo[$row['xar_mime_extension_id']]['extensionName'] = $row['xar_mime_extension_name'];

        $result->MoveNext();
    }
    $result->Close();

    return $subtypeInfo;
}

?>
