<?php
/**
 * File: $Id:
 * 
 * Update configuration parameters of the module with information passed back by the modification form
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team 
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function courses_admin_updateconfig()
{
    if (!xarVarFetch('HideEmptyFields', 'checkbox', $HideEmptyFields, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage', 'str:1:', $itemsperpage, '10', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hidecoursemsg', 'str::', $hidecoursemsg, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hideplanningmsg', 'str::', $hideplanningmsg, '', XARVAR_NOT_REQUIRED)) return;
    
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // Update module variables.
    xarModSetVar('courses', 'HideEmptyFields', $HideEmptyFields);
    xarModSetVar('courses', 'itemsperpage', $itemsperpage);
    xarModSetVar('courses', 'SupportShortURLs', $shorturls);
    xarModSetVar('courses', 'hidecoursemsg', $hidecoursemsg);
    xarModSetVar('courses', 'hideplanningmsg', $hideplanningmsg);
    xarModCallHooks('module','updateconfig','courses',
                   array('module' => 'courses'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('courses', 'admin', 'modifyconfig'));
    // Return
    return true;
}

?>
