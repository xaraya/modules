<?php
/**
 * Utility function to get DD item for type
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author SIGMAPersonnel module development team
 */
/**
 * Gets items of a DynamicData object 'presencetype'
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int $type - Type id to get the name for
 * @return array item
 */
function sigmapersonnel_userapi_getstatustype($args)
{
    extract($args);

    $modid = xarModGetIDFromName('sigmapersonnel');

    $info = array();
    $info['modid'] = $modid;
    $info['itemtype'] = 6;
    $info['itemid'] = $type;
    $info['name'] = 'statustype';
    $item = xarModAPIFunc('dynamicdata', 'user', 'getfield', $info);
    return $item;
}
?>