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
 * create a new scheduler job
 *
 * @author mikespub
 * @param  $args ['module'] module
 * @param  $args ['type'] type
 * @param  $args ['func'] API function
 * @param  $args ['interval'] interval
 * @param  $args ['config'] extra configuration like params, startdate, enddate, crontab, ... (optional)
 * @param  $args ['lastrun'] lastrun (optional)
 * @param  $args ['result'] result (optional)
 * @return int job id on success, void on failure
 */
function scheduler_adminapi_create($args)
{
    extract($args);

    $invalid = array();
    if (empty($module) || !is_string($module)) {
        $invalid[] = 'module';
    }
    if (empty($type) || !is_string($type)) {
        $invalid[] = 'type';
    }
    if (empty($func) || !is_string($func)) {
        $invalid[] = 'func';
    }
    if (empty($interval) || !is_string($interval)) {
        $invalid[] = 'interval';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     join(', ', $invalid), 'admin', 'create', 'scheduler');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AdminScheduler')) return;

    $serialjobs = xarModGetVar('scheduler','jobs');
    if (empty($serialjobs)) {
        $jobs = array();
    } else {
        $jobs = unserialize($serialjobs);
    }
    foreach ($jobs as $id => $job) {
        if ($job['module'] == $module && $job['type'] == $type && $job['func'] == $func) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                         'job', 'admin', 'create', 'scheduler');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                new SystemException($msg));
            return;
        }
    }
    $maxid = xarModGetVar('scheduler','maxjobid');
    if (!isset($maxid)) {
        // re-number jobs starting from 1 and save maxid
        $maxid = 0;
        $newjobs = array();
        foreach ($jobs as $job) {
            $maxid++;
            $newjobs[$maxid] = $job;
        }
        $jobs = $newjobs;
    }
    $maxid++;
    xarModSetVar('scheduler','maxjobid',$maxid);
    if (empty($config)) {
        $config = array();
    }
    if (empty($lastrun)) {
        $lastrun = 0;
    }
    if (empty($result)) {
        $result = '';
    }
    $jobs[$maxid] = array('module' => $module,
                          'type' => $type,
                          'func' => $func,
                          'interval' => $interval,
                          'config' => $config,
                          'lastrun' => $lastrun,
                          'result' => $result);
    $serialjobs = serialize($jobs);
    xarModSetVar('scheduler','jobs',$serialjobs);

    $itemid = $maxid;

    $item = $args;
    $item['module'] = 'scheduler';
    $item['itemid'] = $itemid;
    xarModCallHooks('item', 'create', $itemid, $item);

    // Return the id of the newly created item to the calling process
    return $itemid;
}

?>
