<?php
/**
* Delete a publication
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
 * delete item
 *
 * @param  $ 'id' the id of the item to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function ebulletin_admin_delete($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'int:1:', $id)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // make sure we have an ID
    if (!empty($objectid)) $id = $objectid;

    // get publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $id));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('DeleteeBulletin', 1, 'Publication', "$pub[name]:$id")) return;

    // Check for confirmation.
    if (empty($confirm)) {

        // set template data
        $data = array(
            'id'     => $id,
            'pub'    => &$pub,
            'authid' => xarSecGenAuthKey()
        );

        return $data;
    }

    // security check
    if (!xarSecConfirmAuthKey()) return;

    // call API function to do the deleting
    if (!xarModAPIFunc('ebulletin', 'admin', 'delete', array('id' => $id))) return;

    // set status message and return to view
    xarSessionSetVar('statusmsg', xarML('Publication successfully deleted!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'view'));

    // success
    return true;
}

?>
