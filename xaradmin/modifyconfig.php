<?php
/** 
 * Standard function to modify configuration parameters
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 */

/**
 * This is a standard function to modify the configuration parameters of the
 * module courses
 * @author Courses module development team  
 * @author MichelV michelv@xarayahosting.nl
 */
function courses_admin_modifyconfig()
{
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Security check
    if (!xarSecurityCheck('AdminCourses')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    // Specify some labels and values for display
    $data['hideemptyfieldslabel']   = xarVarPrepForDisplay(xarML('Hide Empty Fields for User?'));
    $data['hideemptyfieldschecked'] = xarModGetVar('courses', 'HideEmptyFields') ? 'checked="checked"' : '';
    $data['hideplanningmsg']        = xarModGetVar('courses', 'hideplanningmsg');
    $data['hideplanningmsg_label']  = xarVarPrepForDisplay(xarML('Message to show when a planned course is selected for hiding'));
    $data['hidecoursemsg']          = xarModGetVar('courses', 'hidecoursemsg');
    $data['hidecoursemsg_label']    = xarVarPrepForDisplay(xarML('Message to show when a complete course is selected for hiding'));
    $data['itemslabel']             = xarVarPrepForDisplay(xarML('Courses Items Per Page?'));
    $data['itemsvalue']             = xarModGetVar('courses', 'itemsperpage');
    $data['ShowShortDesclabel']     = xarVarPrepForDisplay(xarML('Show short description on user view page?'));
    $data['ShowShortDescchecked']   = xarModGetVar('courses', 'ShowShortDesc') ? 'checked="checked"' : '';
    $data['updatebutton']           = xarVarPrepForDisplay(xarML('Update Configuration'));
    $data['AlwaysNotify']           = xarModGetVar('courses', 'AlwaysNotify');
    $data['AlwaysNotify_label']     = xarVarPrepForDisplay(xarML('E-mail address that will always be sent a copy of an enrollment'));
    // Note : if you don't plan on providing encode/decode functions for
    // short URLs (see xaruserapi.php), you should remove these from your
    // admin-modifyconfig.xard template !
    $data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModGetVar('courses', 'SupportShortURLs') ? 'checked="checked"' : '';

    $hooks = xarModCallHooks('module', 'modifyconfig', 'courses',
        array('module' => 'courses'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    // Return the template variables defined in this function
    return $data;
}

?>
