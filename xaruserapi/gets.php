<?php
/**
 * Get items for one dyn data object
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls Module Development Team
 */
/**
 * Gets items of a DynamicData object
 *
 * This function is a short cut
 *
 * @author Brian McGilligan
 * @param $args['itemtype'] - Item type
 * @return list of items of the item type
 */
function maxercalls_userapi_gets($args)
{
    extract($args);

    $modid = xarModGetIDFromName('maxercalls');

    $info = array();
    $info['modid'] = $modid;
    $info['itemtype'] = $itemtype;

    $items = xarModAPIFunc('dynamicdata', 'user', 'getitems', $info);

    return $items;
}
?>