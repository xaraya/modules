<?php

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function scheduler_admin_modifyconfig()
{ 
    if (!xarSecurityCheck('AdminScheduler')) return;

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
    $data['intervals'] = xarModAPIFunc('scheduler','user','intervals');

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