<?php
/**
 * Xaraya HTML Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage HTML Module
 * @link http://xaraya.com/index.php/release/779.html
 * @author John Cox
 */
/**
 * Edit an HTML tag
 *
 * @public
 * @author Richard Cave
 * @return array, or false on failure
 * @throws BAD_PARAM
 */
function html_admin_edit()
{
    // Security Check
    if(!xarSecurityCheck('EditHTML')) return;

    // Get parameters from input
    if (!xarVarFetch('cid', 'int:0:', $cid)) return;
    if (!xarVarFetch('tag', 'str:1:', $tag, '')) return;
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
        $data['allowed'] = $html['allowed'];
        $data['editbutton'] = xarML('Submit');

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for editing #(1) HTML tag #(2)',
                    'HTML', xarVarPrepForDisplay($cid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    // Modify the html tag
    if (!xarModAPIFunc('html',
                       'admin',
                       'edit',
                       array('cid' => $cid,
                             'tag' => $tag))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('HTML Tag Updated'));

    // Redirect
    xarResponseRedirect(xarModURL('html', 'admin', 'set'));

    // Return
    return true;
}

?>
