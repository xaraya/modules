<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * Update a Newsletter owner's signature
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the owner to be updated
 * @param 'signature' the signature of the owner
 * @return bool true on success, false on failure
 */
function newsletter_admin_updatesignature()
{
    // Confirm authorization code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) item #(2) in function #(3)',
                    'Newsletter', xarVarPrepForDisplay($id), 'newsletter_admin_updatesignature');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('id', 'id', $id)) return;
    if (!xarVarFetch('signature', 'str:1:', $signature, '')) return;

    // Call API function
    if(!xarModAPIFunc('newsletter',
                      'admin',
                      'updatesignature',
                      array('id' => $id,
                            'signature' => $signature))) {
        return; // throw back
    }
    xarSessionSetVar('statusmsg', xarML('Newsletter Owner Update'));

    // Redirect to welcome screen
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'modifysignature'));

    // Return
    return true;
}

?>
