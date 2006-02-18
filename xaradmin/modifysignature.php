<?php
/*
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Modify a Newsletter owner's signature
 *
 * @public
 * @author Richard Cave
 * @returns array
 * @return $templateVarArray
 */
function newsletter_admin_modifysignature()
{

    if (!xarVarFetch('func', 'str', $page,  'main', XARVAR_NOT_REQUIRED)) return;

    // Security check
    if(!xarSecurityCheck('EditNewsletter')) return;

    // Check to see if this user is a issue owner
    $userId = xarSessionGetVar('uid');

    // The user API function is called
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getowner',
                          array('id' => $userId));

    if($item) {
        // Set hook variables
        $item['module'] = 'newsletter';
        $hooks = xarModCallHooks('item','modify',$userId,$item);
        if (empty($hooks) || !is_string($hooks)) {
            $hooks = '';
        }

        // Get the admin edit menu
        $menu = xarModApiFunc('newsletter', 'admin', 'editmenu');

        // Set the template variables defined in this function
        $templateVarArray = array('authid' => xarSecGenAuthKey(),
         //   'updatebutton' => xarVarPrepForDisplay(xarML('Update Signature')),
         'page' => $page,
            'menu' => $menu,
            'hooks' => $hooks,
            'item' => $item);
    } else {
        $errorMsg = "You cannot edit another owner's signature.";
        $templateVarArray = array('errorMsg' => xarVarPrepForDisplay(xarML($errorMsg)),
                                  'modifylabel' =>  xarVarPrepForDisplay(xarML('Modify Owner')));
    }

    // Return the template variables defined in this function
    return $templateVarArray;
}

?>
