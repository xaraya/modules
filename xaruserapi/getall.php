<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * get information about all scheduler jobs
 *
 * @author mikespub
 * @param string $args['module']: module name +
 * @param string $args['functype']: api type +
 * @param string $args['func']: function name, or
 * @param int    $args['trigger']: 0: disabled, 1: external, 2: block, 3: event
 * @return array of jobs and their info
 */
function scheduler_userapi_getall($args)
{
    extract($args);

    $where = array();
    $bindvars = array();

    if (isset($trigger) && in_array($trigger, array(0,1,2,3))) {
        $where[] = " trigger = ?";
        $bindvars = $trigger;
    } elseif (isset($module) && isset($functype) && isset($func)) {
        $where[] = " module = ?";
        $bindvars = $module;
        $where[] = " functype = ?";
        $bindvars = $functype;
        $where[] = " func = ?";
        $bindvars = $func;
    }


    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    // Get database information
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $prefix = xarDB::getPrefix();

    $table = $xartable['scheduler_jobs'];

    $query = "SELECT
                id,
                job_trigger,
                checktype,
                lastrun,
                job_interval,
                module,
                functype,
                func,
                result,
                checkvalue,
                config
            FROM $table";

    if(count($where) > 0) {
        $query .= " WHERE " . implode(' AND ', $where);
    }

    $stmt = $dbconn->prepareStatement($query);

    $result = $stmt->executeQuery($bindvars,ResultSet::FETCHMODE_ASSOC);

    $jobs = array();

    while($result->next()) {
        $job = $result->fields;
        if ($job['config'] != '') {
            $job['config'] = unserialize($job['config']);
        }
        $jobs[$job['id']] = $job;
    }

    return $jobs;
}

?>
