<?php

/**
 * delete a scheduler job
 * 
 * @author mikespub
 * @param  $args ['module'] module +
 * @param  $args ['type'] type +
 * @param  $args ['func'] API function, or
 * @param  $args ['itemid'] job id
 * @returns int
 * @return job id on success, void on failure
 */
function scheduler_adminapi_delete($args)
{
    extract($args); 

    $invalid = array();
    if (isset($itemid)) {
        if (!is_numeric($itemid)) {
            $invalid[] = 'item id';
        }
    } else {
        if (empty($module) || !is_string($module)) {
            $invalid[] = 'module';
        } 
        if (empty($type) || !is_string($type)) {
            $invalid[] = 'type';
        } 
        if (empty($func) || !is_string($func)) {
            $invalid[] = 'func';
        }
    } 
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     join(', ', $invalid), 'admin', 'delete', 'scheduler');
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
    if (isset($itemid)) {
        if (!isset($jobs[$itemid])) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                         'job id', 'admin', 'delete', 'scheduler');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                new SystemException($msg));
            return;
        }
    } else {
        foreach ($jobs as $id => $job) {
            if ($job['module'] == $module && $job['type'] == $type && $job['func'] == $func) {
                $itemid = $id;
                break;
            }
        }
        if (!isset($itemid)) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                         'job', 'admin', 'delete', 'scheduler');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                new SystemException($msg));
            return;
        }
    }
    unset($jobs[$itemid]);
    $serialjobs = serialize($jobs);
    xarModSetVar('scheduler','jobs',$serialjobs);

    $item = $args;
    $item['module'] = 'scheduler';
    $item['itemid'] = $itemid;
    xarModCallHooks('item', 'delete', $itemid, $item);

    // Return the id of the deleted item to the calling process
    return $itemid;
}

?>
