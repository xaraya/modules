<?php
/**
 * Check if an action is unique
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Path Module
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <ryan@webcommunicate.net>
 */
/**
* Check if an action is unique.  If it is unique, return the path's itemid and path
 * @param $args['action'] required array action
 */
function path_userapi_checkaction($args) {

	$getpath = false;

	extract($args);

	$action = xarMod::apiFunc('path','admin','standardizeaction',array('action' => $action));
	$action = serialize($action);

	sys::import('modules.dynamicdata.class.objects.master');
	$mylist = DataObjectMaster::getObjectList(array('name' =>  'path'));
	$filters = array(
						'where' => 'action eq \'' . $action . '\''
					);
	$items = $mylist->getItems($filters);
	if(count($items) == 0) {
		return false;
	} elseif (count($items) == 1) { 
		$item = end($items);
		$arr = array($item['itemid'] => $item['path']);
		return $arr;
	} else {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'itemcount', 'adminapi', 'checkaction', 'path');
        throw new Exception($msg);
		return;
	}
}
?>
