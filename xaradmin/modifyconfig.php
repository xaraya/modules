<?php
/**
 * Standard function to modify configuration parameters
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module courses
 * @author Courses module development team
 * @author MichelV michelv@xarayahosting.nl
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
    $data['hideplanningmsg']        = xarModGetVar('courses', 'hideplanningmsg');
    $data['hidecoursemsg']          = xarModGetVar('courses', 'hidecoursemsg');
    $data['itemsvalue']             = xarModGetVar('courses', 'itemsperpage');
    $data['ShowShortDescchecked']   = xarModGetVar('courses', 'ShowShortDesc') ? 'checked="checked"' : '';
    $data['DefaultTeacherType']     = xarModGetVar('courses','DefaultTeacherType');
    $data['updatebutton']           = xarVarPrepForDisplay(xarML('Update Configuration'));
    $data['AlwaysNotify']           = xarModGetVar('courses', 'AlwaysNotify');
    // Short URL support
    $data['shorturlschecked'] = xarModGetVar('courses', 'SupportShortURLs') ? true : false;
    /* If you plan to use alias names for you module then you should use the next two alias vars
     * You must also use short URLS for aliases, and provide appropriate encode/decode functions.
     */
    $data['useAliasName'] = xarModGetVar('courses', 'useModuleAlias');
    $data['aliasname ']= xarModGetVar('courses','aliasname');
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
