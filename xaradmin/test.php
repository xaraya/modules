<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 */
/**
 * Test a scheduler job
 *
 * @author mikespub
 * @param  $args ['itemid'] job id
 * @return true on success, void on failure
 */
function scheduler_admin_test()
{
    // Get parameters
    if (!xarVarFetch('itemid', 'id', $itemid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) {return;}

    if (empty($itemid)) return xarResponse::NotFound();
    
    // Security Check
    if (!xarSecurityCheck('AdminScheduler')) return;

    // Check for confirmation
    if (empty($confirm)) {
        // No confirmation yet - display the confirmation page
        $data['itemid'] = $itemid;
        return $data;
    }

    // Confirm Auth Key
    if (!xarSecConfirmAuthKey()) {return;}

    // Get the job details
    sys::import('modules.dynamicdata.class.objects.master');
    $job = DataObjectMaster::getObject(array('name' => 'scheduler_jobs'));
    $job->getItem(array('itemid' => $itemid));
    
    // Run the job
    $result = xarMod::apiFunc(
                        $job->properties['module']->value,
                        $job->properties['type']->value,
                        $job->properties['function']->value
                            );
                            
    // Go back to the view page
    xarController::redirect(xarModURL('scheduler', 'admin', 'view'));
    return true;
}

?>