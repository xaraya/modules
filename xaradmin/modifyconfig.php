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
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function polls_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminPolls')) {
        return;
    }

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    $data['barscale'] = xarModGetVar('polls', 'barscale');
    $data['defaultopts'] = xarModGetVar('polls', 'defaultopts');
    $data['previewresults'] = xarModGetVar('polls', 'previewresults');
    $data['shorturl'] = xarModGetVar('polls', 'SupportShortURLs');

    $data['imggraphs'] = array();
    $data['imggraphs']['0'] = xarML('Never');
    $data['imggraphs']['1'] = xarML('Blocks only');
    $data['imggraphs']['2'] = xarML('Module space only');
    $data['imggraphs']['3'] = xarML('Always');
    $data['imggraph'] =  xarModGetVar('polls', 'imggraph');

    $data['voteintervals'] = array();
    $data['voteintervals']['-1'] = xarML('Once');
    $data['voteintervals']['86400'] = xarML('Once per day');
    $data['voteintervals']['604800'] = xarML('Once per week');
    $data['voteintervals']['2592000'] = xarML('Once per month');
    $data['voteinterval'] =  xarModGetVar('polls', 'voteinterval');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'polls',
                             array('module' => 'polls'));
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
