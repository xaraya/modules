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
 * modify a poll
 */
function polls_admin_modify()
{
    // Get parameters
    if (!xarVarFetch('pid', 'id', $pid)) return;

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
    $data['authid'] = xarSecGenAuthKey();
    $data['pid'] = $pid;

    $data['buttonlabel'] = xarML('Modify Poll');

    $data['polltitle'] = xarVarPrepHTMLDisplay($poll['title']);
    $data['polltype'] = $poll['type'];
    $data['private'] = $poll['private'];

    $defaultopts = xarModGetVar('polls', 'defaultopts');
    if(count($poll['options']) > $defaultopts){
        $defaultopts = count($poll['options']);
    }

    $options = array();
    for($i = 1; $i <= $defaultopts;$i++){
        if(isset($poll['options'][$i])){
            $options[$i]['name'] = xarVarPrepHTMLDisplay($poll['options'][$i]['name']);
        }
        else{
            $options[$i]['name'] = '';
        }
        $options[$i]['field'] = "option_$i";
        $options[$i]['counter'] = $i;
    }

    $data['options'] = $options;

    $item['module'] = 'polls';
    $item['itemid'] = $pid;
    $item['itemtype'] = 0;
    $hooks = xarModCallHooks('item', 'modify', $pid, $item);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    return $data;
}

?>