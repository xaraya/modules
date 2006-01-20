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
 * @author SIGMAPersonnel module development team
 */
/**
 * Gets items of a DynamicData object 'presencetype'
 *
 * @author
 * @param int $type - Type id to get the name for
 * @returns list of items of the item type
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