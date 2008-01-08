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
 * Modify options
 */
function polls_admin_modifyopt()
{
    // Get parameters
    if (!xarVarFetch('pid', 'id', $pid, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('opt', 'int:0:', $opt, XARVAR_DONT_SET)) return;

    // Check arguments
    if (empty($pid) || empty($opt)) {
        throw new BadParameterException(array($pid,$option),'Missing Poll id (#(1)), or Options (#(2))');
    }

    // Start output
    $data = array();

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    if (!$poll) {
        throw new BadParameterException($pid,'Poll not found (#(1))');
    }

    // Security check
    if (!xarSecurityCheck('EditPolls',1,'Polls',"$poll[pid]:$poll[type]")) {
        return;
    }

    // Title
    $data['polltitle'] = $poll['title'];
    $data['authid'] = xarSecGenAuthKey();
    $data['pid'] = xarVarPrepHTMLDisplay($pid);
    $data['opt'] = $opt;

    // Name
    $data['option'] = xarVarPrepHTMLDisplay($poll['options'][$opt]['name']);

    $data['cancelurl'] = xarModURL('polls',
                            'admin',
                            'modify',
                            array('pid' => $pid));

    return $data;
}

?>
