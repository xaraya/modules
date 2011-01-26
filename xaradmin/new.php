<?php
/**
 * Add a new item
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
 * Create a new content item 
 */
function content_admin_new()
{
    // See if the current user has the privilege to add an item. We cannot pass any extra arguments here
    if (!xarSecurityCheck('AddContent')) return;

	if(!xarVarFetch('objectid',       'id',    $objectid,   NULL, XARVAR_DONT_SET)) {return;}
	//if(!xarVarFetch('path',       'str',    $path,   NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('ctype',    'str', $ctype,     NULL,  XARVAR_GET_ONLY)) {return;}
	
	$data['content_type'] = $ctype;

	$instance = 'All:'.$ctype.':'.xarUserGetVar('id');
	if (!xarSecurityCheck('AddContent',0,'Item',$instance)) {
		return;
	}

	$data['pathval'] = '';

	// Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => $ctype));
	$objectid = $object->objectid;
	$data['label'] = $object->label;
	$data['object'] = $object;

    // Check if we are in 'preview' mode from the input here - the rest is handled by checkInput()
    // Here we are testing for a button clicked, so we test for a string
    if(!xarVarFetch('preview', 'str', $data['preview'],  NULL, XARVAR_DONT_SET)) {return;}

    // Check if we are submitting the form
    // Here we are testing for a hidden field we define as true on the template, so we can use a boolean (true/false)
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($data['confirm']) {

        // Check for a valid confirmation key. The value is automatically gotten from the template
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        // Get the data from the form and see if it is all valid
        // Either way the values are now stored in the object
	
        $isvalid = $object->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with the data we picked up and with error messages
            return xarTplModule('content','admin','new', $data);        
        } elseif (isset($data['preview'])) {
            // Show a preview, same thing as the above essentially
            return xarTplModule('content','admin','new', $data);        
        } else {

			if (isset($object->properties['path'])) {
				$path = $object->properties['path']->getValue();
			} else {
				$path = '';
			}

			// Create the item for the content object
			$contentobject = DataObjectMaster::getObject(array('name' => 'content'));
			$contentobject->properties['content_type']->setValue($ctype);
			$contentobject->properties['path']->setValue($path);
			$itemid = $contentobject->createItem();
			
			/*if (xarModVars::get('content','path_module') && isset($object->properties['path_module'])) {
				$path = $object->properties['path_module']->getValue();
				$action['module'] = 'content';
				$action['itemid'] = $itemid;
				$pathinfo = xarMod::apiFunc('path','user','set',array(
					'path' => $path,
					'action' => $action
					));
				if (isset($pathinfo['errors'])) {
					// Don't save a bad path; save an empty string instead
					$object->properties['path_module']->setValue(''); 
					// The bad path should not interrupt creation of the item.  Thus processing continues...
				} else {
					$object->properties['path_module']->setValue($pathinfo['path']);
				}
			}*/

			if (isset($object->properties['publication_date'])) {
				$pubdate = $object->properties['publication_date']->getValue();
				if ($pubdate == -1) {
					$object->properties['publication_date']->setValue(time());
				}
			}
			if (isset($object->properties['date_created'])) {
				$data['object']->properties['date_created']->setValue(time());
			}
			if (isset($object->properties['date_modified'])) {
				$data['object']->properties['date_modified']->setValue(time());
			}

			// Same itemid for this
			$itemid = $object->createItem(array('itemid' => $itemid));

			/*if (isset($pathinfo['errors'])) {
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
				$args['ctype'] = $ctype;
				$args['itemid'] = $itemid;
				$args['path_error'] = $path_error;
				xarResponse::redirect(xarModURL('content','admin','modify', $args));
			}*/
 
            // Jump to the next page
            xarResponse::redirect(xarModURL('content','admin','view', array('ctype' => $ctype)));
            return true;
        }
    }

    // Return the template variables defined in this function
    return $data;
}

?>