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
 *  Modify an item
 */
function shop_admin_modify()
{
    if(!xarVarFetch('itemid',       'id',    $data['itemid'],   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('name',    'str',   $name, false,       XARVAR_NOT_REQUIRED)) return;

    // Check if we still have no id of the item to modify.
    if (empty($data['itemid'])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'shop');
        throw new Exception($msg);
    }

    // Check if the user can Edit in the shop module, and then specifically for this item.
    // We pass the itemid to the SecurityCheck
    if (!xarSecurityCheck('AdminShop',1,'Item',$data['itemid'])) return;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object we'll be working with
	
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));

	$data['label'] = $data['object']->label;
 
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
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with the data we picked up and with error messages
            return xarTplModule('shop','admin','modify', $data);        
        } elseif (isset($data['preview'])) {
            // Show a preview, same thing as the above essentially
            return xarTplModule('shop','admin','modify', $data);        
        } else {
            // Good data
            $item = $data['object']->updateItem();

            // Jump to the next page

			$func = str_replace('shop_','',$name);
            xarResponse::Redirect(xarModURL('shop','admin',$func));
            return true;
        }
    } else {
        // Get that specific item of the object
        $data['object']->getItem(array('itemid' => $data['itemid']));
    }

	$data['name'] = $name;

    // Return the template variables defined in this function
    return $data;
}

?>