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
 * Update a scheduler job
 * @param array job
 */
function scheduler_admin_update($args)
{
    if (!xarSecurityCheck('AdminScheduler')) return;

    $extract($args);

    $oldjob = xarModAPIFunc('scheduler','user','get',array('itemid' => $id));

    if(!is_array($old_job)) {
        return;
    }

    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    // Get database information
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $prefix = xarDB::getPrefix();

    $table = $xartable['scheduler_jobs'];

    $bindvars = array(
                    $module,
                    $functype,
                    $func,
                    $job_interval,
                    $lastrun,
                    $result,
                    $job_trigger,
                    $checktype,
                    $checkvalue
                );

    $query = "UPDATE $table SET
                module=?,
                functype=?,
                func=?,
                job_interval=?,
                lastrun=?,
                result=?,
                job_trigger=?,
                checktype=?,
                checkvalue=?";

    if(is_array($config)) {
        $query .", config=?";
        $bindvars[] = serialize($config);
    }

    $query .= " WHERE id=?";
    $bindvars[] = $id;

    $result = $dbconn->Execute($sql,$bindvars);

    if (!$result) {
        return;
    }

    xarResponseRedirect(xarModURL('scheduler', 'admin', 'modifyconfig'));

    return true;
}
?>
