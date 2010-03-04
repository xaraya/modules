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
 *  Add a payment option to a customer's account
 */
function shop_admin_newpaymentoption()
{
    // See if the current user has the privilege to add an item. We cannot pass any extra arguments here
    if (!xarSecurityCheck('AddShop')) return;

	if(!xarVarFetch('objectid',       'id',    $data['objectid'],   NULL, XARVAR_DONT_SET)) {return;}
	
	$objectname = 'shop_paymentoptions';
	$data['objectname'] = $objectname;

	// Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => $objectname));
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
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with the data we picked up and with error messages
            return xarTplModule('shop','admin','newpaymentoption', $data);        
        } elseif (isset($data['preview'])) {
            // Show a preview, same thing as the above essentially
            return xarTplModule('shop','admin','newpaymentoption', $data);        
        } else {

			$itemid = $data['object']->createItem();
 
            // Jump to the next page
            xarResponse::Redirect(xarModURL('shop','admin','paymentoptions'));
            // Always add the next line even if processing never reaches it
            return true;
        }
    }

    // Return the template variables defined in this function
    return $data;
}

?>