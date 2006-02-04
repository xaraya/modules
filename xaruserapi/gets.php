<?php
/**
 * Utility function to get DD items
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Gets items of a DynamicData object
 * @author Brian McGilligan
 * @param $args['itemtype'] - Item type
 * @returns list of items of the item type
 */
function courses_userapi_gets($args)
{
    extract($args);

    $modid = xarModGetIDFromName('courses');

    $info = array();
    $info['modid'] = $modid;
    $info['itemtype'] = $itemtype;

    $items = xarModAPIFunc('dynamicdata', 'user', 'getitems', $info);

    return $items;
}
?>
