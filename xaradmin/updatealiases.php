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
    // security checks
    if (!xarSecurityCheck('EditBible')) return;
    if (!xarSecConfirmAuthKey()) return;

    // get HTTP vars
    if (!xarVarFetch('aliases', 'array', $aliases, array())) return;

    // save via api function
    xarModAPIFunc('bible', 'admin', 'updatealiases', array('aliases' => $aliases));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // set status message and redirect
    xarSessionSetVar('statusmsg', xarML('Aliases successfully updated!'));
    xarResponseRedirect(xarModURL('bible', 'admin', 'aliases'));

    // Return
    return true;
}

?>
