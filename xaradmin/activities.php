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
sys::import('modules.workflow.lib.galaxia.api');
sys::import('modules.dynamicdata.class.objects.master');
/**
 * the activities administration function
 *
 * @author mikespub
 * @access public
 */
function workflow_admin_activities()
{
    // Security Check
    if (!xarSecurity::check('AdminWorkflow')) {
        return;
    }

    // Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    $data = [];

    // Adapted from tiki-g-admin_activities.php
    include_once(GALAXIA_LIBRARY.'/processmanager.php');


    if (!xarVar::fetch('pid', 'int', $data['pid'], null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (empty($data['pid'])) {
        $data['msg'] =  xarML("No process indicated");
        return xarTpl::module('workflow', 'admin', 'errors', $data);
    }

    // Create a dataobject of this activity for displaying, saving etc.
    $data['activity'] = DataObjectMaster::getObject(['name' => 'workflow_activities']);
    $data['activity']->properties['process_id']->value = $data['pid'];

    // Create a process object
    $process = new \Galaxia\Api\Process($data['pid']);

    // @todo: use the object above
    $procNName = $process->getNormalizedName();
    $proc_info = $processManager->get_process($data['pid']);

    // Retrieve activity info if we are editing, assign to
    // default values when creating a new activity
    if (!xarVar::fetch('activityId', 'int', $data['activityId'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!empty($data['activityId'])) {
        $data['activity']->getItem(['itemid' => $data['activityId']]);
    }

    $activity  = \Galaxia\Api\WorkflowActivity::get($data['activityId']);
    /*    if ($_REQUEST["activityId"]) {
            $info = array('name'            => $activity->getName(),
                          'description'     => $activity->getDescription(),
                          'activityId'      => $activity->getActivityId(),
                          'isInteractive'   => $activity->isInteractive() ? 1 : 0,
                          'isAutoRouted'    => $activity->isAutoRouted() ? 1 :0,
                          'type'            => $activity->getType()
                          );

        } else {
            $info = array('name' => '',
                          'description' => '',
                          'activityId' => 0,
                          'isInteractive' => 1,
                          'isAutoRouted' => 0,
                          'type' => 'activity'
                          );
        }

        $data['info'] =  $info;
    */
    // Remove a role from the activity
    if (isset($_REQUEST['remove_role']) && $data['activityId']) {
        $activity->removeRole($_REQUEST['remove_role']);
    }

    $role_to_add = 0;
    // Add a role to the process
    if (!xarVar::fetch('addrole', 'int', $data['addrole'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (isset($addrole)) {
        $data['activity']->checkInput();
        $isInteractive = (isset($_REQUEST['isInteractive']) && $_REQUEST['isInteractive'] == 'on') ? 1 : 0;
        $isAutoRouted = (isset($_REQUEST['isAutoRouted']) && $_REQUEST['isAutoRouted'] == 'on') ? 1 : 0;
        $info = ['name' => $_REQUEST['name'],
                      'description' => $_REQUEST['description'],
                      'activityId' => $data['activityId'],
                      'isInteractive' => $isInteractive,
                      'isAutoRouted' => $isAutoRouted,
                      'type' => $_REQUEST['act_type'],
                      ];

        $vars = ['name' => $_REQUEST['rolename'],'description' => ''];

        if (isset($_REQUEST["userole"]) && $_REQUEST["userole"]) {
            $activity->addRole($_REQUEST['userole']);
        } else {
            $rid = $roleManager->replace_role($data['pid'], 0, $vars);
            $activity->addRole($rid);
        }
    }

    // Delete activities
    if (isset($_REQUEST["delete_act"]) && !empty($_REQUEST["activity"])) {
        foreach (array_keys($_REQUEST["activity"])as $item) {
            $process->removeActivity($item);
        }
    }

    //---------------------------------------------

    // If we are adding an activity then add it!
    if (isset($_REQUEST['save_act'])) {
        if (!empty($data['activityId'])) {
            $oldname = $activity->getNormalizedName();
        } else {
            $oldname = '';
        }
        $data['activity']->checkInput();

        /*        $isInteractive = (isset($_REQUEST['isInteractive']) && $_REQUEST['isInteractive'] == 'on') ? 1 : 0;
                $isAutoRouted = (isset($_REQUEST['isAutoRouted']) && $_REQUEST['isAutoRouted'] == 'on') ? 1 : 0;
                $vars = array('name' => $_REQUEST['name'],
                              'description' => $_REQUEST['description'],
                              'activityId' => $data['activityId'],
                              'isInteractive' => $isInteractive,
                              'isAutoRouted' => $isAutoRouted,
                              'type' => $_REQUEST['act_type'],
                              );
        */
        $name = $data['activity']->properties['name']->value;
        if ($process->hasActivity($name) && $data['activityId'] == 0) {
            $data['msg'] =  xarML("Activity name already exists");
            return xarTpl::module('workflow', 'admin', 'errors', $data);
        }

        //--------------------------------------------- Save or create the item

        if (!empty($data['activityId'])) {
            $newaid = $data['activity']->updateItem(['oldname' => $oldname]);
        } else {
            $newaid = $data['activity']->createItem();
        }

        // FIXME: we already do this in the createItem and updateItem methods
        $activity = \Galaxia\Api\WorkflowActivity::get($newaid);

        $rid = 0;
        if (isset($_REQUEST['userole']) && $_REQUEST['userole']) {
            $rid = $_REQUEST['userole'];
        }

        if (!empty($_REQUEST['rolename'])) {
            $data['activity']->properties['name']->value = $_REQUEST['rolename'];
            $data['activity']->properties['description']->value = '';
            $rid = $roleManager->replace_role($data['pid'], 0, $vars);
        }

        if ($rid) {
            $activity->addRole($rid);
        }

        /*        // Reget
                $info = array('name'        => $activity->getName(),
                              'description' => $activity->getDescription(),
                              'activityId'  => $activity->getActivityId(),
                              'isInteractive' => $activity->isInteractive() ? 1 : 0,
                              'isAutoRouted' => $activity->isAutoRouted() ? 1 : 0,
                              'type' => $activity->getType()
                              );


                $data['activityId'] = $newaid;
                $data['info'] =  $info;
        */
        // remove transitions ????
        $activity->removeTransitions();
        if (isset($_REQUEST["add_tran_from"])) {
            foreach ($_REQUEST["add_tran_from"] as $actfrom) {
                $activityManager->add_transition($data['pid'], $actfrom, $newaid);
            }
        }
        if (isset($_REQUEST["add_tran_to"])) {
            foreach ($_REQUEST["add_tran_to"] as $actto) {
                $activityManager->add_transition($data['pid'], $newaid, $actto);
            }
        }
    }
    //---------------------------------------------

    // Get all the process roles
    $all_roles = $roleManager->list_roles($data['pid'], 0, -1, 'name_asc', '');
    $data['all_roles'] =&  $all_roles['data'];

    // Get activity roles
    $data['roles'] = [];
    if ($data['activityId']) {
        $data['roles'] = $activity->getRoles();
    }

    $where = '';
    if (isset($_REQUEST['filter'])) {
        $wheres = [];
        if ($_REQUEST['filter_type']) {
            $wheres[] = " type='" . $_REQUEST['filter_type'] . "'";
        }
        if ($_REQUEST['filter_interactive']) {
            $wheres[] = " isInteractive='" . $_REQUEST['filter_interactive'] . "'";
        }
        if ($_REQUEST['filter_autoroute']) {
            $wheres[] = " isAutoRouted='" . $_REQUEST['filter_autoroute'] . "'";
        }
        $where = implode('and', $wheres);
    }
    $data['where'] = $_REQUEST['where'] ?? $where;

    $data['sort_mode'] =  $_REQUEST['sort_mode'] ?? 'flowNum_asc';
    $data['find'] =  $_REQUEST['find'] ?? '';

    // Transitions
    if (isset($_REQUEST["delete_tran"])) {
        foreach (array_keys($_REQUEST["transition"])as $item) {
            $parts = explode("_", $item);
            // @todo replace with activity->removeTransition()
            $activityManager->remove_transition($parts[0], $parts[1]);
        }
    }

    if (isset($_REQUEST['add_trans'])) {
        $activityManager->add_transition($data['pid'], $_REQUEST['actFromId'], $_REQUEST['actToId']);
    }

    if (isset($_REQUEST['filter_tran_name']) && $_REQUEST['filter_tran_name']) {
        $transitions = $activityManager->get_process_transitions($data['pid'], $_REQUEST['filter_tran_name']);
    } else {
        $transitions = $activityManager->get_process_transitions($data['pid'], '');
    }
    $data['transitions'] =&  $transitions;
    $data['filter_tran_name'] = $_REQUEST['filter_tran_name'] ?? '';

    //Now information for activities in this process
    $activities = $activityManager->list_activities($data['pid'], 0, -1, $data['sort_mode'], $data['find'], $data['where']);

    //Now check if the activity is or not part of a transition
    if (isset($data['activityId'])) {
        for ($i=0, $na=count($activities['data']); $i < $na; $i++) {
            $id = $activities["data"][$i]['activityId'];

            $activities["data"][$i]['to']
                = $activityManager->transition_exists($data['pid'], $data['activityId'], $id) ? 1 : 0;
            $activities["data"][$i]['from']
                = $activityManager->transition_exists($data['pid'], $id, $data['activityId']) ? 1 : 0;
        }
    }

    // ---------------------------------------
    // Update all activities at once

    if (!xarVar::fetch('update_act', 'isset', $update_act, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if ($update_act) {
        for ($i=0, $na=count($activities['data']); $i < $na; $i++) {
            // Make id a bit more accessible
            $id = $activities["data"][$i]['activityId'];
            $activity = \Galaxia\Api\WorkflowActivity::get($id);

            // Is activity interactive?
            $ia = isset($_REQUEST['activity_inter'][$id]) ? 1 : 0;
            $activities["data"][$i]['isInteractive'] = $ia;
            $activity->setInteractive($ia);

            // Is activity autorouted?
            $ar = isset($_REQUEST['activity_route'][$id]) ? 1 : 0;
            $activities["data"][$i]['isAutoRouted'] = $ar;
            $activityManager->set_autorouting($data['pid'], $id, $ar);
        }
    }
    $data['items'] =& $activities['data'];

    // Validate the process
    $valid = $activityManager->validate_process_activities($data['pid']);
    $proc_info['isValid'] = $valid ? 1 : 0;

    // If its valid and requested to activate or deactivate, do so
    if ($valid) {
        xarVar::fetch('activate_proc', 'int', $activate_proc, 0, xarVar::NOT_REQUIRED);
        if ($activate_proc) {
            $process->activate();
            xarController::redirect(xarController::URL('workflow', 'admin', 'activities', ['pid' => $data['pid']]));
            return true;
        }
        xarVar::fetch('deactivate_proc', 'int', $deactivate_proc, 0, xarVar::NOT_REQUIRED);
        if ($deactivate_proc) {
            $process->deactivate();
            xarController::redirect(xarController::URL('workflow', 'admin', 'activities', ['pid' => $data['pid']]));
            return true;
        }
    }

    // @todo migrate proc_info into $process
    $data['proc_info'] =& $proc_info;
    $data['process'] =& $process;

    $data['errors'] = [];
    if (!$valid) {
        $data['errors'] = $activityManager->get_error();
    }

    // Build the new process graph based on the changes.
    $activityManager->build_process_graph($data['pid']);

    // for roles sorting
    $data['sort_mode2'] = '';

    return $data;
}
