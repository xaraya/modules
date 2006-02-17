<?php
/**
 * Standard function to update a current call
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('maxercalls','admin','modify') to update a current item
 *
 * @param  $ 'callid' the id of the item to be updated
 * @param  $ 'name' the name of the item to be updated
 * @param  $ 'number' the number of the item to be updated
 */
function maxercalls_admin_updatecall($args)
{

    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('callid',      'id', $callid, $callid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid',    'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',     'array', $invalid, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('calldate',    'str:1:', $calldate, $calldate, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('calltime',    'str:1:', $calltime, $calltime, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enteruid',    'int:1:', $enteruid, $enteruid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('owner',       'int:1:', $owner, $owner, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('remarks',     'str:1:', $remarks, $remarks, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enterts',     'str:1:', $enterts, $enterts, XARVAR_NOT_REQUIRED)) return;

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
                          array('callid'        => $callid,
                                'enteruid'      => $enteruid,
                                'owner'         => $owner,
                                'remarks'       => $remarks,
                                'calldatetime'  => $calldatetime,
                                'calltime'      => $calltime,
                                'enterts'       => $enterts,
                                'invalid'       => $invalid));
    }
    // Update the entry here
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
