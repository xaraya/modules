<?php
/**
 * File: $Id:
 * 
 * Standard function to modify an item
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
 * modify an item
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 * 
 * @param  $ 'exid' the id of the item to be modified
 */
function courses_admin_modify($args)
{

    // Admin functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *before* we have obtained any form-based input through
    // xarVarFetch(), so that parameters passed by the modules can also be
    // checked by a certain validation.
    extract($args);
    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch(), xarVarCleanFromInput()
    // is a degraded function.  xarVarFetch allows the checking of the input
    // variables as well as setting default values if needed.  Getting vars
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    if (!xarVarFetch('courseid', 'isset:', $courseid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number', 'int:1:', $number, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours', 'int:1:', $hours, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('ceu', 'int:1:', $ceu, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('startdate', 'isset:', $startdate, NULL, XARVAR_DONT_SET)) return;
	if (!xarVarFetch('enddate', 'isset:', $enddate, NULL, XARVAR_DONT_SET)) return;
	if (!xarVarFetch('shortdesc', 'isset:', $shortdesc, NULL, XARVAR_DONT_SET)) return;
	if (!xarVarFetch('longdesc', 'str:1:', $longdesc, $longdesc, XARVAR_NOT_REQUIRED)) return;

   // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.  This could have been passed in by a hook or
    // through some other function calling this as part of a larger module, but
    // if it exists it overrides $exid

    // Note that this module couuld just use $objectid everywhere to avoid all
    // of this munging of variables, but then the resultant code is less
    // descriptive, especially where multiple objects are being used.  The
    // decision of which of these ways to go is up to the module developer
    if (!empty($objectid)) {
        $courseid = $objectid;
    }
    // The user API function is called.  This takes the item ID which we
    // obtained from the input and gets us the information on the appropriate
    // item.  If the item does not exist we post an appropriate message and
    // return
    $item = xarModAPIFunc('courses',
                          'user',
                          'get',
                          array('courseid' => $courseid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.  However,
    // in this case we had to wait until we could obtain the item name to
    // complete the instance information so this is the first chance we get to
    // do the check
    if (!xarSecurityCheck('EditCourses', 1, 'Item', "$item[name]:All:$courseid")) {
        return;
    }
    // Get menu variables - it helps if all of the module pages have a standard
    // menu at their head to aid in navigation
    // $menu = xarModAPIFunc('example','admin','menu','modify');
    $item['module'] = 'courses';
    $hooks = xarModCallHooks('item', 'modify', $courseid, $item);
    if (empty($hooks)) {
        $hooks = '';
    } elseif (is_array($hooks)) {
        $hooks = join('', $hooks);
    }
    // Return the template variables defined in this function
	$dateformat = '%Y-%m-%d %H:%M:%S';
    $startdate = xarLocaleFormatDate($dateformat, $startdate);
    $enddate = xarLocaleFormatDate($dateformat, $enddate);
    return array('authid'       => xarSecGenAuthKey(),
                 'namelabel'    => xarVarPrepForDisplay(xarML('Course Name:')),
	             'name'         => $name,
                 'numberlabel'  => xarVarPrepForDisplay(xarML('Course Number:')),
	             'number'       => $number,
				 'hourslabel'  => xarVarPrepForDisplay(xarML('Course Hours:')),
				 'hours'  => $hours,
				 'ceulabel'  => xarVarPrepForDisplay(xarML('Course Credit Hours:')),
				 'ceu' => $ceu,
				 'startdatelabel'  => xarVarPrepForDisplay(xarML('Start Date:')),
				 'startdate' => $startdate,
				 'enddatelabel'  => xarVarPrepForDisplay(xarML('End Date:')),
				 'enddate' => $enddate,
				 'shortdesclabel'  => xarVarPrepForDisplay(xarML('Short Description:')),
				 'shortdesc' => $item['shortdesc'],
				 'longdesclabel'  => xarVarPrepForDisplay(xarML('Long Description:')),
				 'longdesc' => $item['longdesc'],
				 'invalid'      => $invalid,
                 'updatebutton' => xarVarPrepForDisplay(xarML('Update Course')),
                 'hooks'        => $hooks,
                 'item'         => $item);
}

?>
