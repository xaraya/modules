<?php
/**
 * Utility function to get DD item for type
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team 
 */
/**
 * Gets items of a DynamicData object 'type'
 *
 * @author Brian McGilligan
 * @param $args['itemtype'] - Item type
 * @returns list of items of the item type
 */
function courses_userapi_gettype($args)
{
    extract($args);
    
    $modid = xarModGetIDFromName('courses');

    $info = array();
    $info['modid'] = $modid;
    $info['itemtype'] = 3;
    $info['itemid'] = $type;
    $info['name'] = 'type';
    $item = xarModAPIFunc('dynamicdata', 'user', 'getfield', $info);
    
    return $item;
}
?>
