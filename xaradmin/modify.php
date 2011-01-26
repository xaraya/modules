<?php
/**
 * Modify an item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * modify an item
 *
 * This function shows a form in which the user can modify the item
 *
 * @param id itemid The id of the dynamic data item to modify
 */
function content_admin_modify()
{
    if(!xarVarFetch('itemid',       'id',    $itemid,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;
	//if (!xarVarFetch('path_error',    'str',   $data['path_error'], NULL,       XARVAR_NOT_REQUIRED)) return;
	//if (!xarVarFetch('pathaliasmodule',    'str',   $data['pathaliasmodule'], NULL,       XARVAR_NOT_REQUIRED)) return;
	//if (!xarVarFetch('pathstart',    'str',   $data['pathstart'], NULL,       XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('view',    'str',   $data['view'], '',       XARVAR_NOT_REQUIRED)) return;

    // Check if we still have no id of the item to modify.
    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'content');
        throw new Exception($msg);
    }

	$data['itemid'] = $itemid;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	// Get the object name
	$contentobject = DataObjectMaster::getObject(array('name' => 'content'));
	$check = $contentobject->getItem(array('itemid' => $itemid));
	$ctype = $contentobject->properties['content_type']->getValue();
	if (empty($check)) { 
		$msg = 'There is no content item with an itemid of ' . $itemid;
		return xarTplModule('base','message','notfound',array('msg' => $msg));
	}

	$instance = $itemid.':'.$ctype.':'.xarUserGetVar('id');
	if (!xarSecurityCheck('EditContent',0,'Item',$instance)) {
		return;
	}
	
	$data['ctype'] = $ctype;
	$data['pathval'] = '';

    // Get the object we'll be working with
    $object = DataObjectMaster::getObject(array('name' => $ctype));
	$data['object'] = $object; // save for later

	// TODO: check if path module is installed
	/*if (xarModIsAvailable('path') && xarModVars::get('content','path_module') && isset($object->properties['path_module'])) {
		$dopath = true;
	} else {
		$dopath = false;
	}*/

	/*if ($dopath) {
		// Get the field value from the path module, in case a change was made there
		$pathval = xarMod::apiFunc('path','user','action2path',array(
				'action' => array('module' => 'content', 'itemid' => $itemid)
				));
		if ($pathval) {
			if($pathval[0] != '/') {
				$pathval = '/' . $pathval;
			}
			$data['pathval'] = $pathval;
		}
	}*/

	$data['label'] = $object->label;
   
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        // Get the data from the form
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            return xarTplModule('content','admin','modify', $data);        
        } elseif (isset($data['preview'])) {
            // Show a preview, same thing as the above essentially
            return xarTplModule('content','admin','modify', $data);        
        } else {
            // Good data: update the item
			/*if ($dopath) {
				$path = $object->properties['path_module']->getValue();
				if ($path != $pathval) { // The path was edited
					//If we've changed the path and the new path is not a dupe
					$action['module'] = 'content';
					$action['itemid'] = $itemid;
					$pargs = array(
						'path' => $path, 
						'action' => $action,
						'currpath' => $pathval
						);

					$pathinfo = xarMod::apiFunc('path','user','set',$pargs);

					if (isset($pathinfo['errors'])) {
						$path_errors = $pathinfo['errors'];
						if (isset($path_errors[5])) {
							$args['pathstart'] = $path_errors[5];
						}
						if (isset($path_errors[6])) {
							$vals = explode('|',$path_errors[6]);
							$args['pathstart'] = $vals[0];
							$args['pathaliasmodule'] = $vals[1];  
						}
						$path_errors = array_keys($path_errors);
						$path_error = reset($path_errors); //There will never be multiple errors
						if (!$pathval) {
							$pathval = '';
						}
						$data['object']->properties['path_module']->setValue($pathval);
					} else { 
						$val = $pathinfo['path'];
						$data['object']->properties['path_module']->setValue($val);
					}
				}
			}*/
			
			$properties = array_keys($data['object']->getProperties());

			if (in_array('path', $properties)) {
				$path = $data['object']->properties['path']->getValue(); 
				$contentobject = DataObjectMaster::getObject(array('name' => 'content'));
				$contentobject->getItem(array('itemid' => $itemid));
				$contentobject->properties['path']->setValue($path);
				$contentobject->updateItem(array('itemid' => $itemid));			
			}

			// never an empty publication_date
			
			if (in_array('publication_date', $properties)) {
				$pubdate = $data['object']->properties['publication_date']->getValue();
				if ($pubdate == -1) { 
					$previous = $data['object'];
					$previous->getItem(array('itemid' => $itemid));
					$pubdate = $previous->properties['publication_date']->value;
					$data['object']->properties['publication_date']->setValue($pubdate);
				}
			}
			if (in_array('date_modified', $properties)) {
				$data['object']->properties['date_modified']->setValue(time());
			}

            $data['object']->updateItem(array('itemid' => $itemid));

			/*if (isset($path_error)) {
				$args['itemid'] = $itemid;
				$args['path_error'] = $path_error;
				xarResponse::redirect(xarModURL('content','admin','modify', $args));
				return true;
			}*/

			if (!empty($data['view'])) {
				if (xarModAlias::resolve($ctype) == 'content') {
					xarResponse::redirect(xarModURL('content','user','display', array('itemid'=>$itemid, 'ctype' => $ctype)));
				} else { 
					xarResponse::redirect(xarModURL('content','user','display', array('itemid'=>$itemid)));
				}
			} else {
				xarResponse::redirect(xarModURL('content','admin','modify', array('itemid'=>$itemid)));
			}
            return true;
        }
    } else {
        $data['object']->getItem(array('itemid' => $itemid));
    }

    return $data;
}

?>