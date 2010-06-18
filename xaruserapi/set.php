<?php
/**
 * Add a new item
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
 * Create (or update) an item of the path object
  * @param args[$path] required string path
  * @param args[$action] required array action
  * @param args[$itemid] optional itemid if you want to update an item
 */
function path_userapi_set($args)
{

	$update = false;

	extract($args);

    sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => 'path'));

	$data['errors'] = array();

	$pattern = '/^[\w\-\/]{1,}$/';
	if (!preg_match($pattern, $path)) {
		$data['errors'][] = "Path must be at least one character long and can contain only letters, numbers, slashes, underscores and dashes.";
	}	

	if (empty($action['module'])) {
		$data['errors'][] = "Action keys must include module";
	}

	if($path[0] != '/') {
		$path = '/' . $path;
	}

	$action = xarMod::apiFunc('path','admin','standardizeaction',array('action' => $action));

	// Make sure the path is unique
	if (!isset($itemid)) {
		$checkpath = xarMod::apiFunc('path','user','checkpath',array('path' => $path));
		if($checkpath) {
			$data['errors'][] = "The path you've specified is already in use.  Please try again.";
		}  
	}

	// Make sure there's no module alias conflict

	$aliascheck = xarMod::apiFunc('path','admin','alias',array('path' => $path, 'actionmodule' => $action['module']));

	if(is_array($aliascheck)) {
		$data['errors'][] = 'Sorry, that pathstart ("' . $aliascheck['pathstart'] . '") is already an alias for the <a href="' . xarmodurl('modules','admin','aliases', array('name' => $aliascheck['aliasmodule'])) . '">' . $aliascheck['aliasmodule'] . '</a> module.  Please try a different path or specify a different module for the action.';
	}
	

	if(!empty($data['errors'])) {
		return $data;
	} else {
		$object->properties['path']->setValue($path);
		$object->properties['action']->setValue($action);

		if (isset($itemid)) {
			$itemid = $object->updateItem(array('itemid' => $itemid));
		} else {
			$itemid = $object->createItem();
		}

		return array('itemid' => $itemid, 'path' => $path, 'action' => $action);

	}

}

?>