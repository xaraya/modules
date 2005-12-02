<?php
/**
 * File: $Id:
 * 
 * Update book aliases
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
function bible_admin_updatealiases()
{
    if (!xarVarFetch('aliases', 'array', $aliases, array())) return;

    // confirm auth code
    if (!xarSecConfirmAuthKey()) return;

    // security check
    if (!xarSecurityCheck('EditBible')) {
        return;
    }

    // save via api function
    xarModAPIFunc('bible', 'admin', 'updatealiases', array('aliases' => $aliases));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('bible', 'admin', 'aliases'));

    // Return
    return true;
}

?>
