<?php
/*
 *
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * increment position for a poll option
 */
function polls_admin_incopt()
{
    // Get parameters

    if (!xarVarFetch('pid', 'id', $pid, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('opt', 'int:0:', $opt, XARVAR_DONT_SET)) return;

    if (!isset($pid) || !isset($opt) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    $incremented = xarModAPIFunc('polls', 'admin', 'incopt', array('pid' => $pid,
                                                         'opt' => $opt));

    if (!$incremented && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Redirect
    xarResponseRedirect(xarModURL('polls',
                        'admin',
                        'modify',
                        array('pid' => $pid)));
    return true;
}

?>
