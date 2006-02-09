<?php
/**
 * Utility function to get DD item for level
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Gets items of a DynamicData object 'level'
 *
 * @author Brian McGilligan
 * @param $args['itemtype'] - Item type
 * @returns list of items of the item type
 */
function courses_userapi_getlevel($args)
{
    extract($args);
    if (!isset($level) || !is_numeric($level)) {
        return false;
    }
    $modid = xarModGetIDFromName('courses');

    $info = array();
    $info['modid'] = $modid;
    $info['itemtype'] = 1003;
    $info['itemid'] = $level;
    $info['name'] = 'level';
    $item = xarModAPIFunc('dynamicdata', 'user', 'getfield', $info);

    return $item;
}
?>
