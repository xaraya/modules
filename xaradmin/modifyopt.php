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

function polls_admin_modifyopt()
{
    // Get parameters
    if (!xarVarFetch('pid', 'id', $pid, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('opt', 'int:0:', $opt, XARVAR_DONT_SET)) return;

    // Check arguments
    if (empty($pid) || empty($opt)) {
        $msg = xarML('No poll or option specified');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Start output
    $data = array();

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    if (!$poll) {
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }

    // Security check
    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    // Title
    $data['polltitle'] = $poll['title'];
    $data['authid'] = xarSecGenAuthKey();
    $data['pid'] = xarVarPrepHTMLDisplay($pid);
    $data['opt'] = $opt;

    // Name
    $data['option'] = xarVarPrepHTMLDisplay($poll['options'][$opt]['name']);

    // End form

    $data['buttonlabel'] = xarML('Modify Option');
    $data['cancelurl'] = xarModURL('polls',
                            'admin',
                            'display',
                            array('pid' => $pid));

    return $data;
}

?>