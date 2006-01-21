<?php
/**
 * Utility function to get DD item for type
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SIGMAPersonnel
 * @link http://xaraya.com/index.php/release/418.html
 * @author SIGMAPersonnel module development team
 */
/**
 * Gets items of a DynamicData object 'presencetype'
 *
 * @author
 * @param int $type - Type id to get the name for
 * @returns list of items of the item type
 */
function sigmapersonnel_userapi_getprestype($args)
{
    extract($args);

    $modid = xarModGetIDFromName('sigmapersonnel');

    $info = array();
    $info['modid'] = $modid;
    $info['itemtype'] = 5;
    $info['itemid'] = $type;
    $info['name'] = 'type';
    $item = xarModAPIFunc('dynamicdata', 'user', 'getfield', $info);
    return $item;
}
?>