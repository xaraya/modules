<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * delete item
 * @param 'hid' the id of the item to be deleted
 * @param 'confirmation' confirmation that this item can be deleted
 */
function headlines_admin_delete()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('hid','int:1:',$hid)) return;
    if (!xarVarFetch('obid','str:1:',$obid,$hid,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;

    // Security Check
    if(!xarSecurityCheck('DeleteHeadlines')) return;

    // The user API function is called
    $link = xarMod::apiFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if ($link == false) return;

    // Check for confirmation.
    if (empty($confirm)) {
    $link['submitlabel'] = xarML('Submit');
    $link['authid'] = xarSecGenAuthKey();

    return $link;

    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // The API function is called
    if (!xarMod::apiFunc('headlines',
                       'admin',
                       'delete',
                       array('hid' => $hid))) return;

    xarController::redirect(xarModURL('headlines', 'admin', 'view'));

    // Return
    return true;
}
?>
