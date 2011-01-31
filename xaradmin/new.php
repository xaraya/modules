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
	if(!xarVarFetch('ctype',    'str', $ctype,     NULL,  XARVAR_GET_ONLY)) {return;}
	
	$data['content_type'] = $ctype;

	$instance = 'All:'.$ctype.':'.xarUserGetVar('id');
	if (!xarSecurityCheck('AddContent',0,'Item',$instance)) {
		return;
	}

	//$data['pathval'] = '';

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

			if (isset($object->properties['item_path'])) {
				$path = $object->properties['item_path']->getValue();
			} else {
				$path = '';
			}

			// Create the item for the content object
			$contentobject = DataObjectMaster::getObject(array('name' => 'content'));
			$contentobject->properties['content_type']->setValue($ctype);
			$contentobject->properties['item_path']->setValue($path);
			$itemid = $contentobject->createItem();

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
 
            // Jump to the next page
            xarResponse::redirect(xarModURL('content','admin','view', array('ctype' => $ctype)));
            return true;
        }
    }

    // Return the template variables defined in this function
    return $data;
}

?>