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
 * delete a scheduler job
 *
 * @author mikespub
 * @param  $args ['module'] module +
 * @param  $args ['type'] type +
 * @param  $args ['func'] API function, or
 * @param  $args ['itemid'] job id
 * @return int job id on success, void on failure
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
        throw new Exception($msg);
    }

    if (!xarSecurityCheck('AdminScheduler')) return;

    $serialjobs = xarModVars::get('scheduler','jobs');
    if (empty($serialjobs)) {
        $jobs = array();
    } else {
        $jobs = unserialize($serialjobs);
    }
    if (isset($itemid)) {
        if (!isset($jobs[$itemid])) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                         'job id', 'admin', 'delete', 'scheduler');
            throw new Exception($msg);
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
            throw new Exception($msg);
        }
    }
    unset($jobs[$itemid]);
    $serialjobs = serialize($jobs);
    xarModVars::set('scheduler','jobs',$serialjobs);

    $item = $args;
    $item['module'] = 'scheduler';
    $item['itemid'] = $itemid;
    xarModCallHooks('item', 'delete', $itemid, $item);

    // Return the id of the deleted item to the calling process
    return $itemid;
}

?>
