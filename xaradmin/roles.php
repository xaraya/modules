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
sys::import('modules.workflow.lib.galaxia.api');
  /**
   * the roles administration function
   *
   * @author mikespub
   * @access public
   */
function workflow_admin_roles()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

    // Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $data = array();

    // Adapted from tiki-g-admin_roles.php
    include_once(GALAXIA_LIBRARY.'/processmanager.php');

    if (!xarVarFetch('pid','id',$pid)) return;
    if (empty($pid)) {
        $data['msg'] =  xarML("No process indicated");
        return xarTplModule('workflow', 'admin', 'error', $data);
    }
    $data['pid'] =  $pid;

    // Retrieve the relevant process info
    $process = new Process($pid);
    $proc_info = $processManager->get_process($pid);
    $proc_info['graph']=$process->getGraph();

    // Role ID set?
    if (!xarVarFetch('roleId','id',$roleId,0,XARVAR_NOT_REQUIRED)) return;
    if ($roleId) {
        // Get it
        $data['info'] = $roleManager->get_role($pid, $roleId);
    } else {
        // Set it
        $data['info'] = array('name' => '', 'description' => '', 'roleId' => 0 );
    }
    $data['roleId'] =  $roleId;

    // Delete roles
    if (isset($_REQUEST["delete"])) {
        foreach (array_keys($_REQUEST["role"])as $item) {
            $roleManager->remove_role($pid, $item);
        }
    }

    // If we are adding an roles then add it!
    if (isset($_REQUEST['save'])) {
        $vars = array('name' => $_REQUEST['name'],'description' => $_REQUEST['description']);
        // Save that
        $roleManager->replace_role($pid, $roleId, $vars);
        $vars['roleId'] = $roleId;
        $data['info'] =  $vars;
    }

    // MAPPING
    $data['find_users'] =isset($_REQUEST['find_users']) ? $_REQUEST['find_users'] : '';

    $numusers = xarModAPIFunc('roles','user','countall');
    // don't show thousands of users here without filtering
    if ($numusers > 1000 && empty($data['find_users'])) {
        $data['users'] = array();
    } else {
        $selection = '';
        if (!empty($data['find_users'])) {
            $dbconn =& xarDBGetConn();
            $selection = " AND xar_name LIKE " . $dbconn->qstr('%'.$data['find_users'].'%');
        }
        $data['users'] = xarModAPIFunc('roles','user','getall',
                                       array('selection' => $selection,
                                             'order' => 'name'));
    }

    $data['groups'] = xarModAPIFunc('roles','user','getallgroups');

    $roles = $roleManager->list_roles($pid, 0, -1, 'name_asc', '');
    $data['roles'] =&  $roles['data'];

    if (isset($_REQUEST["delete_map"])) {
        foreach (array_keys($_REQUEST["map"])as $item) {
            $parts = explode(':::', $item);
            $roleManager->remove_mapping($parts[0], $parts[1]);
        }
    }

    if (isset($_REQUEST['mapg'])) {
        if ($_REQUEST['op'] == 'add') {
            $users = xarModAPIFunc('roles','user','getusers',
                                   array('uid' => $_REQUEST['group']));
            foreach ($users as $a_user) {
                $roleManager->map_user_to_role($pid, $a_user['uid'], $_REQUEST['role']);
            }
        } else {
            $users = xarModAPIFunc('roles','user','getusers',
                                   array('uid' => $_REQUEST['group']));
            foreach ($users as $a_user) {
                $roleManager->remove_mapping($a_user['uid'], $_REQUEST['role']);
            }
        }
    }

    if (isset($_REQUEST['save_map'])) {
        if (isset($_REQUEST['user']) && isset($_REQUEST['role'])) {
            foreach ($_REQUEST['user'] as $a_user) {
                if (empty($a_user)) {
                    $a_user = _XAR_ID_UNREGISTERED;
                }
                foreach ($_REQUEST['role'] as $role) {
                    $roleManager->map_user_to_role($pid, $a_user, $role);
                }
            }
        }
    }

    // list mappings
    $data['offset']    = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 1;
    $data['find']      = isset($_REQUEST['find']) ? $_REQUEST['find'] : '';
    $data['sort_mode'] = isset($_REQUEST['sort_mode']) ? $_REQUEST['sort_mode'] : 'name_asc';
    $mapitems = $roleManager->list_mappings($pid, $data['offset'] - 1, $maxRecords, $data['sort_mode'], $data['find']);

    // trick : replace userid by user here !
    foreach (array_keys($mapitems['data']) as $index) {
        $role = xarModAPIFunc('roles','user','get',
                              array('uid' => $mapitems['data'][$index]['user']));
        if (!empty($role)) {
            $mapitems['data'][$index]['userId'] = $role['uid'];
            $mapitems['data'][$index]['user'] = $role['name'];
            $mapitems['data'][$index]['login'] = $role['uname'];
        } else {
            $roleManager->remove_mapping($mapitems['data'][$index]['user'], $mapitems['data'][$index]['roleId']);
            $mapitems['cant'] = $mapitems['cant'] - 1;
            unset($mapitems['data'][$index]);
        }
    }

    $data['cant'] =  $mapitems['cant'];
    $data['cant_pages']  =  ceil($mapitems["cant"] / $maxRecords);
    $data['actual_page'] =  1 + (($data['offset'] - 1) / $maxRecords);

    $data['next_offset'] =  -1;
    if ($mapitems["cant"] >= ($data['offset'] + $maxRecords)) {
        $data['next_offset'] =  $data['offset'] + $maxRecords;
    }

    $data['prev_offset'] =  -1;
    if ($data['offset'] > 1) {
        $data['prev_offset'] =  $data['offset'] - $maxRecords;
    }
    $data['mapitems'] =&  $mapitems["data"];

    //MAPPING
    $data['sort_mode2'] =  isset($_REQUEST['sort_mode2']) ? $_REQUEST['sort_mode2'] : 'name_asc';
    // Get all the process roles
    $all_roles = $roleManager->list_roles($pid, 0, -1, $data['sort_mode2'], '');
    $data['items'] =&  $all_roles['data'];

    $valid = $activityManager->validate_process_activities($pid);
    $proc_info['isValid'] = $valid ? 'y' : 'n';
    $errors = array();
    if (!$valid) {
        $errors = $activityManager->get_error();
    }

    $data['errors'] =  $errors;
    $data['proc_info'] =  $proc_info;
    $url = xarModURL('workflow','admin','roles',array('pid' => $data['pid'],'offset' => '%%'));
    $data['pager'] = xarTplGetPager($data['offset'],$mapitems['cant'],$url,$maxRecords);
    return $data;
}

?>
