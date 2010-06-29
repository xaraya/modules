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
  * @param args[$currpath] optional currpath if you want to update an item
  
  If itemid is passed, look up the path to update by itemid.  If currpath is passed, look up the path to update by current path.

 */
function path_userapi_set($args)
{

	$update = false;

	extract($args);

	if(!isset($path) || empty($path)) return;

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

	// Make sure the new path is unique
	// If we're passing an itemid, then we must be trying to update an existing path and don't want this check 
	if (!isset($itemid)) {
		$checkpath = xarMod::apiFunc('path','user','checkpath',array('path' => $path));
		if($checkpath) {
			$data['errors'][] = "The path you've specified is already in use.  Please try again.";
		}  
	}

	// Make sure the action is unique
	// If we're passing currpath or itemid, then we must be trying to update a path for an existing action and so we should skip this check 
	if (!isset($currpath) && !isset($itemid)) {
		$checkaction = xarMod::apiFunc('path','user','checkaction',array('action' => $action));
		if($checkaction) {
			$data['errors'][] = "The action you've specified already has a path.  Please try again.";
		}
	}

	// Make sure there's no module alias conflict
	$aliascheck = xarMod::apiFunc('path','admin','alias',array('path' => $path, 'actionmodule' => $action['module']));
	if(is_string($aliascheck)) { 
		$data['errors'][] = 'The pathstart <strong>"'. $aliascheck . '"</strong> is the name of an installed module.';
	}

	if(is_array($aliascheck)) {
		$data['errors'][] = 'Sorry, that pathstart (<strong>"' . $aliascheck['pathstart'] . '"</strong>) is already an alias for the <a href="' . xarmodurl('modules','admin','aliases', array('name' => $aliascheck['aliasmodule'])) . '">' . $aliascheck['aliasmodule'] . '</a> module.  Please try a different path or specify a different module for the action.';
	}	

	if(!empty($data['errors'])) {
		return $data;
	} else {

		if($path[0] != '/') {
			$path = '/' . $path;
		}

		$action = xarMod::apiFunc('path','admin','standardizeaction',array('action' => $action));

		$object->properties['path']->setValue($path);
		$object->properties['action']->setValue($action);

		// If both itemid and currpath are set, prefer the itemid
		if (!isset($itemid) && isset($currpath)) {
			$currinfo = xarMod::apiFunc('path','user','checkpath',array('path' => $currpath));
			if ($currinfo) {
				$arr = array_keys($currinfo);
				$curritemid = reset($arr);
			}
		}
 
		if (isset($itemid)) {
			$itemid = $object->updateItem(array('itemid' => $itemid));
		} elseif (isset($curritemid)) {
			$itemid = $object->updateItem(array('itemid' => $curritemid));
		} else {
			$itemid = $object->createItem();
		}

		return array('itemid' => $itemid, 'path' => $path, 'action' => $action);

	}

}

?>