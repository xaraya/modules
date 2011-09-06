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
 * delete a scheduler job
 *
 * @author mikespub
 * @param  $args ['itemid'] job id
 * @return true on success, void on failure
 */
function scheduler_adminapi_delete($args)
{
    if (!xarSecurityCheck('AdminScheduler')) return;

    extract($args);

    $invalid = array();
    if (isset($itemid)) {
        if (!is_numeric($itemid)) {
            $invalid[] = 'item id';
        }
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     join(', ', $invalid), 'admin', 'delete', 'scheduler');
        throw new BadParameterException($msg);
    }

    $job = xarmodAPIFunc('scheduler','user','get',array('itemid'=>$itemid));

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
    xarModVars::set('scheduler','jobs',$serialjobs);

    $running = xarModVars::get('scheduler', 'running.' . $itemid);

    if($running == 1 || $job['job_trigger'] != 0) {
        $msg = xarML('Job #(1) must be disabled and not running to allow deletion.',
                     $itemid);
        throw new Exception($msg);
    }   
    
    // Load up database details.
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $table = $xartable['scheduler_jobs'];

    $query = "DELETE FROM $table WHERE id=?";
    
    $result = $dbconn->Execute($query,array($itemid));

    xarModVars::delete('scheduler','running.' . $itemid);

    return true;
}

?>
