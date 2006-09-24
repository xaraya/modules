<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * utility function to pass individual item links to whoever
 *
 * @param  $args ['itemtype'] item type (optional)
 * @param  $args ['itemids'] array of item ids to get
 * @return array containing the itemlink(s) for the item(s).
 */
function headlines_userapi_getitemlinks($args)
{
    $itemlinks = array();
    if (!xarSecurityCheck('OverviewHeadlines', 0)) {
        return $itemlinks;
    }

    foreach ($args['itemids'] as $itemid) {
        $item = xarModAPIFunc('headlines', 'user', 'get',
                              array('hid' => $itemid));
        if (!isset($item)) return;
        $itemlinks[$itemid] = array('url' => xarModURL('headlines', 'user', 'view',array('hid' => $itemid)),
                                    'title' => xarML('Display Headline'),
                                    'label' => xarVarPrepForDisplay($item['title']));
    }
    return $itemlinks;
}
?>