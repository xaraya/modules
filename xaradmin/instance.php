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
 * the instance administration function
 *
 * @author mikespub
 * @access public
 */
function workflow_admin_instance()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $tplData = array();

// Adapted from tiki-g-admin_instance.php

include_once (GALAXIA_LIBRARY.'/ProcessManager.php');
include_once (GALAXIA_LIBRARY.'/API.php');

if (!isset($_REQUEST['iid'])) {
    $tplData['msg'] =  xarML("No instance indicated");

    return xarTplModule('workflow', 'admin', 'error', $tplData);
}

$tplData['iid'] =  $_REQUEST['iid'];

// Get workitems and list the workitems with an option to edit workitems for
// this instance
if (isset($_REQUEST['save'])) {
    //status, owner
    $instanceManager->set_instance_status($_REQUEST['iid'], $_REQUEST['status']);

    $instanceManager->set_instance_owner($_REQUEST['iid'], $_REQUEST['owner']);
    //y luego acts[activityId][user] para reasignar users
    foreach (array_keys($_REQUEST['acts'])as $act) {
        $instanceManager->set_instance_user($_REQUEST['iid'], $act, $_REQUEST['acts'][$act]);
    }

    if ($_REQUEST['sendto']) {
        $instanceManager->set_instance_destination($_REQUEST['iid'], $_REQUEST['sendto']);
    }
//process sendto
}

// Get the instance and set instance information
$ins_info = $instanceManager->get_instance($_REQUEST['iid']);
if (!empty($ins_info['started'])) {
    $date = xarLocaleGetFormattedDate('medium',$ins_info['started']) . ' '
            . xarLocaleGetFormattedTime('short',$ins_info['started']);
    $ins_info['started'] = $date;
}
$tplData['ins_info'] =&  $ins_info;

// Get the process from the instance and set information
$proc_info = $processManager->get_process($ins_info['pId']);
$tplData['proc_info'] =&  $proc_info;

// Process activities
$activities = $activityManager->list_activities($ins_info['pId'], 0, -1, 'flowNum_asc', '', '');
$tplData['activities'] =  $activities['data'];

// Users
$mapitems = $roleManager->list_mappings($ins_info['pId'], 0, -1, 'name_asc', '');
// trick : replace userid by user here !
foreach (array_keys($mapitems['data']) as $index) {
    $role = xarModAPIFunc('roles','user','get',
                          array('uid' => $mapitems['data'][$index]['user']));
    if (!empty($role)) {
        $mapitems['data'][$index]['userId'] = $role['uid'];
        $mapitems['data'][$index]['user'] = $role['name'];
        $mapitems['data'][$index]['login'] = $role['uname'];
    }
}
$tplData['users'] =&  $mapitems['data'];

$props = $instanceManager->get_instance_properties($_REQUEST['iid']);

if (isset($_REQUEST['unsetprop'])) {
    unset ($props[$_REQUEST['unsetprop']]);

    $instanceManager->set_instance_properties($_REQUEST['iid'], $props);
}

if (!is_array($props))
    $props = array();

$tplData['props'] =&  $props;

if (isset($_REQUEST['addprop'])) {
    $props[$_REQUEST['name']] = $_REQUEST['value'];

    $instanceManager->set_instance_properties($_REQUEST['iid'], $props);
}

if (isset($_REQUEST['saveprops'])) {
    foreach (array_keys($_REQUEST['props'])as $key) {
        $props[$key] = $_REQUEST['props'][$key];
    }

    $instanceManager->set_instance_properties($_REQUEST['iid'], $props);
}

$acts = $instanceManager->get_instance_activities($_REQUEST['iid']);
$tplData['acts'] =&  $acts;

$instance->getInstance($_REQUEST['iid']);

// Process comments
if (isset($_REQUEST['__removecomment'])) {
    $__comment = $instance->get_instance_comment($_REQUEST['__removecomment']);

    if ($__comment['user'] == $user or $tiki_p_admin_workflow == 'y') {
        $instance->remove_instance_comment($_REQUEST['__removecomment']);
    }
}

$tplData['__comments'] =&  $__comments;

if (!isset($_REQUEST['__cid']))
    $_REQUEST['__cid'] = 0;

if (isset($_REQUEST['__post'])) {
    $instance->replace_instance_comment($_REQUEST['__cid'], 0, '', $user, $_REQUEST['__title'], $_REQUEST['__comment']);
}

$__comments = $instance->get_instance_comments();

$tplData['mid'] =  'tiki-g-admin_instance.tpl';

    return $tplData;
}

?>
