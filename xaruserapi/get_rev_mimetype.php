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
  *  Get the typeId and subtypeId for a named mimeType (ie: application/octet-stream)
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  string     the mime type we want to lookup id's for
  *  returns array      An array of (subtypeId, subtypeName) or an empty array
  */

function mime_userapi_get_rev_mimetype( $args )
{

    extract($args);

    if (empty($mimeType)) {
        // if not found return 0 for the id of both type / subtype
        return array('typeId' => 0, 'subtypeId' => 0);
    }

    $mimeType = explode('/', $mimeType);

    $typeInfo = xarModAPIFunc('mime','user','get_type', array('typeName' => $mimeType[0]));
    if (!isset($typeInfo['typeId'])) {
        // if not found return 0 for the id of both type / subtype
        return array('typeId' => 0, 'subtypeId' => 0);
    } else {
        $typeId =& $typeInfo['typeId'];
    }

    $subtypeInfo = xarModAPIFunc('mime', 'user', 'get_subtype', array('subtypeName' => $mimeType[1]));

    if (!isset($subtypeInfo['subtypeId'])) {
        // if not found return 0 for the subtypeId
        return array('typeId' => $typeId, 'subtypeId' => 0);
    } else {
        return array('typeId' => $typeId, 'subtypeId' => $subtypeInfo['subtypeId']);
    }

}

?>
