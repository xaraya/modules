<?php
/**
 * Access Methods Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Access Methods Module
 * @link http://xaraya.com/index.php/release/333.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
/**
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item ids to get
 * @param $args['field'] field to return as label in the list (default 'name' - adapt as needed for your objects)
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function accessmethods_userapi_getitemlinks($args)
{
    extract($args);

    $itemlinks = array();
    if (empty($itemtype)) {
        $itemtype = 0;
    }
    if (empty($field)) {
        $field = 'name'; // adapt as needed for your own objects
    }
    $items = xarModAPIFunc('dynamicdata','user','getitems',
                           array('module'    => 'accessmethods',
                                 'itemtype'  => $itemtype,
                                 'itemids'   => $itemids,
                                 'fieldlist' => array($field)));
    if (empty($items)) {
        return array();
    }

    // if we didn't have a list of itemids, return all the items we found
    if (empty($itemids)) {
        $itemids = array_keys($items);
    }

    foreach ($itemids as $itemid) {
        if (isset($items[$itemid][$field])) {
            $label = xarVarPrepForDisplay($items[$itemid][$field]);
        } else {
            $label = xarML('Item #(1)',$itemid);
        }
        $itemlinks[$itemid] = array('url'   => xarModURL('accessmethods', 'user', 'display',
                                                         array('itemtype' => empty($itemtype) ? null : $itemtype,
                                                               'itemid' => $itemid)),
                                    'title' => xarML('Display Item'),
                                    'label' => $label);
    }
    return $itemlinks;
}

?>
