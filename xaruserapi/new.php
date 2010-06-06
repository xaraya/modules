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
 * Create a new item of the path object
  * @param args[$path] required string path
  * @param args[$action] required array action
 */
function path_userapi_new($args)
{
    // See if the current user has the privilege to add an item. We cannot pass any extra arguments here
    extract($args);

	// Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => 'path'));

	if (!isset($action['module']) || !isset($action['type']) || !isset($action['func'])) {
		$data['msg'] = "Action keys must include module, type and func.";
		return $data;
	}

	if($path[0] != '/') {
		$path = '/' . $path;
	}

	// Make sure the path is unique
	$mylist = DataObjectMaster::getObjectList(array('name' =>  'path'));
	$filters = array(
						'where' => 'path eq \'' . $path . '\''
					);

	$items = $mylist->getItems($filters);
	if(count($items) != 0) {
		$msg = "The path is not unique.";
		return $msg;
	}

	// Make sure there's no module alias conflict
	$pos = strpos($path, '/');
	if($pos) {
		$pathstart = substr($path, 0, $pos);
	} else {
		$pathstart = $path;
	}

	$aliases = xarConfigVars::get(null, 'System.ModuleAliases');

	if (empty($aliases[$pathstart])) {
		xarModAlias::set($pathstart, $action['module']);
	} else {
		// $pathstart is already registered as an alias
		$aliasmodule = $aliases[$pathstart];
		if($action['module'] != $aliasmodule) {
			$msg = 'The pathstart is already an alias for a module other than the module in the action';
			return $msg;
		}
	}

	$itemid = $object->createItem();
	return true;

	}

}

?>