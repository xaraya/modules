<?php
/**
 * File: $Id:
 * 
 * Standard function to create a new module item
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
 * add new item
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 */
function courses_admin_new($args)
{
    // Admin functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *before* we have obtained any form-based input through
    // xarVarFetch().
    extract($args);

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch(), xarVarCleanFromInput()
    // is a degraded function.  xarVarFetch allows the checking of the input
    // variables as well as setting default values if needed.  Getting vars
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    if (!xarVarFetch('number', 'str:1:', $number, $number,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('hours', 'str:1:', $hours, $hours, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('ceu', 'str:1:', $ceu, $ceu, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('startdate', 'str:1:', $startdate, $startdate, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('enddate', 'str:1:', $enddate, $enddate, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('shortdesc', 'str:1:', $shortdesc, $shortdesc, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('longdesc', 'str:1:', $longdesc, $longdesc, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AddCourses')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;
    // Specify some labels for display
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Course Name:'));
    $data['numberlabel'] = xarVarPrepForDisplay(xarML('Course Number:'));
	$data['hourslabel'] = xarVarPrepForDisplay(xarML('Course Hours:'));
	$data['ceulabel'] = xarVarPrepForDisplay(xarML('Course Credit Hours:'));
	$data['startdatelabel'] = xarVarPrepForDisplay(xarML('Course Start Date:'));
	$data['enddatelabel'] = xarVarPrepForDisplay(xarML('Course End Date:'));
	$data['shortdesclabel'] = xarVarPrepForDisplay(xarML('Short Course Description:'));
	$data['longdesclabel'] = xarVarPrepForDisplay(xarML('Course Description:'));
    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add Course'));

    $item = array();
    $item['module'] = 'courses';
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    // For E_ALL purposes, we need to check to make sure the vars are set.
    // If they are not set, then we need to set them empty to surpress errors
    if (empty($name)) {
        $data['name'] = '';
    } else {
        $data['name'] = $name;
    }

    if (empty($number)) {
        $data['number'] = '';
    } else {
        $data['number'] = $number;
    }

	 if (empty($hours)) {
        $data['hours'] = '';
    } else {
        $data['hours'] = $hours;
    }

	 if (empty($ceu)) {
        $data['ceu'] = '';
    } else {
        $data['ceu'] = $ceu;
    }

	 if (empty($startdate)) {
        $data['startdate'] = '';
    } else {
        $data['startdate'] = $startdate;
    }

	 if (empty($enddate)) {
        $data['enddate'] = '';
    } else {
        $data['enddate'] = $enddate;
    }

	 if (empty($shortdesc)) {
        $data['shortdesc'] = '';
    } else {
        $data['shortdesc'] = $shortdesc;
    }

	if (empty($longdesc)) {
        $data['longdesc'] = '';
    } else {
        $data['longdesc'] = $longdesc;
    }

	// Return the template variables defined in this function
    return $data;
}

?>
