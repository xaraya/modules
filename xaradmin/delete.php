<?php
/**
 * Censor Module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
 * @author John Cox
 */
/**
 * delete a censored word
 *
 * @param  $ 'cid' the id of the censored word to be deleted
 * @param  $ 'confirmation' confirmation that this item can be deleted
 */
function censor_admin_delete($args)
{
    // Get parameters
    if (!xarVarFetch('cid', 'int:1:', $cid)) return;
    if (!xarVarFetch('obid', 'str:1:', $obid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;

    extract($args);

    if (!empty($obid)) {
        $cid = $obid;
    }

    // The user API function is called
    $censor = xarModAPIFunc('censor',
                            'user',
                            'get',
                             array('cid' => $cid));
    if ($censor == false) return;

    // Security Check
    if (!xarSecurityCheck('DeleteCensor')) return;

    // Check for confirmation.
    if (empty($confirm)) {
        $censor['authid'] = xarSecGenAuthKey();

        return $censor;
    }
    // If we get here it means that the user has confirmed the action
    // Confirm authorisation code

    if (!xarSecConfirmAuthKey()) return;
    // The API function is called
    if (!xarModAPIFunc('censor',
            'admin',
            'delete',
            array('cid' => $cid))) return;
    xarResponseRedirect(xarModURL('censor', 'admin', 'view'));
    // Return
    return true;
}
?>
