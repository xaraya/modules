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
 * create new poll option
 */
function polls_admin_createopt()
{
    // Get parameters

    if (!xarVarFetch('pid', 'id', $pid, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('option', 'str:0:', $option, XARVAR_DONT_SET)) return;

    if (!isset($pid) || !isset($option) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    $poll = xarModAPIFunc('polls',
                           'user',
                           'get', array('pid' => $pid));

    if (!isset($poll) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditPolls',1,'Polls',"$poll[title]:$poll[type]")) {
        return;
    }

    // Pass to API
    $created = xarModAPIFunc('polls',
                           'admin',
                           'createopt', array('pid' => $pid,
                                              'option' => $option));

    if (!$created && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('Created option'));

    xarResponseRedirect(xarModURL('polls',
                        'admin',
                        'modify',
                        array('pid' => $pid)));

    return true;
}

?>
