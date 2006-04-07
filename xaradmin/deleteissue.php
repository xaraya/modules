<?php
/**
* Delete an issue
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * delete issue
 *
 * @param  $ 'id' the id of the item to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function ebulletin_admin_deleteissue($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'int:1:', $id)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // get issue
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('DeleteeBulletin', 1, 'Publication', "$issue[pubname]:$issue[id]")) return;

    // Check for confirmation.
    if (empty($confirm)) {

        // set template vars
        $data = array();
        $data['id']     = $id;
        $data['issue']  = $issue;
        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }

    // security check
    if (!xarSecConfirmAuthKey()) return;

    // call API function to do the deleting
    if (!xarModAPIFunc('ebulletin', 'admin', 'deleteissue', array('id' => $id))) return;

    // set status message and return to view
    xarSessionSetVar('statusmsg', xarML('Issue successfully deleted!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'viewissues'));

    // success
    return true;
}

?>
