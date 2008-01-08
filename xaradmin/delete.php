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
 * delete a poll
 */
function polls_admin_delete()
{
    // Get parameters
    if (!xarVarFetch('pid', 'id', $pid)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, NULL, XARVAR_DONT_SET)) return;

    if (!isset($pid) && xarCurrentErrorType() != NO_EXCEPTION) return; // throw back



    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (!xarSecurityCheck('DeletePolls',1,'Polls',"$poll[pid]:$poll[type]")) {return;}

    // Check for confirmation
    if ($confirm != 1) {
        // No confirmation yet - get one

        $data = array();

        $data['polltitle'] = $poll['title'];
        $data['pid'] = $pid;
        $data['confirm'] = 1;
        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (xarModAPIFunc('polls',
                     'admin',
                     'delete', array('pid' => $pid))) {
        // Success
        xarSessionSetVar('statusmsg', xarML('Poll deleted'));

    }


    xarResponseRedirect(xarModURL('polls',
                                  'admin',
                                  'list'));

    return true;
}

?>
