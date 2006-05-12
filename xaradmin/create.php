<?php
/**
 * Polls module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Polls Module
 * @link http://xaraya.com/index.php/release/23.html
 * @author Jim McDonalds, dracos, mikespub et al.
 */
/**
 * create a new poll
 */
function polls_admin_create()
{
    // Get parameters

    if (!xarVarFetch('polltype', 'str:1:', $polltype, 'single', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'int:0:1', $private, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str:1:', $title, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('start_date', 'str:1:', $start_date, time(),  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('end_date', 'str:1:', $end_date, NULL, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if (!xarSecurityCheck('AddPolls')) {
        return;
    }

    if (!isset($title)){
        $msg = xarML('Missing required field title');
        xarErrorSet(XAR_USER_EXCEPTION,'MISSING_DATA',new DefaultUserException($msg));
        return;
    }

    if ($polltype != 'single' && $polltype != 'multi'){
        $msg = xarML('Invalid poll type');
        xarErrorSet(XAR_USER_EXCEPTION,
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return;
    }
    if ($private != 1){
        $private = 0;
    }

     if (!empty($start_date)) {
        $start_date .= ' GMT';
        $start_date = strtotime($start_date);
        // adjust for the user's timezone offset
        $start_date -= xarMLS_userOffset() * 3600;
        }

     if(!empty($end_date)) {
        $end_date .= ' GMT';
        $end_date = strtotime($end_date);
        // adjust for the user's timezone offset
        $end_date -= xarMLS_userOffset() * 3600;
        }




    // Pass to API
    $pid = xarModAPIFunc('polls',
                        'admin',
                        'create', array('title' => $title,
                                        'polltype' => $polltype,
                                        'private' => $private,
                                        'start_date' => $start_date,
                                        'end_date' => $end_date));
    if (!$pid) {
        // Something went wrong - return
        $msg = xarML('Unable to create poll');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }

    $optlimit = xarModGetVar('polls', 'defaultopts');

    for ($i = 1; $i <= $optlimit; $i++) {
        xarVarFetch('option_' . $i, 'isset', $option[$i]);
        if (!empty($option[$i])) {
            xarModAPIFunc('polls',
                         'admin',
                         'createopt',
                         array('pid' => $pid,
                               'option' => $option[$i]));
        }
    }

    // Back to main page
    // Success
    xarSessionSetVar('polls_statusmsg', xarML('Poll Created Successfuly.'));
    xarResponseRedirect(xarModURL('polls', 'admin', 'list'));

    return true;
}

?>
