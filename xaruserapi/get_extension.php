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
  *  @param  integer    extensionId        the ID of the extension to lookup   (optional)
  *  @param  integer    extensionName     the Name of the extension to lookup (optional)
  *  returns array      An array of (subtypeId, extension) or an empty array
  */

function mime_userapi_get_extension( $args )
{

    extract($args);

    if (!isset($extensionId) && !isset($extensionName)) {
        $msg = xarML('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime','userapi','get_extension');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable     = xarDBGetTables();

    $where = ' WHERE ';

    if (isset($extensionId)) {
        $where .= ' xar_mime_extension_id = ' . $extensionId;
    } else {
        $where .= " xar_mime_extension_name = '".strtolower($extensionName)."'";
    }

    // table and column definitions
    $extension_table =& $xartable['mime_extension'];

    $sql = "SELECT xar_mime_subtype_id,
                   xar_mime_extension_id,
                   xar_mime_extension_name
              FROM $extension_table
            $where";

    $result = $dbconn->Execute($sql);

    if (!$result || $result->EOF)  {
        return array();
    }

    $row = $result->GetRowAssoc(false);

    return array('subtypeId'     => $row['xar_mime_subtype_id'],
                 'extensionId'   => $row['xar_mime_extension_id'],
                 'extensionName' => $row['xar_mime_extension_name']);
}

?>
