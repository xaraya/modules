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
 * Attempt to convert a MIME type to a file extension.
 * If we cannot map the type to a file extension, we return false.
 *
 * Code originally based on hordes Magic class (www.horde.org)
 *
 * @author  Carl P. Corliss
 * @access  public
 * @param   string      $mime_type MIME type to be mapped to a file extension.
 * @return  string      The file extension of the MIME type.
 */
function mime_userapi_mime_to_extension( $args )
{

    extract($args);

    if (!isset($mime_type) || empty($mime_type)) {
        $msg = xarML('Missing \'mime_type\' parameter!');
        throw new Exception($msg);
    }

    $typeparts = explode('/',$mime_type);
    if (count($typeparts) < 2) {
        $msg = xarML('Missing mime type or subtype parameter!');
        throw new Exception($msg);
    }

    $xartable = xarDB::getTables();
    $dbconn = xarDB::getConn();
    $query = "
        SELECT xar_mime_type_name AS type_name, 
               xar_mime_subtype_name AS subtype_name, 
               xar_mime_extension_name AS extension 
        FROM   $xartable[mime_type] mt, 
               $xartable[mime_subtype] mst, 
               $xartable[mime_extension] me 
        WHERE  mt.xar_mime_type_id = mst.xar_mime_type_id AND 
               mst.xar_mime_subtype_id = me.xar_mime_subtype_id AND
               mt.xar_mime_type_name = ? AND 
               mst.xar_mime_subtype_name = ?
        LIMIT  1";
    
    $result = $dbconn->Execute($query,array($typeparts[0],$typeparts[1]),ResultSet::FETCHMODE_ASSOC);
    if($result->getRecordCount() != 1) 
        return false;
    return $result->getRow();
}

?>
