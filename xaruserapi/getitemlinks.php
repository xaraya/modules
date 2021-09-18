<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    $itemlinks = [];
    if (!xarSecurity::check('ReadComments', 0)) {
        return $itemlinks;
    }

    if (empty($itemids)) {
        $itemids = [];
    }

    // FIXME: support retrieving several comments at once
    foreach ($itemids as $itemid) {
        $item = xarMod::apiFunc('comments', 'user', 'get_one', ['id' => $itemid]);
        if (!isset($item)) {
            return;
        }
        if (!empty($item) && !empty($item[0]['title'])) {
            $title = $item[0]['title'];
        } else {
            $title = xarML('Comment #(1)', $itemid);
        }
        $itemlinks[$itemid] = ['url'   => xarController::URL(
            'comments',
            'user',
            'display',
            ['id' => $itemid]
        ),
                                    'title' => xarML('Display Comment'),
                                    'label' => xarVar::prepForDisplay($title), ];
    }
    return $itemlinks;
}
