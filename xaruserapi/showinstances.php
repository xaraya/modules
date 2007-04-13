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
 * show the instances assigned/accessible to you (called via <xar:workflow-instances tag)
 *
 * @author mikespub
 * @access public
 */
function workflow_userapi_showinstances($args)
{
    // Security Check
    if (!xarSecurityCheck('ReadWorkflow',0)) {
        return '';
    }

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $tplData = array();

    include(GALAXIA_LIBRARY.'/GUI.php');

    if (empty($user)) {
        $user = xarUserGetVar('uid');
    }

// TODO: keep track of instances from anonymous visitors via session ?

    $wheres = array();
    if (!empty($args['status'])) {
        $wheres[] = "gi.status='" . $args['status'] . "'";
    }
    if (!empty($args['actstatus'])) {
        $wheres[] = "gia.status='" . $args['actstatus'] . "'";
    }
    if (!empty($args['id'])) {
        $wheres[] = "gp.id='" . $args['id'] . "'";
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

    foreach ($items['data'] as $index => $info) {
        if (!empty($info['started'])) {
            $items['data'][$index]['started'] = xarLocaleGetFormattedDate('medium',$info['started']) . ' '
                                                . xarLocaleGetFormattedTime('short',$info['started']);
        }
        $items['data'][$index]['ownerId'] = $info['owner'];
        if (!empty($info['owner']) &&
            is_numeric($info['owner'])) {
            $items['data'][$index]['owner'] = xarUserGetVar('name', $info['owner']);
        }
        if (!is_numeric($info['user'])) {
            $items['data'][$index]['userId'] = $info['user'];
            continue;
        }
        $role = xarModAPIFunc('roles','user','get',
                              array('uid' => $info['user']));
        if (!empty($role)) {
            $items['data'][$index]['userId'] = $role['uid'];
            $items['data'][$index]['user'] = $role['name'];
            $items['data'][$index]['login'] = $role['uname'];
        }
    }
    $tplData['items'] = $items['data'];

    $tplData['userId'] = $user;

    if (!empty($args['layout'])) {
        $tplData['layout'] = $args['layout'];
    }

    // URL to return to if some action is taken
    $tplData['return_url'] = xarServerGetCurrentURL();

    if (!empty($args['template'])) {
        return xarTplModule('workflow', 'user', 'showinstances', $tplData, $args['template']);
    } else {
        return xarTplModule('workflow', 'user', 'showinstances', $tplData);
    }
}

?>
