<?php
/*
 *
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage polls
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
    if (!xarVarFetch('title', 'str:1:', $title, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if (!xarSecurityCheck('AddPolls')) {
        return;
    }

    if (!isset($title) || !isset($polltype)){
        $msg = xarML('Missing required field title or polltype');
        xarErrorSet(XAR_USER_EXCEPTION,
                    'MISSING_DATA',
                     new DefaultUserException($msg));
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

    // Pass to API
    $pid = xarModAPIFunc('polls',
                        'admin',
                        'create', array('title' => $title,
                                        'polltype' => $polltype,
                                        'private' => $private));
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