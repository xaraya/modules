<?php
/**
 * File: $Id:
 * 
 * Standard function to update a current item
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
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('example','admin','modify') to update a current item
 * 
 * @param  $ 'exid' the id of the item to be updated
 * @param  $ 'name' the name of the item to be updated
 * @param  $ 'number' the number of the item to be updated
 */
function courses_admin_update($args)
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
    if (!xarVarFetch('hours', 'int:1:', $hours, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ceu', 'int:1:', $ceu, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('startdate', 'str:1:', $startdate, $startdate, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('enddate', 'str:1:', $enddate, $enddate, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('shortdesc', 'isset:', $shortdesc, NULL, XARVAR_DONT_SET)) return;
	if (!xarVarFetch('longdesc', 'str:1:', $longdesc, $longdesc, XARVAR_NOT_REQUIRED)) return;

    // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.  This could have been passed in by a hook or
    // through some other function calling this as part of a larger module, but
    // if it exists it overrides $courseid

    // Note that this module couuld just use $objectid everywhere to avoid all
    // of this munging of variables, but then the resultant code is less
    // descriptive, especially where multiple objects are being used.  The
    // decision of which of these ways to go is up to the module developer
    if (!empty($objectid)) {
        $courseid = $objectid;
    }


    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;
    // Notable by its absence there is no security check here.  This is because
    // the security check is carried out within the API function and as such we
    // do not duplicate the work here

    $invalid = array();
    if (empty($number) || !is_numeric($number)) {
        $invalid['number'] = 1;
		$number = '';
    }
    if (empty($name) || !is_string($name)) {
        $invalid['name'] = 1;
		$name = '';
    }

	 if (empty($hours) || !is_numeric($hours)) {
        $invalid['hours'] = 1;
		$hours = '';
    }

	 if (empty($ceu) || !is_numeric($ceu)) {
        $invalid['ceu'] = 1;
		$ceu = '';
    }

	 if (empty($startdate) || !is_string($startdate)) {
        $invalid['startdate'] = 1;
		$startdate = '';
    }

	 if (empty($enddate) || !is_string($enddate)) {
        $invalid['enddate'] = 1;
		$enddate = '';
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_new function and return the template vars
        // (you need to copy admin-new.xd to admin-create.xd here)
        return xarModFunc('courses', 'admin', 'modify',
                          array('name'     => $name,
                                'number'   => $number,
								'hours' => $hours,
								'ceu' => $ceu,
								'startdate' => $startdate,
								'enddate' => $enddate,
								'shortdesc' => $shortdesc,
								'longdesc' => $longdesc,
                                'invalid'  => $invalid));
    }

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array.

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!xarModAPIFunc('courses',
                       'admin',
                       'update',
                       array('courseid'   => $courseid,
                             'name'   => $name,
                             'number' => $number,
							 'hours' => $hours,
							 'ceu' => $ceu,
							 'startdate' => $startdate,
							 'enddate' => $enddate,
							 'shortdesc' => $shortdesc,
							 'longdesc' => $longdesc))) {
        return; // throw back
    }
    xarSessionSetVar('statusmsg', xarML('Course Was Successfully Updated!'));
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('courses', 'admin', 'view'));
    // Return
    return true;
}

?>
