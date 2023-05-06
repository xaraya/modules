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
 *  Imports the mimelist array and adds it to the database
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   array      $mimeList   List of mimetypes with their extensions (if any) and magics (if any)
 *  @returns boolean    true if successful importing into database, false otherwise.
 */

xarMod::apiLoad('mime','user');
 
function mime_userapi_import_mimelist( $args ) 
{
 
    extract($args);

    $descriptions = array();

    foreach($mimeList as $mimeTypeText => $mimeInfo) {
        /* 
            start off processing the mimetype and mimesubtype
            if niether of those exist, create them :)
        */
        $mimeType = explode('/', $mimeTypeText);
        
        $typeInfo = xarMod::apiFunc('mime','user','get_type', array('typeName' => $mimeType[0]));
        if (!isset($typeInfo['typeId'])) {
            $typeId = xarMod::apiFunc('mime','user','add_type', array('typeName' => $mimeType[0]));
        } else {
            $typeId =& $typeInfo['typeId'];
        }
        
        $subtypeInfo = xarMod::apiFunc('mime', 'user', 'get_subtype', array('subtypeName' => $mimeType[1]));
        if (!isset($subtypeInfo['subtypeId'])) {
            $subtypeId = xarMod::apiFunc(
                'mime', 'user', 'add_subtype', 
                array(
                    'subtypeName'   => $mimeType[1], 
                    'typeId'        => $typeId,
                    'subtypeDesc'   => (isset($mimeInfo['description']) ? $mimeInfo['description'] : NULL)
                )
            );
        } else {
            $subtypeId =& $subtypeInfo['subtypeId'];
        }
        
        if (isset($mimeInfo['extensions']) && count($mimeInfo['extensions'])) {
            foreach($mimeInfo['extensions'] as $extension) {
                $extensionInfo = xarMod::apiFunc('mime', 'user', 'get_extension', 
                                                array('extensionName' => $extension));
                if (!isset($extensionInfo['extensionId'])) {
                    $extensionId = xarMod::apiFunc('mime','user','add_extension', 
                                   array('subtypeId'     => $subtypeId,
                                         'extensionName' => $extension));
                } else {
                    $extensionId = $extensionInfo['extensionId'];
                }
            }
        }
        
        if (isset($mimeInfo['needles']) && count($mimeInfo['needles'])) {
            foreach($mimeInfo['needles'] as $magicNumber => $magicInfo) {
                $info = xarMod::apiFunc('mime', 'user', 'get_magic', 
                                            array('magicValue' => $magicNumber));
                if (!isset($info['magicId'])) {
                    $magicId = xarMod::apiFunc('mime', 'user', 'add_magic',
                                    array('subtypeId'   => $subtypeId,
                                          'magicValue'  => $magicNumber,
                                          'magicOffset' => $magicInfo['offset'],
                                          'magicLength' => $magicInfo['length']));
                } else {
                    $magicId = $info['magicId'];
                }
            }
        }
        
    }
    
    return TRUE;
} 
  
?>
