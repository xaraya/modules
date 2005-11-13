<?php
/**
 * File: $Id:
 * 
 * Standard function to update a current item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage maxercalls
 * @author Example module development team 
 */
/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('maxercalls','admin','modify') to update a current item
 * 
 * @param  $ 'exid' the id of the item to be updated
 * @param  $ 'name' the name of the item to be updated
 * @param  $ 'number' the number of the item to be updated
 */
function maxercalls_admin_updatecall($args)
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
    if (!xarVarFetch('callid', 'int:1:', $callid, $callid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'int:1:', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('calldate', 'str:1:', $calldate, $calldate, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('calltime', 'str:1:', $calltime, $calltime, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enteruid', 'int:1:', $enteruid, $enteruid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('owner', 'int:1:', $owner, $owner, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('remarks', 'str:1:', $remarks, $remarks, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enterts', 'str:1:', $enterts, $enterts, XARVAR_NOT_REQUIRED)) return;
	
    if (!empty($objectid)) {
        $callid = $objectid;
    } 

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return; 
    // Notable by its absence there is no security check here.  This is because
    // the security check is carried out within the API function and as such we
    // do not duplicate the work here

    $invalid = array();
    if (empty($calldate) || !is_string($calldate)) {
        $invalid['calldate'] = 1;
        $calldate = '';
    } 
    $invalid = array();
    if (empty($calltime) || !is_string($calltime)) {
        $invalid['calltime'] = 1;
        $calltime = '';
    } 
    if (empty($owner) || !is_numeric($owner)) {
        $invalid['owner'] = 1;
        $owner = '';
    } 

    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_new function and return the template vars
        // (you need to copy admin-new.xd to admin-create.xd here)
        return xarModFunc('maxercalls', 'admin', 'modifycall',
                          array('callid' => $callid,
						        'enteruid' => $enteruid,
						        'owner' => $owner,
								'remarks' => $remarks,
								'calldatetime' => $calldatetime,
								'calltime' => $calltime,
								'enterts' => $enterts,
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
    if (!xarModAPIFunc('maxercalls',
                       'admin',
                       'updatecall',
                       array('callid' => $callid,
                'calldate' => $calldate,
                'calltime' => $calltime,
                'remarks' => $remarks,
				'enterts' => $enterts,
				'enteruid' => $enteruid,
				'owner' => $owner))) {
        return; // throw back
    } 
    xarSessionSetVar('statusmsg', xarML('Maxer Call was successfully updated!')); 
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('maxercalls', 'admin', 'viewcalls')); 
    // Return
    return true;
} 

?>
