<?php

/**
 * Update the configuration parameters of the module based on data from the modification form
 * 
 * @author mikespub
 * @access public 
 * @param no $ parameters
 * @return true on success or void on failure
 * @throws no exceptions
 * @todo nothing
 */
function workflow_admin_modifyconfig()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

    $data = array();
    $data['settings'] = array();

    $create = xarModGetVar('workflow','default.create');
    $update = xarModGetVar('workflow','default.update');
    $delete = xarModGetVar('workflow','default.delete');
    $data['settings']['default'] = array('label' => xarML('Default configuration'),
                                         'create' => $create,
                                         'update' => $update,
                                         'delete' => $delete);

    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'workflow'));
    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                // Get the list of all item types for this module (if any)
                $mytypes = xarModAPIFunc($modname,'user','getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         array(), 0);
                foreach ($value as $itemtype => $val) {
                    $create = xarModGetVar('workflow', "$modname.$itemtype.create");
                    if (empty($create)) {
                        $create = '';
                    }
                    $update = xarModGetVar('workflow', "$modname.$itemtype.update");
                    if (empty($update)) {
                        $update = '';
                    }
                    $delete = xarModGetVar('workflow', "$modname.$itemtype.delete");
                    if (empty($delete)) {
                        $delete = '';
                    }
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                        $link = $mytypes[$itemtype]['url'];
                    } else {
                        $type = xarML('type #(1)',$itemtype);
                        $link = xarModURL($modname,'user','view',array('itemtype' => $itemtype));
                    }
                    $data['settings']["$modname.$itemtype"] = array('label' => xarML('Configuration for #(1) module - <a href="#(2)">#(3)</a>', $modname, $link, $type),
                                                                    'create' => $create,
                                                                    'update' => $update,
                                                                    'delete' => $delete);
                }
            } else {
                $create = xarModGetVar('workflow', "$modname.create");
                if (empty($create)) {
                    $create = '';
                }
                $update = xarModGetVar('workflow', "$modname.update");
                if (empty($update)) {
                    $update = '';
                }
                $delete = xarModGetVar('workflow', "$modname.delete");
                if (empty($delete)) {
                    $delete = '';
                }
                $link = xarModURL($modname,'user','main');
                $data['settings'][$modname] = array('label' => xarML('Configuration for <a href="#(1)">#(2)</a> module', $link, $modname),
                                                    'create' => $create,
                                                    'update' => $update,
                                                    'delete' => $delete);
            }
        }
    }
    $data['isalias'] = xarModGetVar('workflow','SupportShortURLs');
    $data['numitems'] = xarModGetVar('workflow','itemsperpage');
    if (empty($data['numitems'])) {
        $data['numitems'] = 20;
    }

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    include_once (GALAXIA_LIBRARY.'/ProcessMonitor.php');

    // get all start activities that are not interactive
    $activities = $processMonitor->monitor_list_activities(0, -1, 'pId_asc', '', "type='start' and isInteractive='n'");

    // get the name of all processes
    $all_procs = $processMonitor->monitor_list_all_processes('pId_asc', "isActive='y'");
    $pid2name = array();
    foreach ($all_procs as $info) {
        $pid2name[$info['pId']] = $info['name'] . ' ' . $info['version'];
    }

    // build a list of activity ids and names
    $data['activities'] = array();
    $data['activities'][0] = '';
    foreach ($activities['data'] as $info) {
        if (isset($pid2name[$info['pId']])) {
            $data['activities'][$info['activityId']] = $pid2name[$info['pId']] . ' - ' . $info['name'];
        }
    }

    // get all stand-alone activities that are not interactive
    $activities = $processMonitor->monitor_list_activities(0, -1, 'pId_asc', '', "type='standalone' and isInteractive='n'");

    // build a list of activity ids and names
    $data['standalone'] = array();
    foreach ($activities['data'] as $info) {
        if (isset($pid2name[$info['pId']])) {
            $data['standalone'][$info['activityId']] = $pid2name[$info['pId']] . ' - ' . $info['name'];
        }
    }

// We need to keep track of our own set of jobs here, because the scheduler won't know what
// workflow activities to run when. Other modules will typically have 1 job that corresponds
// to 1 API function, so they won't need this...

    $serialjobs = xarModGetVar('workflow','jobs');
    if (!empty($serialjobs)) {
        $data['jobs'] = unserialize($serialjobs);
    } else {
        $data['jobs'] = array();
    }
    $data['jobs'][] = array('activity' => '',
                            'interval' => '',
                            'lastrun' => '',
                            'result' => '');

    $data['intervals'] = array(
                               '1h' => xarML('every hour'),
                               '2h' => xarML('every #(1) hours',2),
                               '3h' => xarML('every #(1) hours',3),
                               '4h' => xarML('every #(1) hours',4),
                               '5h' => xarML('every #(1) hours',5),
                               '6h' => xarML('every #(1) hours',6),
                               '6h' => xarML('every #(1) hours',6),
                               '8h' => xarML('every #(1) hours',8),
                               '9h' => xarML('every #(1) hours',9),
                               '10h' => xarML('every #(1) hours',10),
                               '11h' => xarML('every #(1) hours',11),
                               '12h' => xarML('every #(1) hours',12),
                               '1d' => xarML('every day'),
                               '2d' => xarML('every #(1) days',2),
                               '3d' => xarML('every #(1) days',3),
                               '4d' => xarML('every #(1) days',4),
                               '5d' => xarML('every #(1) days',5),
                               '6d' => xarML('every #(1) days',6),
                               '1w' => xarML('every week'),
                               '2w' => xarML('every #(1) weeks',2),
                               '3w' => xarML('every #(1) weeks',3),
                               '1m' => xarML('every month'),
                               '2m' => xarML('every #(1) months',2),
                               '3m' => xarML('every #(1) months',3),
                               '4m' => xarML('every #(1) months',4),
                               '5m' => xarML('every #(1) months',5),
                               '6m' => xarML('every #(1) months',6),
                              );

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
