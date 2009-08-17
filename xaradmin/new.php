<?php
/**
 * Add a new item
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
 * Create a new item of the dyn_example object
 */
function dyn_example_admin_new()
{
    // See if the current user has the privilege to add an item. We cannot pass any extra arguments here
    if (!xarSecurityCheck('AddDynExample')) return;

    $data['object'] = DataObjectMaster::getObject(array('name' => 'dyn_example'));

    // Check if we are in 'preview' mode from the input here - the rest is handled by checkInput()
    if(!xarVarFetch('preview', 'str', $data['preview'],  NULL, XARVAR_DONT_SET)) {return;}

    // Check if we are submitting the form
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;

        // Get the data from the form
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('dyn_example','admin','new', $data);        
        } elseif ($data['preview']) {
            // Show a preview, same thing as the above essentially
            return xarTplModule('dyn_example','admin','new', $data);        
        } else {
            // Good data: create the item
            $item = $data['object']->createItem();

            // Jump to the next page
            xarResponse::Redirect(xarModURL('dyn_example','admin','view'));
            return true;
        }
    }

    // Return the template variables defined in this function
    return $data;
}

?>