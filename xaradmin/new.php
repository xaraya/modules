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
 */
function path_admin_new()
{
    // See if the current user has the privilege to add an item. We cannot pass any extra arguments here
    if (!xarSecurityCheck('AddPath')) return;

	if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

	// Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => 'path'));
	
	$data['object'] = $object;

	if ($data['confirm']) {

		$isvalid =  $object->properties['path']->checkInput();
		$isvalid2 =  $object->properties['action']->checkInput();

		if ($isvalid && $isvalid2) { 
			$path = $object->properties['path']->getValue();
			$action = $object->properties['action']->getValue();

			if (!isset($action['module']) || !isset($action['type']) || !isset($action['func'])) {
				$data['msg'] = "Action keys must include module, type and func.";
				return $data;
			}

			if($path[0] != '/') {
				$path = '/' . $path;
				$object->properties['path']->setValue($path);
			}

			// Make sure the path is unique
			$mylist = DataObjectMaster::getObjectList(array('name' =>  'path'));
			$filters = array(
								'where' => 'path eq \'' . $path . '\''
							);
		
			$items = $mylist->getItems($filters);
			if(count($items) != 0) {
				$data['msg'] = "The path is not unique.";
				return $data;
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
					$data['msg'] = 'The pathstart is already an alias for a module other than the module in the action';
					return $data;
				}
			}

			$itemid = $object->createItem();

			xarController::Redirect(xarModURL('path','admin','view'));
			return true;
		
		} else {
			// Invalid...
			return xarTplModule('content','admin','new', $data);
		}

	} else {
		return $data;
	}

}

?>