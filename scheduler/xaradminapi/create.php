<?php

/**
 * create a new scheduler job
 * 
 * @author mikespub
 * @param  $args ['module'] module
 * @param  $args ['type'] type
 * @param  $args ['func'] API function
 * @param  $args ['interval'] interval
 * @returns int
 * @return job id on success, void on failure
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
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
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
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                new SystemException($msg));
            return;
        }
    }
    if (empty($lastrun)) {
        $lastrun = 0;
    }
    if (empty($result)) {
        $result = '';
    }
    $jobs[] = array('module' => $module,
                    'type' => $type,
                    'func' => $func,
                    'interval' => $interval,
                    'lastrun' => $lastrun,
                    'result' => $result);
    $serialjobs = serialize($jobs);
    xarModSetVar('scheduler','jobs',$serialjobs);

    $itemid = count($jobs) - 1;

    $item = $args;
    $item['module'] = 'scheduler';
    $item['itemid'] = $itemid;
    xarModCallHooks('item', 'create', $itemid, $item);

    // Return the id of the newly created item to the calling process
    return $itemid;
}

?>
