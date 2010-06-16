<?php
/**
 * Modify an item
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
 * modify an item
 *
 * This function shows a form in which the user can modify the item
 *
 * @param id itemid The id of the dynamic data item to modify
 */
function path_admin_modify()
{
    if(!xarVarFetch('itemid',       'id',    $data['itemid'],   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

    // Check if we still have no id of the item to modify.
    if (empty($data['itemid'])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'path');
        throw new Exception($msg);
    }

    // Check if the user can Edit in the path module, and then specifically for this item.
    // We pass the itemid to the SecurityCheck
    if (!xarSecurityCheck('EditPath',1,'Item',$data['itemid'])) return;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object we'll be working with
    $object = DataObjectMaster::getObject(array('name' => 'path'));
	$data['object'] = $object;

	$data['label'] = $object->label;
    
    // Check if we are in 'preview' mode from the input here - the rest is handled by checkInput()
    // Here we are testing for a button clicked, so we test for a string
    if(!xarVarFetch('preview', 'str', $data['preview'],  NULL, XARVAR_DONT_SET)) {return;}

    // Check if we are submitting the form
    // Here we are testing for a hidden field we define as true on the template, so we can use a boolean (true/false)
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        // Get the data from the form
        $isvalid = $object->checkInput();

		$path = $object->properties['path']->getValue();
		$action = $object->properties['action']->getValue();

		$pattern = '/^[\w\-\/]{1,}$/';
		if (!preg_match($pattern, $path)) {
			$data['errors'][] = "Path must be at least one character long and can contain only letters, numbers, slashes, underscores and dashes.";
		}

		if(is_array($action)) {
			foreach($action as $key=>$val){
				$action[$key] = trim($val);
			}
		}

		$data['errors'] = array();

		if (empty($action['module']) || empty($action['func'])) {
			$data['errors'][] = "Action keys must include module and func.";
		} else {
			$object->properties['action']->setValue($action);
		}

		if($path[0] == '/') {
			$path = substr($path, 1);
		}

		// Get the current value of the path before we modify anything
		$object2 = DataObjectMaster::getObject(array('name' => 'path'));
		$curritem = $object2->getItem(array('itemid' => $data['itemid']));
		$currpath = $object2->properties['path']->value;

		// If we're changing the path, make sure the new one is unique
		if ($path != $currpath) {
			$checkpath = xarMod::apiFunc('path','admin','checkpath',array('path' => $path));
			if(!($checkpath)) {
				$data['errors'][] = "The path you've specified is already in use.  Please try again.";
			} else {
				$object->properties['path']->setValue($path);
			}
		}

		// Make sure we're not creating a module alias conflict
		$aliascheck = xarMod::apiFunc('path','admin','alias',array('path' => $path, 'actionmodule' => $action['module']));

		if(is_array($aliascheck)) {
			$data['errors'][] = 'Sorry, that pathstart ("' . $aliascheck['pathstart'] . '") is already an alias for the <a href="' . xarmodurl('modules','admin','aliases', array('name' => $aliascheck['aliasmodule'])) . '">' . $aliascheck['aliasmodule'] . '</a> module.  Please try a different path or specify a different module for the action.';
		}

		if(!empty($data['errors'])) {
			return $data;
		}

		// Good data: create the item
		$item = $object->updateItem();

		// Jump to the next page
		xarResponse::redirect(xarModURL('path','admin','view'));
		return true;
        
    } else {
        // ?? Get that specific item of the object
        $object->getItem(array('itemid' => $data['itemid']));
    }

    // Return the template variables defined in this function
    return $data;
}

?>