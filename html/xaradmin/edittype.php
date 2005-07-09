<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage HTML Module
 * @author John Cox 
*/

/*
 * Edit an HTML tag
 *
 * @public
 * @author Richard Cave
 * @returns array, or false on failure
 * @raise BAD_PARAM
 */
function html_admin_edittype()
{
    // Security Check
	if(!xarSecurityCheck('EditHTML')) return;

    // Get parameters from input
    if (!xarVarFetch('id', 'int:0:', $id)) return;
    if (!xarVarFetch('tagtype', 'str:1:', $tagtype, '')) return;
    if (!xarVarFetch('confirm', 'int:0:1', $confirm, 0)) return;

    // Get the current html tag 
    $type = xarModAPIFunc('html',
                          'user',
                          'gettype',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($type) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Check for confirmation.
    if (!$confirm) {

        // Specify for which html tag you want confirmation
        $data['id'] = $id;

        // Data to display in the template
        $data['type'] = xarVarPrepForDisplay($type['type']);
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
                    'HTML', xarVarPrepForDisplay($id));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    // Modify the html tag
    if (!xarModAPIFunc('html',
                       'admin',
                       'edittype',
                       array('id' => $id,
                             'tagtype' => $tagtype))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('HTML Tag Updated'));

    // Redirect
    xarResponseRedirect(xarModURL('html', 'admin', 'viewtypes'));

    // Return
    return true;
}

?>
