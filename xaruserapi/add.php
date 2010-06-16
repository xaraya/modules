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
function path_userapi_add($args)
{
    extract($args);

    sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => 'path'));

	$pattern = '/^[\w\-\/]{1,}$/';
	if (!preg_match($pattern, $path)) {
		$data['errors'][] = "Path must be at least one character long and can contain only letters, numbers, slashes, underscores and dashes.";
	}

	if(is_array($action)) {
		foreach($action as $key=>$val){
			$action[$key] = trim($val);
		}
	}

	if (empty($action['module']) || empty($action['type']) || empty($action['func'])) {
		$data['errors'][] = "Action keys must include module, type and func.";
	}

	if($path[0] == '/') {
		$path = substr($path, 1);
	}
	$object->properties['path']->setValue($path);

	// Make sure the path is unique
	$checkpath = xarMod::apiFunc('path','admin','checkpath',array('path' => $path));
	if(!($checkpath)) {
		$data['errors'][] = "The path you've specified is already in use.  Please try again.";
	}  

	// Make sure there's no module alias conflict
	if (!empty($action['module'])) {
		$aliascheck = xarMod::apiFunc('path','admin','alias',array('path' => $path, 'actionmodule' => $action['module']));

		if(is_array($aliascheck)) {
			$data['errors'][] = 'Sorry, that pathstart ("' . $aliascheck['pathstart'] . '") is already an alias for the <a href="' . xarmodurl('modules','admin','aliases', array('name' => $aliascheck['aliasmodule'])) . '">' . $aliascheck['aliasmodule'] . '</a> module.  Please try a different path or specify a different module for the action.';
		}
	}

	if(!empty($data['errors'])) {
		return $data;
	}

	$itemid = $object->createItem();

	xarResponse::redirect(xarModURL('path','admin','view'));
	return true;

}

?>