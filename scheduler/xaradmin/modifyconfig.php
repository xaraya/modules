<?php

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function scheduler_admin_modifyconfig()
{ 
    if (!xarSecurityCheck('Adminscheduler')) return;

    $data = array();
    $data['authid'] = xarSecGenAuthKey();
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));

    $data['trigger'] = xarModGetVar('scheduler', 'trigger');
    $data['checktype'] = xarModGetVar('scheduler', 'checktype');
    $data['checkvalue'] = xarModGetVar('scheduler', 'checkvalue');

    $data['ip'] = xarServerGetVar('REMOTE_ADDR');

    $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $data['proxy'] = $data['ip'];
        $data['ip'] = preg_replace('/,.*/', '', $forwarded);
        $data['ip'] = xarVarPrepForDisplay($data['ip']);
    }
    $data['hostname'] = @gethostbyaddr($data['ip']);

    $jobs = xarModGetVar('scheduler', 'jobs');
    if (empty($jobs)) {
        $data['jobs'] = array();
    } else {
        $data['jobs'] = unserialize($jobs);
    }
    $data['jobs'][] = array(
                            'module' => '',
                            'type' => '',
                            'func' => '',
                            'interval' => '',
                            'lastrun' => '',
                            'result' => ''
                           );
    $data['lastrun'] = xarModGetVar('scheduler','lastrun');

    $modules = xarModAPIFunc('modules', 'admin', 'getlist', 
                             array('filter' => array('AdminCapable' => 1)));
    $data['modules'] = array();
    foreach ($modules as $module) {
        $data['modules'][$module['name']] = $module['displayname'];
    }
    $data['types'] = array( // don't translate API types
                           'scheduler' => 'scheduler',
                           'admin' => 'admin',
                           'user' => 'user',
                          );
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

    $hooks = xarModCallHooks('module', 'modifyconfig', 'scheduler',
                             array('module' => 'scheduler'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    } 
    // Return the template variables defined in this function
    return $data;
} 

?>
