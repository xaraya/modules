<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * Update configuration
 */
function workflow_admin_updateconfig()
{
    // Get parameters
    xarVarFetch('settings', 'isset',    $settings, '', XARVAR_DONT_SET);

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

    if (!xarVarFetch('jobs','isset',$jobs,array(),XARVAR_NOT_REQUIRED)) return;
    if (empty($jobs)) {
        $jobs = array();
    }
    $savejobs = array();
    foreach ($jobs as $job) {
        if (!empty($job['activity']) && !empty($job['interval'])) {
            $savejobs[] = $job;
        }
    }
    $serialjobs = serialize($savejobs);
    xarModVars::set('workflow','jobs',$serialjobs);

    if (xarModIsAvailable('scheduler')) {
        if (!xarVarFetch('interval', 'str:1', $interval, '', XARVAR_NOT_REQUIRED)) return;
        // see if we have a scheduler job running to execute workflow activities
        $job = xarModAPIFunc('scheduler','user','get',
                             array('module' => 'workflow',
                                   'type' => 'scheduler',
                                   'func' => 'activities'));
        if (empty($job) || empty($job['interval'])) {
            if (!empty($interval)) {
                // create a scheduler job
                xarModAPIFunc('scheduler','admin','create',
                              array('module' => 'workflow',
                                    'type' => 'scheduler',
                                    'func' => 'activities',
                                    'interval' => $interval));
            }
        } elseif (empty($interval)) {
            // delete the scheduler job
            xarModAPIFunc('scheduler','admin','delete',
                          array('module' => 'workflow',
                                'type' => 'scheduler',
                                'func' => 'activities'));
        } elseif ($interval != $job['interval']) {
            // update the scheduler job
            xarModAPIFunc('scheduler','admin','update',
                          array('module' => 'workflow',
                                'type' => 'scheduler',
                                'func' => 'activities',
                                'interval' => $interval));
        }
    }

    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'blocks'));
    $data['module_settings']->getItem();
    $isvalid = $data['module_settings']->checkInput();
    if (!$isvalid) {
        return xarTplModule('workflow','admin','modifyconfig', $data);        
    } else {
        $itemid = $data['module_settings']->updateItem();
    }
    
    xarResponse::redirect(xarModURL('workflow', 'admin', 'modifyconfig'));

    return true;
}

?>