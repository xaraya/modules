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
 *  Get the typeId and subtypeId for a named mimeType (ie: application/octet-stream)
 *
 *  @author Carl P. Corliss
 *  @access public
 *  @param  string     the mime type we want to lookup id's for
 *  returns array      An array of (subtypeId, subtypeName) or an empty array
 */

function mime_userapi_get_rev_mimetype($args)
{
    extract($args);

    if (empty($mimeType)) {
        // if not found return 0 for the id of both type / subtype
        return ['typeId' => 0, 'subtypeId' => 0];
    }
    if (is_numeric($mimeType)) {
        // Do a lookup
        $types = DataObjectMaster::getObject(['name' => 'mime_types']);
        $types->getItem(['itemid' => $mimeType]);
        $mimeType = $types->properties['name']->value;
    }

    $mimeType = explode('/', $mimeType);

    $typeInfo = xarMod::apiFunc('mime', 'user', 'get_type', ['typeName' => $mimeType[0]]);
    if (!isset($typeInfo['typeId'])) {
        // if not found return 0 for the id of both type / subtype
        return ['typeId' => 0, 'subtypeId' => 0];
    } else {
        $typeId =& $typeInfo['typeId'];
    }

    $subtypeInfo = xarMod::apiFunc('mime', 'user', 'get_subtype', ['subtypeName' => $mimeType[1]]);

    if (!isset($subtypeInfo['subtypeId'])) {
        // if not found return 0 for the subtypeId
        return ['typeId' => $typeId, 'subtypeId' => 0];
    } else {
        return ['typeId' => $typeId, 'subtypeId' => $subtypeInfo['subtypeId']];
    }
}
