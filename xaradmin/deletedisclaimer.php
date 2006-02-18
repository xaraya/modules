<?php
/**
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
 * Delete an Newsletter disclaimer
 *
 * @public
 * @author Richard Cave
 * @param int 'id' the id of the item to be deleted
 * @param string 'confirm' confirm that this item can be deleted
 * @return array $data
 */
function newsletter_admin_deletedisclaimer($args)
{
    // Extract args
    extract ($args);

    // Security check
    if(!xarSecurityCheck('DeleteNewsletter')) return;

    // Get parameters from input
    if (!xarVarFetch('id', 'id', $id, 0)) return;
    if (!xarVarFetch('confirm', 'int:0:1', $confirm, 0)) return;

    // The user API function is called
    $item = xarModAPIFunc('newsletter',
                         'user',
                         'getdisclaimer',
                         array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Check for confirmation.
    if (!$confirm) {

        // Get the admin menu
      //  $data = xarModAPIFunc('newsletter', 'admin', 'menu');

        // Specify for which item you want confirmation
        $data['id'] = $id;
      //   $data['confirmbutton'] = xarML('Confirm');

        // Data to display in the template
        $data['namevalue'] = xarVarPrepForDisplay($item['title']);

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for deleting #(1) item #(2)',
                    'Newsletter', xarVarPrepForDisplay($id));
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // The API function is called
    if (!xarModAPIFunc('newsletter',
                       'admin',
                       'deletedisclaimer',
                       array('id' => $id))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('Item Deleted'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewdisclaimer'));
}

?>
