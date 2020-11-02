<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * show the instances assigned/accessible to you (called via <xar:workflow-instances tag)
 *
 * @author mikespub
 * @access public
 */
function workflow_userapi_showinstances($args)
{
    // Security Check
    if (!xarSecurity::check('ReadWorkflow', 0)) {
        return '';
    }

    // Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    $tplData = array();

    include(GALAXIA_LIBRARY.'/gui.php');

    if (empty($user)) {
        $user = xarUser::getVar('id');
    }

    // TODO: keep track of instances from anonymous visitors via session ?

    $wheres = array();
    if (!empty($args['status'])) {
        if (strpos($args['status'], ',')) {
            $statuslist = explode(',', $args['status']);
            $wheres[] = "gi.status IN ('" . implode("','", $statuslist) . "')";
        } else {
            $wheres[] = "gi.status='" . $args['status'] . "'";
        }
    }
    if (!empty($args['notstatus'])) {
        if (strpos($args['notstatus'], ',')) {
            $statuslist = explode(',', $args['notstatus']);
            $wheres[] = "gi.status NOT IN ('" . implode("','", $statuslist) . "')";
        } else {
            $wheres[] = "gi.status!='" . $args['notstatus'] . "'";
        }
    }
    if (!empty($args['actstatus'])) {
        $wheres[] = "gia.status='" . $args['actstatus'] . "'";
    }
    if (!empty($args['pId'])) {
        $wheres[] = "gp.pId='" . $args['pId'] . "'";
    }
    if (!empty($args['owner'])) {
        $wheres[] = "gi.owner='" . $args['owner'] . "'";
    }
    if (!empty($args['user'])) {
        $wheres[] = "gia.user='" . $args['user'] . "'";
    }
    $where = implode(' and ', $wheres);

    if (!empty($args['numitems'])) {
        $numitems = $args['numitems'];
    } else {
        $numitems = -1;
    }
    if (!empty($args['startnum'])) {
        $startnum = $args['startnum'];
    } else {
        $startnum = 1;
    }
    if (!empty($args['sort_mode'])) {
        $sort_mode = $args['sort_mode'];
    } else {
        $sort_mode = 'started_asc';
    }
    $items = $GUI->gui_list_user_instances($user, $startnum - 1, $numitems, $sort_mode, '', $where);
    if (empty($items) || count($items['data']) < 1) {
        return '';
    }

    // filter out instances the user doesn't want to see
    if (xarUser::isLoggedIn()) {
        $seenlist = xarModUserVars::get('workflow', 'seenlist');
    } else {
        $seenlist = xarSession::getVar('workflow.seenlist');
    }
    if (!empty($seenlist)) {
        $seen = explode(';', $seenlist);
    } else {
        $seen = array();
    }
    $tplData['items'] = array();
    foreach ($items['data'] as $index => $info) {
        if (in_array($info['instanceId'], $seen)) {
            continue;
        }
        $tplData['items'][] = $items['data'][$index];
    }
    if (count($tplData['items']) < 1) {
        return '';
    }

    $tplData['userId'] = $user;

    if (!empty($args['title'])) {
        $tplData['title'] = $args['title'];
    }

    if (!empty($args['layout'])) {
        $tplData['layout'] = $args['layout'];
    }

    // URL to return to if some action is taken
    if (!empty($args['return_url'])) {
        $tplData['return_url'] = $args['return_url'];
    } else {
        $tplData['return_url'] = xarServer::getCurrentURL();
    }

    // field list to show
    if (!empty($args['fieldlist'])) {
        $tplData['fieldlist'] = explode(',', $args['fieldlist']);
    }

    // action list to show (could be empty here !)
    if (isset($args['actionlist'])) {
        if (!empty($args['actionlist'])) {
            $tplData['actionlist'] = explode(',', $args['actionlist']);
        } else {
            $tplData['actionlist'] = array();
        }
    }

    if (!empty($args['template'])) {
        return xarTpl::module('workflow', 'user', 'showinstances', $tplData, $args['template']);
    } else {
        return xarTpl::module('workflow', 'user', 'showinstances', $tplData);
    }
}
