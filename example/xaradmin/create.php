<?php
/**
 * File: $Id: 
 * 
 * Standard function to create a new item
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
 * form supplied by xarModFunc('example','admin','new') to create a new item
 * 
 * @param  $ 'name' the name of the item to be created
 * @param  $ 'number' the number of the item to be created
 */
function example_admin_create($args)
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
    if (!xarVarFetch('exid', 'str:1:', $exid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, '', XARVAR_NOT_REQUIRED)) return; 
    if (!xarVarFetch('number', 'str:1:', $number, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then return to the add form with the
    // values that are there and a message with a session var.  If you perform
    // this check now, you could do away with the check in the API along with
    // the exception that comes with it.
    $item = xarModAPIFunc('example',
                          'user',
                          'validateitem',
                          array('name' => $name));

    // Argument check
    $invalid = array();
    if (empty($number) || !is_numeric($number)) {
        $invalid['number'] = 1;
		$number = '';
    } 
    if (empty($name) || !is_string($name)) {
        $invalid['name'] = 1;
		$name = '';
    } 

    if (!empty($name) && $item['name'] == $name) {
        $invalid['duplicatename'] = 1;
        $duplicatename = '';
    } 
    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_new function and return the template vars
        // (you need to copy admin-new.xd to admin-create.xd here)
        return xarModFunc('example', 'admin', 'new',
                          array('name' => $name,
                                'number' => $number,
                                'invalid' => $invalid));
    } 
    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return; 
    // Notable by its absence there is no security check here.  This is because
    // the security check is carried out within the API function and as such we
    // do not duplicate the work here
    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array
    $exid = xarModAPIFunc('example',
                          'admin',
                          'create',
                          array('name' => $name,
                                'number' => $number)); 
    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($exid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('example', 'admin', 'view')); 
    // Return
    return true;
} 

?>
