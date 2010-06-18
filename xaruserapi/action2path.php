<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <potion@xaraya.com>
 */
/**
 *    Given an action (array), try to look up a path
 */
function path_userapi_action2path($args) {

	extract($args);

	$action = xarMod::apiFunc('path','admin','standardizeaction',array('action' => $action));

	$action = serialize($action);

	sys::import('modules.dynamicdata.class.objects.master');

	$list = DataObjectMaster::getObjectList(array('name' => 'path'));
	$filters = array(
		'where' => 'action eq \'' . $action . '\''
	);
	$items = $list->getItems($filters);
	if (empty($items)) {
		return false;
	} else {
		$item = end($items);
		return $item['path'];
	}

} 
?>