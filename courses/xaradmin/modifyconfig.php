<?php
/**
 * File: $Id:
 * 
 * Standard function to modify configuration parameters
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Example module development team 
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function courses_admin_modifyconfig()
{
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminCourses')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    // Specify some labels and values for display
    $data['boldlabel'] = xarVarPrepForDisplay(xarML('Display Course Items In Bold?'));
    $data['boldchecked'] = xarModGetVar('courses', 'bold') ? 'checked' : '';
    $data['itemslabel'] = xarVarPrepForDisplay(xarML('Courses Items Per Page?'));
    $data['itemsvalue'] = xarModGetVar('courses', 'itemsperpage');
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));
    // Note : if you don't plan on providing encode/decode functions for
    // short URLs (see xaruserapi.php), you should remove these from your
    // admin-modifyconfig.xard template !
    $data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModGetVar('courses', 'SupportShortURLs') ?
    'checked' : '';

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
