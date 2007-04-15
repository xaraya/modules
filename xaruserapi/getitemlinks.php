<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function comments_userapi_getitemlinks($args)
{
    extract($args);
    $itemlinks = array();
    if (!xarSecurityCheck('ReadComments', 0)) {
        return $itemlinks;
    }
    if (empty($itemids)) {
        $itemids = array();
    }
// FIXME: support retrieving several comments at once
    foreach ($itemids as $itemid) {
        $item = xarModAPIFunc('comments', 'user', 'get_one', array('id' => $itemid));
        if (!isset($item)) return;
        if (!empty($item) && !empty($item[0]['title'])) {
            $title = $item[0]['title'];
        } else {
            $title = xarML('Comment #(1)',$itemid);
        }
        $itemlinks[$itemid] = array('url'   => xarModURL('comments', 'user', 'display',
                                                         array('id' => $itemid)),
                                    'title' => xarML('Display Comment'),
                                    'label' => xarVarPrepForDisplay($title));
    }
    return $itemlinks;
}
?>
