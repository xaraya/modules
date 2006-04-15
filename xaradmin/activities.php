<?php

/**
 * the activities administration function
 * 
 * @author mikespub
 * @access public 
 */
function workflow_admin_activities()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

    // Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $data = array();
    
    // Adapted from tiki-g-admin_activities.php
    include_once(GALAXIA_LIBRARY.'/ProcessManager.php');
    
    if (!isset($_REQUEST['pid'])) {
        $data['msg'] =  xarML("No process indicated");
        return xarTplModule('workflow', 'admin', 'error', $data);
    }
    $data['pid'] =  $_REQUEST['pid'];
    
    $proc_info = $processManager->get_process($data['pid']);
    $proc_info['graph']=GALAXIA_PROCESSES."/".$proc_info['normalized_name']."/graph/".$proc_info['normalized_name'].".png";
    
    // Retrieve activity info if we are editing, assign to 
    // default values when creating a new activity
    if (!isset($_REQUEST['activityId']))
        $_REQUEST['activityId'] = 0;
    
    if ($_REQUEST["activityId"]) {
        $info = $activityManager->get_activity($data['pid'], $_REQUEST["activityId"]);
    } else {
        $info = array('name' => '',
                      'description' => '',
                      'activityId' => 0,
                      'isInteractive' => 'y',
                      'isAutoRouted' => 'n',
                      'type' => 'activity'
                      );
    }
    
    $data['activityId'] =  $_REQUEST['activityId'];
    $data['info'] =  $info;
    
    // Remove a role from the activity
    if (isset($_REQUEST['remove_role']) && $_REQUEST['activityId']) {
        $activityManager->remove_activity_role($_REQUEST['activityId'], $_REQUEST['remove_role']);
    }
    
    $role_to_add = 0;
    // Add a role to the process
    if (isset($_REQUEST['addrole'])) {
        $isInteractive = (isset($_REQUEST['isInteractive']) && $_REQUEST['isInteractive'] == 'on') ? 'y' : 'n';
        $isAutoRouted = (isset($_REQUEST['isAutoRouted']) && $_REQUEST['isAutoRouted'] == 'on') ? 'y' : 'n';
        $info = array('name' => $_REQUEST['name'],
                      'description' => $_REQUEST['description'],
                      'activityId' => $_REQUEST['activityId'],
                      'isInteractive' => $isInteractive,
                      'isAutoRouted' => $isAutoRouted,
                      'type' => $_REQUEST['act_type'],
                      );
        
        $vars = array('name' => $_REQUEST['rolename'],'description' => '');
        
        if (isset($_REQUEST["userole"]) && $_REQUEST["userole"]) {
            if ($_REQUEST['activityId']) {
                $activityManager->add_activity_role($_REQUEST['activityId'], $_REQUEST["userole"]);
            }
        } else {
            $rid = $roleManager->replace_role($data['pid'], 0, $vars);
            if ($_REQUEST['activityId']) {
                $activityManager->add_activity_role($_REQUEST['activityId'], $rid);
            }
        }
    }
    
    // Delete activities
    if (isset($_REQUEST["delete_act"])) {
        foreach (array_keys($_REQUEST["activity"])as $item) {
            $activityManager->remove_activity($data['pid'], $item);
        }
    }
    
    // If we are adding an activity then add it!
    if (isset($_REQUEST['save_act'])) {
        $isInteractive = (isset($_REQUEST['isInteractive']) && $_REQUEST['isInteractive'] == 'on') ? 'y' : 'n';
        $isAutoRouted = (isset($_REQUEST['isAutoRouted']) && $_REQUEST['isAutoRouted'] == 'on') ? 'y' : 'n';
        $vars = array('name' => $_REQUEST['name'],
                      'description' => $_REQUEST['description'],
                      'activityId' => $_REQUEST['activityId'],
                      'isInteractive' => $isInteractive,
                      'isAutoRouted' => $isAutoRouted,
                      'type' => $_REQUEST['act_type'],
                      );
        
        if ($activityManager->activity_name_exists($data['pid'], $_REQUEST['name']) && $_REQUEST['activityId'] == 0) {
            $data['msg'] =  xarML("Activity name already exists");
            return xarTplModule('workflow', 'admin', 'error', $data);
        }
        
        $newaid = $activityManager->replace_activity($data['pid'], $_REQUEST['activityId'], $vars);

        $rid = 0;
        if (isset($_REQUEST['userole']) && $_REQUEST['userole'])
            $rid = $_REQUEST['userole'];
        
        if (!empty($_REQUEST['rolename'])) {
            $vars = array('name' => $_REQUEST['rolename'],
                          'description' => ''
                          );
            $rid = $roleManager->replace_role($data['pid'], 0, $vars);
        }
        
        if ($rid) {
            $activityManager->add_activity_role($newaid, $rid);
        }
        
        // Reget 
        $info = $activityManager->get_activity($data['pid'], $newaid);
        
        $_REQUEST['activityId'] = $newaid;
        $data['info'] =  $info;
        // remove transitions ????
        $activityManager->remove_activity_transitions($data['pid'], $newaid);
        
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
    
    // Get all the process roles
    $all_roles = $roleManager->list_roles($data['pid'], 0, -1, 'name_asc', '');
    $data['all_roles'] =&  $all_roles['data'];
    
    // Get activity roles
    $data['roles'] = array();
    if ($_REQUEST['activityId']) {
        $data['roles'] = $activityManager->get_activity_roles($_REQUEST['activityId']);
    }
    
    $where = '';
    if (isset($_REQUEST['filter'])) {
        $wheres = array();
        if ($_REQUEST['filter_type'])        $wheres[] = " type='" . $_REQUEST['filter_type'] . "'";
        if ($_REQUEST['filter_interactive']) $wheres[] = " isInteractive='" . $_REQUEST['filter_interactive'] . "'";
        if ($_REQUEST['filter_autoroute'])   $wheres[] = " isAutoRouted='" . $_REQUEST['filter_autoroute'] . "'";
        $where = implode('and', $wheres);
    }
    $data['where'] = isset($_REQUEST['where']) ? $_REQUEST['where'] : $where;
    
    $data['sort_mode'] =  isset($_REQUEST['sort_mode']) ? $_REQUEST['sort_mode'] : 'flowNum_asc';
    $data['find'] =  isset($_REQUEST['find']) ? $_REQUEST['find'] : '';
    
    // Transitions
    if (isset($_REQUEST["delete_tran"])) {
        foreach (array_keys($_REQUEST["transition"])as $item) {
            $parts = explode("_", $item);
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
    $data['filter_tran_name'] = isset($_REQUEST['filter_tran_name']) ? $_REQUEST['filter_tran_name'] : '';
    
    // Validate the process
    $valid = $activityManager->validate_process_activities($data['pid']);
    $proc_info['isValid'] = $valid ? 'y' : 'n';
    
    // If its valid and requested to activate, do so
    if ($valid && isset($_REQUEST['activate_proc'])) {
        $processManager->activate_process($data['pid']);
        $proc_info['isActive'] = 'y';
    }
    
    // If its not valid or requested to deactivate, deactivate the process
    if (!$valid || isset($_REQUEST['deactivate_proc'])) {
        $processManager->deactivate_process($data['pid']);
        $proc_info['isActive'] = 'n';
    }
    $data['proc_info'] =&  $proc_info;
    
    $data['errors'] = array();
    if (!$valid) $data['errors'] = $activityManager->get_error();
    
    //Now information for activities in this process
    $activities = $activityManager->list_activities($data['pid'], 0, -1,  $data['sort_mode'], $data['find'], $data['where']);
    
    //Now check if the activity is or not part of a transition
    if (isset($_REQUEST['activityId'])) {
        for ($i=0, $na=count($activities['data']); $i < $na; $i++) {
            $id = $activities["data"][$i]['activityId'];
            
            $activities["data"][$i]['to']
                = $activityManager->transition_exists($data['pid'], $_REQUEST['activityId'], $id) ? 'y' : 'n';
            $activities["data"][$i]['from']
                = $activityManager->transition_exists($data['pid'], $id, $_REQUEST['activityId']) ? 'y' : 'n';
        }
    }
    
    // Set activities
    if (isset($_REQUEST['update_act'])) {
        for ($i=0, $na=count($activities['data']); $i < $na; $i++) {
            // Make id a bit more accessible
            $id = $activities["data"][$i]['activityId'];
            
            // Is activity interactive?
            $ia = isset($_REQUEST['activity_inter'][$id]) ? 'y' : 'n';
            $activities["data"][$i]['isInteractive'] = $ia;
            $activityManager->set_interactivity($data['pid'], $id, $ia);
            
            // Is activity autorouted?
            $ar = isset($_REQUEST['activity_route'][$id]) ? 'y' : 'n';
            $activities["data"][$i]['isAutoRouted'] = $ar;
            $activityManager->set_autorouting($data['pid'], $id, $ar);
        }
    }
    $data['items'] =& $activities['data'];
    
    // Build the new process graph based on the changes.
    $activityManager->build_process_graph($data['pid']);
    
    // unknown variables ?
    $data['where2'] = '';
    $data['find2'] = '';
    $data['sort_mode2'] = '';
    
    $data['feature_help'] = $feature_help;
    $data['direct_pagination'] = $direct_pagination;
    return $data;
}

?>
