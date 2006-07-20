<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
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
    $maxid = xarModGetVar('scheduler','maxjobid');
    if (!isset($maxid)) {
        // re-number jobs starting from 1 and save maxid
        $maxid = 0;
        $newjobs = array();
        foreach ($data['jobs'] as $job) {
            $maxid++;
            $newjobs[$maxid] = $job;
        }
        xarModSetVar('scheduler','maxjobid',$maxid);
        $serialjobs = serialize($newjobs);
        xarModSetVar('scheduler','jobs',$serialjobs);
        $data['jobs'] = $newjobs;
    }

    if (!xarVarFetch('addjob','str',$addjob,'',XARVAR_NOT_REQUIRED)) return;
    if (!empty($addjob) && preg_match('/^(\w+);(\w+);(\w+)$/',$addjob,$matches)) {
        $maxid++;
        xarModSetVar('scheduler','maxjobid',$maxid);
        $data['jobs'][$maxid] = array(
                                      'module' => $matches[1],
                                      'type' => $matches[2],
                                      'func' => $matches[3],
                                      'interval' => '',
                                      'config' => array(),
                                      'lastrun' => '',
                                      'result' => ''
                                     );
    }
    $data['jobs'][0] = array(
                             'module' => '',
                             'type' => '',
                             'func' => '',
                             'interval' => '',
                             'config' => array(),
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
