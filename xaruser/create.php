<?php
/**
 * Standard function to create a new item
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('maxercalls','admin','new') to create a new item
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  int $callhours the date and time of the call to be created
 * @param  int $callminutes the date and time of the call to be created
 * @param  int $callday the date and time of the call to be created
 * @param  int $callmonth the date and time of the call to be created
 * @param  int $callyear the date and time of the call to be created
 * @param  int $calltext the type of text reported
 * @param  string $remarks any remarks regarding this call to be created
 * @return bool true on success
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

    // Argument check
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
    // Confirm authorisation code.
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
                          array('enteruid'  => $enteruid,
                                'owner'     => $owner,
                                'remarks'   => $remarks,
                                'calldate'  => $calldate,
                                'calltime'  => $calltime,
                                'calltext'  => $calltext,
                                'enterts'   => $enterts,
                                'itemtype'  => $itemtype));

    if (!isset($callid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('maxercalls', 'user', 'view'));
    // Return
    return true;
}

?>
