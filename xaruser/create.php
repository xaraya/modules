<?php
/**
 * Standard function to create a new item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls module
 * @author Michel V. Maxercalls module development team
 */

/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('maxercalls','admin','new') to create a new item
 *
 * @param  $ 'calldatetime' the date and time of the call to be created
 * @param  $ 'remarks' any remarks regarding this call to be created
 */
function maxercalls_user_create($args)
{
    extract($args);

    if (!xarVarFetch('remarks', 'str:1:', $remarks, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('callhours','int::', $callhours)) return;
    if (!xarVarFetch('callminutes','int::', $callminutes)) return;
    if (!xarVarFetch('callday', 'int:1:', $callday)) return;
    if (!xarVarFetch('callmonth', 'int:1:', $callmonth)) return;
    if (!xarVarFetch('callyear', 'int:1:', $callyear)) return;
    if (!xarVarFetch('calltext', 'int:1:', $calltext)) return;
    if (!xarVarFetch('owner', 'int::', $owner, $owner, XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('itemtype', 'int:1:', $itemtype, 1,XARVAR_NOT_REQUIRED )) return;
    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then return to the add form with the
    // values that are there and a message with a session var.  If you perform
    // this check now, you could do away with the check in the API along with
    // the exception that comes with it.
    //$item = xarModAPIFunc('maxercalls',
    //                      'user',
    //                     'validateitem',
    //                      array('name' => $name));

    // Argument check
    /*
    $invalid = array();
    if (empty($callhours) || !is_numeric($callhours)) {
        $invalid['callhours'] = 1;
        $callhours = '';
    }
    if (empty($callminutes) || !is_numeric($callminutes)) {
        $invalid['callminutes'] = 1;
        $callminutes = '';
    }
    if (empty($callyear) || !is_numeric($callyear)) {
        $invalid['callyear'] = 1;
        $callyear = '';
    }
    if (empty($callmonth) || !is_numeric($callmonth)) {
        $invalid['callmonth'] = 1;
        $callmonth = '';
    }
    if (empty($callday) || !is_numeric($callday)) {
        $invalid['callday'] = 1;
        $callday = '';
    }
    if (empty($owner) || !is_numeric($owner)) {
        $invalid['owner'] = 1;
        $owner = '';
    }
    if (empty($calltext) || !is_numeric($calltext)) {
        $invalid['calltext'] = 1;
        $calltext = '';
    }


    //if (!empty($name) && $item['name'] == $name) {
    //    $invalid['duplicatename'] = 1;
    //    $duplicatename = '';
    //}
    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_new function and return the template vars
        // (you need to copy admin-new.xd to admin-create.xd here)
        return xarModFunc('maxercalls', 'user', 'new',
                          array('remarks' => $remarks,
                                'callhours' => $callhours,
                                'callminutes' => $callminutes,
                                'callyear' => $callyear,
                                'callmonth' => $callmonth,
                                'callday' => $callday,
                                'calltext' => $calltext,
                                'owner' => $owner,
                                'invalid' => $invalid));
    } */
    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;
    $enteruid = xarUserGetVar('uid');
    $enterts = date("Y-m-d H:i:s");

    //$owner = $enteruid;
    //Put the date together
    $calldatefull = $callyear."-".$callmonth."-".$callday;
    $calldate=date('Y-m-d',strtotime($calldatefull));
    //Put the time together
    $calltime = $callhours.":".$callminutes.":"."00";

    // The API function is called.
    $callid = xarModAPIFunc('maxercalls', 'admin', 'create',
                          array('enteruid' => $enteruid,
                                'owner' => $owner,
                                'remarks' => $remarks,
                                'calldate' => $calldate,
                                'calltime' => $calltime,
                                'calltext' => $calltext,
                                'enterts' => $enterts,
                                'itemtype' => $itemtype));

    if (!isset($callid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('maxercalls', 'user', 'view'));
    // Return
    return true;
}

?>
