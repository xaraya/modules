<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Create a new customer
 */
function shop_user_newcustomer()
{

	if(!xarVarFetch('objectid',       'id',    $data['objectid'],   NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('returnurl',       'str',    $returnurl,   NULL, XARVAR_NOT_REQUIRED)) {return;}
	
    sys::import('modules.dynamicdata.class.objects.master');

	$rolesobject = DataObjectMaster::getObject(array('name' => 'roles_users'));
	$data['properties'] = $rolesobject->properties;

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
        $isvalid = $rolesobject->properties['email']->checkInput();
		$isvalid2 = $rolesobject->properties['password']->checkInput();

        if (!$isvalid || !$isvalid2) {
            // Bad data: redisplay the form with the data we picked up and with error messages
            return xarTplModule('shop','user','newcustomer', $data);               
        } else {

			$email = $rolesobject->properties['email']->getValue();
			$password = $rolesobject->properties['password']->getValue();

			$rolesobject->properties['name']->setValue($email);
			$rolesobject->properties['email']->setValue($email);
			$rolesobject->properties['uname']->setValue($email);
			$rolesobject->properties['password']->setValue($password);
			$rolesobject->properties['state']->setValue(3);
			$authmodule = (int)xarMod::getID('shop');
			$rolesobject->properties['authmodule']->setValue($authmodule);
			$uid = $rolesobject->createItem();
 
			$custobject = DataObjectMaster::getObject(array('name' => 'shop_customers'));
			$custobject->createItem(array('id' => $uid));
 
            if (isset($returnurl)) {
				xarMod::APIFunc('authsystem','user','login',array('uname' => $email, 'pass' => $password));
				xarResponse::Redirect($returnurl);
			} else {
				xarResponse::Redirect(xarModURL('shop'));
			}

            // Always add the next line even if processing never reaches it
            return true;
        }
    }

    // Return the template variables defined in this function
    return $data;
}

?>