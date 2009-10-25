<?php
/**
 * Modify an item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * modify an item
 *
 * This function shows a form in which the user can modify the item
 *
 * @param id itemid The id of the dynamic data item to modify
 */
function dyn_example_admin_modify()
{
    if(!xarVarFetch('itemid',       'id',    $tdata['itemid'],   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('name',       'str',    $name,            'dyn_example', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'bool',   $tdata['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

    // Check if we still have no id of the item to modify.
    if (empty($tdata['itemid'])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'dyn_example');
        throw new Exception($msg);
    }

    // Check if the user can Edit in the dyn_example module, and then specifically for this item.
    // We pass the itemid to the SecurityCheck
    if (!xarSecurityCheck('EditDynExample',1,'Item',$tdata['itemid'])) return;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object we'll be working with
    $tdata['object'] = DataObjectMaster::getObject(array('name' => $name));
    
    // Check if we are in 'preview' mode from the input here - the rest is handled by checkInput()
    // Here we are testing for a button clicked, so we test for a string
    if(!xarVarFetch('preview', 'str', $tdata['preview'],  NULL, XARVAR_DONT_SET)) {return;}

    // Check if we are submitting the form
    // Here we are testing for a hidden field we define as true on the template, so we can use a boolean (true/false)
    if (!xarVarFetch('confirm',    'bool',   $tdata['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($tdata['confirm']) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        // Get the data from the form
        $isvalid = $tdata['object']->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with the data we picked up and with error messages
            return xarTplModule('dyn_example','admin','modify', $tdata);        
        } elseif (isset($tdata['preview'])) {
            // Show a preview, same thing as the above essentially
            return xarTplModule('dyn_example','admin','modify', $tdata);        
        } else {
            // Good data: create the item
            $item = $tdata['object']->updateItem();

            // Jump to the next page
            xarResponse::Redirect(xarModURL('dyn_example','admin','view'));
            return true;
        }
    } else {
        // Get that specific item of the object
        $tdata['object']->getItem(array('itemid' => $tdata['itemid']));
    }

    // Return the template variables defined in this function
    return $tdata;
}

?>