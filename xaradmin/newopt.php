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
 * display form for a new poll option
 */
function polls_admin_newopt()
{
    // Get parameters
    if (!xarVarFetch('pid', 'id', $pid, XARVAR_DONT_SET)) return;

    if (!isset($pid) && xarCurrentErrorType() != NO_EXCEPTION) return; // throw back

    // Start output
    $data = array();

    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (!xarSecurityCheck('EditPolls',1,'Polls',"$poll[pid]:$poll[type]")) {
        return;
    }

    // Title
    $data['polltitle'] =  xarVarPrepHTMLDisplay($poll['title']);

    $data['authid'] = xarSecGenAuthKey();
    $data['pid'] = xarVarPrepForDisplay($pid);

    $data['buttonlabel'] = xarML('Create Option');
    $data['cancelurl'] = xarModURL('polls',
                            'admin',
                            'modify',
                            array('pid' => $pid));

    return $data;
}

?>
