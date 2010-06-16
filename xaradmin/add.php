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
function path_admin_add()
{
    // See if the current user has the privilege to add an item. We cannot pass any extra arguments here
    if (!xarSecurityCheck('AddPath')) return;

	if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

	// Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => 'path'));
	
	$data['object'] = $object;
	$data['label'] = $object->label;

	if ($data['confirm']) {

		// Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        // Get the data from the form
        $isvalid = $object->checkInput();

		$path = $object->properties['path']->getValue();
		$action = $object->properties['action']->getValue();

		if(is_array($action)) {
			foreach($action as $key=>$val){
				$action[$key] = trim($val);
			}
		}

		$data['errors'] = array();

		$pattern = '/^[\w\-\/]{1,}$/';
		if (!preg_match($pattern, $path)) {
			$data['errors'][] = "Path must be at least one character long and can contain only letters, numbers, slashes, underscores and dashes.";
		}

		if (empty($action['module']) || empty($action['func'])) {
			$data['errors'][] = "Action keys must include module and func.";
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

	} else {
		return $data;
	}

}

?>