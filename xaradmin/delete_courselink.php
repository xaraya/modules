<?php
/**
 * Standard function to Delete and item
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Standard function to Delete an item
 *
 * This is a standard function that is called whenever an administrator
 * wishes to delete a current module item.  Note that this function is
 * the equivalent of both of the modify() and update() functions above as
 * it both creates a form and processes its output.  This is fine for
 * simpler functions, but for more complex operations such as creation and
 * modification it is generally easier to separate them into separate
 * functions.  There is no requirement in the Xaraya MDG to do one or the
 * other, so either or both can be used as seen appropriate by the module
 * developer
 *
 * @author ITSP Module Development Team
 * @param  int courselinkid the id of the linked course to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function itsp_admin_delete_courselink($args)
{
    /* Admin functions of this type can be called by other modules.
     */
    extract($args);

    if (!xarVarFetch('courselinkid',     'id', $courselinkid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itspid',  'id', $itspid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemid',  'id', $pitemid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('authid',  'str::', $authid, '', XARVAR_NOT_REQUIRED)) return;
    /* At this stage we check to see if we have been passed $objectid
     */
    if (!empty($objectid)) {
        $courselinkid = $objectid;
    }
    /* The user API function is called.  This takes the item ID which we
     * obtained from the input and gets us the information on the appropriate
     * item.  If the item does not exist we post an appropriate message and
     * return
     */
    $item = xarModAPIFunc('itsp',
        'user',
        'get_courselink',
        array('courselinkid' => $courselinkid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Security check
    if (!xarSecurityCheck('DeleteITSP', 1, 'Item', "$item[name]:All:$exid")) {
        return;
    }*/
    /*
     * Confirm authorisation code.
     */
    if (!xarSecConfirmAuthKey('itsp')) return;
    /* The API function is called.  Note that the name of the API function and
     * the name of this function are identical, this helps a lot when
     * programming more complex modules.  The arguments to the function are
     * passed in as their own arguments array.
     */

    /* The return value of the function is checked here, and if the function
     * suceeded then an appropriate message is posted.  Note that if the
     * function did not succeed then the API function should have already
     * posted a failure message so no action is required
     */
    if (!xarModAPIFunc('itsp',
            'admin',
            'delete_courselink',
            array('courselinkid' => $courselinkid))) {
        return; // throw back
    }
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    if (!empty($pitemid) && !empty($itspid)) {
        xarResponseRedirect(xarModURL('itsp', 'user', 'modify', array('itspid' => $itspid, 'pitemid' => $pitemid)));
    } else {
        xarResponseRedirect(xarModURL('itsp', 'admin', 'view'));
    }

    /* Return */
    return true;
}
?>