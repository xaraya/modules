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
 * update a scheduler job
 *
 * @author mikespub
 * @param  $args ['module'] module
 * @param  $args ['functype'] type
 * @param  $args ['func'] API function
 * @param  $args ['itemid'] job id
 * @param  $args ['job_interval'] interval (optional)
 * @param  $args ['config'] extra configuration like params, startdate, enddate, crontab, ... (optional)
 * @param  $args ['lastrun'] lastrun (optional)
 * @param  $args ['result'] result (optional)
 * @return int true success, void on failure
 */
function scheduler_adminapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }

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

    if (!empty($checktype) && !is_string($checkvalue)) {
        $invalid[] = 'checkvalue';
    }

    if (!isset($result)) {
        $result = '';
    }

    if (!isset($lastrun) || !is_numeric($lastrun)) {
        $lastrun = 0;
    }

    if(!is_array($config)) {
        $config = '';
    } else {
        if(!isset($config['params']) || !is_string($config['params'])) {
            $config['params'] = '';
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

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     join(', ', $invalid), 'admin', 'update', 'scheduler');
        throw new BadParameterException($msg);
    }

    if (!xarSecurityCheck('AdminScheduler')) return;

    $job = xarModAPIFunc('scheduler','user','get', array('itemid' => $id));

    if (empty($job)) {
        $msg = xarML('Invalid itemid for #(2) function #(3)() in module #(4)',
                     'admin', 'update', 'scheduler');
        throw new BadParameterException($msg);
    }

    $config = serialize($config);

    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    // Get database information
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $prefix = xarDB::getPrefix();

    $table = $xartable['scheduler_jobs'];

    $query = "UPDATE $table SET
                    job_trigger=?,
                    checktype=?,
                    lastrun=?,
                    job_interval=?,
                    module=?,
                    functype=?,
                    func=?,
                    result=?,
                    checkvalue=?,
                    config=?
                WHERE id=?";

    $bindvars = array(
                    $job_trigger,
                    $checktype,
                    $lastrun,
                    $job_interval,
                    $module,
                    $functype,
                    $func,
                    $result,
                    $checkvalue,
                    $config,
                    $id);

    try {
        $dbconn->begin();
        $stmt = $dbconn->prepareStatement($query);     
        $stmt->executeUpdate($bindvars);

        $item = $args;
        $item['module'] = 'scheduler';
        $item['itemid'] = $id;
        xarModCallHooks('item', 'update', $id, $item);

        $dbconn->commit();
    } catch (SQLException $e) {
        $dbconn->rollback();                
        throw $e;              
    }

    return true;
}

?>
