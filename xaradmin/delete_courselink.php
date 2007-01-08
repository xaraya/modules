<?php
/**
 * Standard function to Delete and item
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
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
 * @param  int courselinkid the id of the linked course to be deleted OR
 * @param  int icourseid the id of the internal course to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function itsp_admin_delete_courselink($args)
{
    /* Admin functions of this type can be called by other modules.
     */
    extract($args);

    if (!xarVarFetch('courselinkid','id', $courselinkid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('icourseid',   'id', $icourseid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itspid',      'id', $itspid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemid',     'id', $pitemid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('authid',      'str::', $authid, '', XARVAR_NOT_REQUIRED)) return;
    /* At this stage we check to see if we have been passed any valid id
     */
    if (empty($courselinkid) && empty($icourseid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'delete_courselink', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!empty($courselinkid) && empty($icourseid)) {

        /* The user API function is called.
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
        /* The API function is called.
         */
        if (!xarModAPIFunc('itsp',
                'admin',
                'delete_courselink',
                array('courselinkid' => $courselinkid))) {
            return; // throw back
        } else {
            xarSessionSetVar('statusmsg', xarML('ITSP Item was successfully deleted!'));
        }
    } elseif (empty($courselinkid) && !empty($icourseid)) {

        /* The user API function is called.
         */
        $item = xarModAPIFunc('itsp',
            'user',
            'get_itspcourse',
            array('icourseid' => $icourseid));
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
        /* The API function is called.
         */
        if (!xarModAPIFunc('itsp',
                'admin',
                'delete_itspcourse',
                array('icourseid' => $icourseid))) {
            return; // throw back
        } else {
            xarSessionSetVar('statusmsg', xarML('ITSP Item was successfully deleted!'));
        }
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