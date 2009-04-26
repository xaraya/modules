<?php
/**
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * get a specific poll hooked to some external module item
 * @param $args['modname'] module name of the original item
 * @param $args['itemtype'] item type of the original item
 * @param $args['objectid'] object id of the original item
 * @returns array
 * @return item array, or false on failure
 */
function polls_userapi_gethooked($args)
{
    if (empty($args['modname'])) $args['modname'] = xarModGetName();
    $args['modid'] = xarModGetIDFromName($args['modname']);

    if (empty($args['modid'])) return;
    if (empty($args['itemtype'])) $args['itemtype'] = 0;
    if (empty($args['objectid'])) $args['objectid'] = 0;

    $args['startnum'] = 1; // -1?
    $args['numitems'] = 1;

    $args['getoptions'] = true;
    $args['fetchone'] = true;

    $items = xarModAPIfunc('polls', 'user', 'getall', $args);

    if (!empty($items)) {
        $item = reset($items);
    } else {
        $item = array();
    }

    return $item;
}

?>