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
function scheduler_admin_delete()
{
    // Get parameters
    if (!xarVarFetch('itemid', 'id', $itemid)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) {return;}

    // Security Check
    if (!xarSecurityCheck('AdminScheduler')) return;

    // Check for confirmation
    if (empty($confirm)) {
        // No confirmation yet - get one

        $job = xarmodAPIFunc('scheduler','user','get',array('itemid'=>$itemid));

        if (empty($job)) {
            $msg = xarML('Job #(1) for #(2) function #(3)() in module #(4)',
                         $itemid, 'admin', 'delete', 'scheduler');
            throw new Exception($msg);
        }

        $job['authid'] = xarSecGenAuthKey();
        $job['triggers'] = xarModAPIFunc('scheduler','user','triggers');
        return $job;
    }

    // Confirm Auth Key
    if (!xarSecConfirmAuthKey()) {return;}

    // Pass to API
    xarModAPIFunc('scheduler', 'admin', 'delete',array('itemid' => $itemid));

    xarResponseRedirect(xarModURL('scheduler', 'admin', 'modifyconfig'));

    return true;
}

?>
