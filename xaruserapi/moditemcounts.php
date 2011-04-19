<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**

 */
function comments_userapi_moditemcounts($args) 
{

    $moditemcounts = array();

    $items = xarMod::apiFunc('comments','user','getitems',$args);

    extract($args);

    sys::import('modules.dynamicdata.class.objects.master');

    foreach($items as $item) {
        if(!isset($objectid) || $objectid != $item['objectid']) {

            $filters['where'] = 'objectid eq ' . $item['objectid'];
            if (isset($itemtype)) {
                $filters['where'] .= ' and itemtype eq ' . $itemtype;
            }

            if (isset($status) && $status == 'inactive') {
                $filters['where'] .= ' and status eq ' . _COM_STATUS_OFF;
            } else {
                $filters['where'] .= ' and status ne ' . _COM_STATUS_ROOT_NODE;
            }
            $list = DataObjectMaster::getObjectList(array(
                                'name' => 'comments'
                            ));
            $items = $list->getItems($filters);
            $count = count($items);
            $id = $item['objectid'];
            $itemtype = $item['itemtype'];

            $moditemcounts[$id] = array('count' => $count, 'itemtype' => $itemtype);

        }
        $objectid = $item['objectid'];
    }


    return $moditemcounts;

}
?>