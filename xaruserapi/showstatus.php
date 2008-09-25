<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * show the current status of "your" instances, i.e. those that you started and
 * are the owner of (called via <xar:workflow-status tag)
 *
 * @author mikespub
 * @access public
 */
function workflow_userapi_showstatus($args)
{
    // Security Check
    if (!xarSecurityCheck('ReadWorkflow',0)) {
        return '';
    }

    // Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    $tplData = array();

    include (GALAXIA_LIBRARY.'/processmonitor.php');

    if (empty($user)) {
        $user = xarUserGetVar('id');
    }

// TODO: keep track of instances from anonymous visitors via session ?

    // retrieve the instances for which you're the owner
    $where = "owner=$user";
    if (!empty($args['status'])) {
        $where .= " and gi.status='" . $args['status'] . "'";
    }
    if (!empty($args['actstatus'])) {
        $where .= " and gia.status='" . $args['actstatus'] . "'";
    }
    if (!empty($args['pId'])) {
        $where .= " and gp.pId='" . $args['pId'] . "'";
    }
    $items = $processMonitor->monitor_list_instances(0, -1, 'started_asc', '', $where, array());

    if (xarUserIsLoggedIn()) {
        $seenlist = xarModUserVars::get('workflow','seenlist');
    } else {
        $seenlist = xarSession::getVar('workflow.seenlist');
    }
    if (!empty($seenlist)) {
        $seen = explode(';',$seenlist);
    } else {
        $seen = array();
    }
    $tplData['items'] = array();
    foreach ($items['data'] as $index => $info) {
        if (in_array($info['instanceId'],$seen)) continue;
        $items['data'][$index]['started'] = xarLocaleGetFormattedDate('medium',$info['started']) . ' '
                                            . xarLocaleGetFormattedTime('short',$info['started']);
        $items['data'][$index]['userId'] = $info['user'];
        if (is_numeric($info['user'])) {
            $items['data'][$index]['user'] = xarUserGetVar('name',$info['user']);
        }
        $tplData['items'][] = $items['data'][$index];
    }
    if (count($tplData['items']) < 1) {
        return '';
    }

    $tplData['userId'] = $user;

    if (!empty($args['layout'])) {
        $tplData['layout'] = $args['layout'];
    }

    // URL to return to if some action is taken
    $tplData['return_url'] = xarServerGetCurrentURL();

    if (!empty($args['template'])) {
        return xarTplModule('workflow', 'user', 'showstatus', $tplData, $args['template']);
    } else {
        return xarTplModule('workflow', 'user', 'showstatus', $tplData);
    }
}

?>
