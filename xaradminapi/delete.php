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

	if (empty($job)) {
        $msg = xarML('Job #(1) for #(2) function #(3)() in module #(4)',
                     $itemid, 'admin', 'delete', 'scheduler');
        throw new Exception($msg);
	}

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
