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
 * @author mikespub
 */
/**
 * Modify extra information for scheduler jobs
 * @param id itemid
 */
function scheduler_admin_modify()
{
    if (!xarSecurityCheck('AdminScheduler')) return;

    if (!xarVarFetch('confirm','isset', $confirm,'', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,'id'   , $data['itemid'], 0, XARVAR_NOT_REQUIRED)) {return;}
    
    if (empty($data['itemid'])) {
        xarController::redirect(xarModURL('scheduler', 'admin', 'view'));
        return true;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => 'scheduler_jobs'));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    if (!empty($confirm)) {
        if (!xarSecConfirmAuthKey()) return;

        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {var_dump($data['object']->getInvalids());exit;
            xarController::redirect(xarModURL('scheduler', 'admin', 'modify',array('itemid' => $itemid)));
        }
        
        // Reset this job as having not yet run
        $data['object']->properties['last_run']->setValue(0);

        $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));

        xarController::redirect(xarModURL('scheduler', 'admin', 'view'));
        return true;
        
        if (!xarVarFetch('config','isset',$config,array(),XARVAR_NOT_REQUIRED)) return;
        if (empty($config)) {
            $config = array();
        }
        if ($interval == '0c' && !empty($config['crontab'])) {
            $config['crontab']['nextrun'] = xarMod::apiFunc('scheduler','user','nextrun',
                                                          $config['crontab']);
        }
        $job['config'] = $config;

        $serialjobs = serialize($jobs);
        xarModVars::set('scheduler','jobs',$serialjobs);

    }


    // Prefill the configuration array
    if (empty($data['config'])) {
        $data['config'] = array(
                                'params' => '',
                                'startdate' => '',
                                'enddate' => '',
                                'crontab' => array('minute' => '',
                                                   'hour' => '',
                                                   'day' => '',
                                                   'month' => '',
                                                   'weekday' => '',
                                                   'nextrun' => ''),
                                // not supported yet
                                'runas' => array('user' => '',
                                                 'pass' => ''),
                               );
    }

    return $data;
}
?>