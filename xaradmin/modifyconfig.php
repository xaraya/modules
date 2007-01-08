<?php
/**
 * Standard function to modify configuration parameters
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module courses
 * @author MichelV <michelv@xarayahosting.nl>
 * @return array
 */
function courses_admin_modifyconfig()
{
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Security check
    if (!xarSecurityCheck('AdminCourses')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    // Specify some labels and values for display
    $data['hideemptyfieldschecked'] = xarModGetVar('courses', 'HideEmptyFields') ? 'checked="checked"' : '';
    $data['itemsvalue']             = xarModGetVar('courses', 'itemsperpage');
    $data['ShowShortDescchecked']   = xarModGetVar('courses', 'ShowShortDesc') ? 'checked="checked"' : '';
    $data['SendConfirmsForExtregchecked']   = xarModGetVar('courses', 'SendConfirmsForExtreg') ? 'checked="checked"' : '';
    $data['DefaultTeacherType']     = xarModGetVar('courses','DefaultTeacherType');
    $data['updatebutton']           = xarVarPrepForDisplay(xarML('Update Configuration'));
    $data['AlwaysNotify']           = xarModGetVar('courses', 'AlwaysNotify');
    $data['coord_group']            = xarModGetVar('courses', 'coord_group');
    // Short URL support
    $data['shorturlschecked'] = xarModGetVar('courses', 'SupportShortURLs') ? true : false;

    // TODO: call hook for each itemtype
    $hooks = xarModCallHooks('module', 'modifyconfig', 'courses',
                       array('module' => 'courses', 'itemtype' => NULL));
    if (empty($hooks)) {
        $data['hookoutput'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for this module'));
    } else {
        $data['hookoutput'] = $hooks;

    }
    // Return the template variables defined in this function
    return $data;
}

?>
