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
 * display form for a new poll option
 */
function polls_admin_newopt()
{
    // Get parameters
    if (!xarVarFetch('pid', 'id', $pid, XARVAR_DONT_SET)) return;

    if (!isset($pid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Start output
    $data = array();

    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    // Title
    $data['polltitle'] =  xarVarPrepHTMLDisplay($poll['title']);

    $data['authid'] = xarSecGenAuthKey();
    $data['pid'] = xarVarPrepForDisplay($pid);

    $data['buttonlabel'] = xarML('Create Option');
    $data['cancelurl'] = xarModURL('polls',
                            'admin',
                            'display',
                            array('pid' => $pid));

    return $data;
}

?>