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
 * create a new scheduler job
 *
 * @author mikespub
 * @param  $args ['module'] module
 * @param  $args ['functype'] type
 * @param  $args ['func'] API function
 * @param  $args ['job_interval'] interval
 * @param  $args ['config'] extra configuration like params, startdate, enddate, crontab, ... (optional)
 * @param  $args ['lastrun'] lastrun (optional)
 * @param  $args ['result'] result (optional)
 * @return int job id on success, void on failure
 */
function scheduler_adminapi_create($args)
{
    extract($args);

    $invalid = array();
    $modules = xarModAPIFunc('modules', 'admin', 'getlist',
                             array('filter' => array('AdminCapable' => 1)));

    $modnames = array();

    foreach ($modules as $mod) {
        $modnames[] = $mod['name'];
    }

    if (empty($module) || !in_array($module, $modnames)) {
        $invalid[] = 'module';
    }
    if (empty($functype) || !in_array($functype, array('scheduler','admin','user'))) {
        $invalid[] = 'functype';
    }

    if (empty($func) || !is_string($func)) {
        $invalid[] = 'func';
    }

    $triggers = xarModAPIFunc('scheduler','user','triggers');
    if (!isset($job_trigger)) {
        $invalid[] = 'trigger';
    } elseif (!is_numeric($job_trigger) && !isset($triggers[$job_trigger])) {
        $invalid[] = 'trigger';
    }

    $intervals = xarModAPIFunc('scheduler','user','intervals');
    if (empty($job_interval) || !isset($intervals[$job_interval])) {
        $invalid[] = 'job_interval';
    }

    $checktypes = xarmodAPIFunc('scheduler','user','sources');
    if (!empty($checktype) && !isset($checktype, $checktypes)) {
        $invalid[] = 'checktype';
    }

    $serialjobs = xarModVars::get('scheduler','jobs');
    if (empty($serialjobs)) {
        $jobs = array();
    } else {
        $jobs = unserialize($serialjobs);
    }

    if (!isset($result)) {
        $result = '';
    }
    $maxid = xarModVars::get('scheduler','maxjobid');
    if (!isset($maxid)) {
        // re-number jobs starting from 1 and save maxid
        $maxid = 0;
        $newjobs = array();
        foreach ($jobs as $job) {
            $maxid++;
            $newjobs[$maxid] = $job;
        }
        if(!isset($config['startdate']) || !is_numeric($config['startdate'])) {
            $config['startdate'] = '';
        }
        if(!isset($config['enddate']) || !is_numeric($config['enddate'])) {
            $config['enddate'] = '';
        }
        if(!isset($config['crontab']) || !is_array($config['crontab'])) {
            $config['crontab'] = array('minute' => '',
                'hour' => '',
                'day' => '',
                'month' => '',
                'weekday' => '',
                'nextrun' => 0
            );
        } else {
            if(!isset($config['minute']) || !is_string($config['minute'])) {
                $config['minute'] = '';
            }
            if(!isset($config['hour']) || !is_string($config['hour'])) {
                $config['hour'] = '';
            }
            if(!isset($config['day']) || !is_string($config['day'])) {
                $config['day'] = '';
            }
            if(!isset($config['month']) || !is_string($config['month'])) {
                $config['month'] = '';
            }
            if(!isset($config['weekday']) || !is_string($config['weekday'])) {
                $config['weekday'] = '';
            }
            if(!isset($config['nextrun']) || !is_numeric($config['nextrun'])) {
                $config['nextrun'] = 0;
            }
        }
    }
    $maxid++;
    xarModVars::set('scheduler','maxjobid',$maxid);
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
                          'type' => $functype,
                          'func' => $func,
                          'interval' => $interval,
                          'config' => $config,
                          'lastrun' => $lastrun,
                          'result' => $result);
    $serialjobs = serialize($jobs);
    xarModVars::set('scheduler','jobs',$serialjobs);

    $config = serialize($config);

    // Load up database details.
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $table = $xartable['scheduler_jobs'];

    $query = "INSERT INTO $table (
                    job_trigger,
                    checktype,
                    lastrun,
                    job_interval,
                    module,
                    functype,
                    func,
                    result,
                    checkvalue,
                    config)
                VALUES (?,?,?,?,?,?,?,?,?,?)";

    $bindvars = array($job_trigger, $checktype, $lastrun, $job_interval, $module, $functype, $func, $result, $checkvalue, $config);

    $dbconn->Execute($query, $bindvars);

    // Get ID of row inserted.
    $itemid = $dbconn->getLastId($table);

    xarModVars::set('scheduler','running.' . $itemid, 0);

    $item = $args;
    $item['module'] = 'scheduler';
    $item['itemid'] = $itemid;
    xarModCallHooks('item', 'create', $itemid, $item);

    // Return the id of the newly created item to the calling process
    return $itemid;
}

?>
