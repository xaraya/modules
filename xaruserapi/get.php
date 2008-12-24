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
 * get information about a scheduler job
 *
 * @author mikespub
 * @param  $args ['module'] module +
 * @param  $args ['functype'] type +
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
        if (empty($functype) || !is_string($functype)) {
            $invalid[] = 'type';
        }
        if (empty($func) || !is_string($func)) {
            $invalid[] = 'func';
        }
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     join(', ', $invalid), 'user', 'get', 'scheduler');
        throw new BadParameterException($msg);
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
                module,
                functype,
                func,
                job_interval,
                lastrun,
                result,
                job_trigger,
                checktype,
                checkvalue,
                config
            FROM $table";

    $bindvars = array();
    $where = array();

    if (isset($itemid)) {
        $where[] = "id = ?";
        $bindvars[] = $itemid;
    } elseif (isset($module) && isset($functype) && isset($func)) {
        $where[] = " module = ?";
        $bindvars = $module;
        $where[] = " functype = ?";
        $bindvars = $functype;
        $where[] = " func = ?";
        $bindvars = $func;
    }

    if(count($where) > 0) {
        $query .= " WHERE " . implode(' AND ', $where);
    }

    $stmt = $dbconn->prepareStatement($query);
    $stmt->setLimit(1);

    $result = $stmt->executeQuery($bindvars,ResultSet::FETCHMODE_ASSOC);

    $jobs = array();

    while($result->next()) {
        $job = $result->fields;
        if ($job['config'] != '') {
            $job['config'] = unserialize($job['config']);
        }
        $jobs[$job['id']] = $job;
    }

    // Return the job information
    return $jobs[$itemid];
}

?>
