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
 * reset a poll
 */
function polls_admin_reset()
{
    // Get parameters

    if (!xarVarFetch('pid', 'id', $pid)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, NULL, XARVAR_DONT_SET)) return;

    if (!isset($pid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (!xarSecurityCheck('AdminPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    // Check for confirmation
    if ($confirm != 1) {
        // No confirmation yet - get one

        $data = array();

        $data['polltitle'] = $poll['title'];
        $data['pid'] = $pid;
        $data['confirm'] = 1;
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = 'Reset Poll';

        return $data;
    }

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (xarModAPIFunc('polls',
                     'admin',
                     'reset', array('pid' => $pid))) {
        // Success
        xarSessionSetVar('statusmsg', xarML('Poll reset'));

    }

    xarResponseRedirect(xarModURL('polls', 'admin', 'list'));

    return true;
}


?>