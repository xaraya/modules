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
 * get information about a scheduler job
 *
 * @author mikespub
 * @param  $args ['module'] module +
 * @param  $args ['type'] type +
 * @param  $args ['func'] API function, or
 * @param  $args ['itemid'] job id
 * @return array of job info on success, void on failure
 */
function scheduler_userapi_get($args)
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
                     join(', ', $invalid), 'user', 'get', 'scheduler');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $serialjobs = xarModGetVar('scheduler','jobs');
    if (empty($serialjobs)) {
        $jobs = array();
    } else {
        $jobs = unserialize($serialjobs);
    }
    if (isset($itemid)) {
        if (!isset($jobs[$itemid])) {
            return; // no exception here
        }
    } else {
        foreach ($jobs as $id => $job) {
            if ($job['module'] == $module && $job['type'] == $type && $job['func'] == $func) {
                $itemid = $id;
                break;
            }
        }
        if (!isset($itemid)) {
            return; // no exception here
        }
    }

    // Return the job information
    return $jobs[$itemid];
}

?>
