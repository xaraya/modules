<?php
/**
 * Xaraya HTML Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage HTML Module
 * @link http://xaraya.com/index.php/release/779.html
 * @author John Cox
 */
/**
 * Delete a tag
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @return mixed array, or false on failure
 * @throws BAD_PARAM
 */
function html_admin_delete()
{
    // Security Check
    if(!xarSecurityCheck('DeleteHTML')) return;

    // Get parameters from input
    if (!xarVarFetch('cid', 'int:0:', $cid)) return;
    if (!xarVarFetch('confirm', 'int:0:1', $confirm, 0)) return;

    // Get the current html tag
    $html = xarModAPIFunc('html',
                          'user',
                          'gettag',
                          array('cid' => $cid));

    // Check for exceptions
    if (!isset($html) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Check for confirmation.
    if (!$confirm) {

        // Specify for which html tag you want confirmation
        $data['cid'] = $cid;

        // Data to display in the template
        $data['tag'] = xarVarPrepForDisplay($html['tag']);
        $data['submitlabel'] = xarML('Confirm');

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for deleting #(1) HTML tag #(2)',
                    'HTML', xarVarPrepForDisplay($cid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    // Remove the html tag
    if (!xarModAPIFunc('html',
                       'admin',
                       'delete',
                       array('cid' => $cid))) {
        return; // throw back
    }

    xarSession::setVar('statusmsg', xarML('HTML Tag Deleted'));

    // Redirect
    xarResponse::Redirect(xarModURL('html', 'admin', 'set'));

    // Return
    return true;
}

?>
