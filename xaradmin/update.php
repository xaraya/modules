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
 * update a poll
 */
function polls_admin_update()
{

    // Get parameters
    if (!xarVarFetch('pid', 'id', $pid)) return;
    if (!xarVarFetch('polltype', 'str:1:', $type, 'single', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'int:0:1', $private, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str:1:', $title, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if($private != 1){
        $private = 0;
    }

    // Get poll info
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    if (!$poll) {
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }

    // security check
    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }
    $options = $poll['options'];

    // Pass to API
    $updated = xarModAPIFunc('polls',
                      'admin',
                      'update',
                      array('pid' => $pid,
                           'title' => $title,
                           'type' => $type,
                           'private' => $private));
    if(!$updated){
       return false;
    }

    // Success
    xarSessionSetVar('statusmsg', xarML('Poll Updated'));

    xarResponseRedirect(xarModURL('polls', 'admin', 'list'));

    return true;
}

?>