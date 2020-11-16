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

 */
function comments_userapi_moditemcounts($args)
{
    $moditemcounts = array();

    $items = xarMod::apiFunc('comments', 'user', 'getitems', $args);

    extract($args);

    sys::import('modules.dynamicdata.class.objects.master');

    foreach ($items as $item) {
        if (!isset($itemid) || $itemid != $item['itemid']) {
            $filters['where'] = 'itemid eq ' . $item['itemid'];
            if (isset($itemtype)) {
                $filters['where'] .= ' and itemtype eq ' . $itemtype;
            }

            if (isset($status) && $status == 'inactive') {
                $filters['where'] .= ' and status eq ' . _COM_STATUS_OFF;
            } else {
                $filters['where'] .= ' and status ne ' . _COM_STATUS_ROOT_NODE;
            }
            $list = DataObjectMaster::getObjectList(array(
                                'name' => 'comments_comments'
                            ));
            $items = $list->getItems($filters);
            $count = count($items);
            $id = $item['itemid'];
            $itemtype = $item['itemtype'];

            $moditemcounts[$id] = array('count' => $count, 'itemtype' => $itemtype);
        }
        $objectid = $item['objectid'];
    }


    return $moditemcounts;
}
