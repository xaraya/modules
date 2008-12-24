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
 * Modify extra information for scheduler jobs
 * @param id itemid
 */
function scheduler_admin_create()
{
    if (!xarSecurityCheck('AdminScheduler')) return;

    if (!xarVarFetch('confirm','isset',$confirm,NULL,XARVAR_NOT_REQUIRED)) return;
    if (!empty($confirm)) {
        if (!xarSecConfirmAuthKey()) return;

        if (!xarVarFetch('job_module','isset',$job_module,'',XARVAR_NOT_REQUIRED)) return;
        $modules = xarModAPIFunc('modules', 'admin', 'getlist', array('filter' => array('AdminCapable' => 1)));

        $modnames = array();

        foreach ($modules as $module) {
            $modnames[] = $module['name'];
        }

        if($job_module != '' && !in_array($job_module, $modnames)) {
            $msg = xarML('Invalid module for #(1) function #(2)() in module #(3)',
                         'user', 'modify', 'scheduler');
            throw BadParameterException($msg);
        }
        $job['module'] = $job_module;

        if (!xarVarFetch('functype','isset',$functype,'',XARVAR_NOT_REQUIRED)) return;
        $types = array(
                   'scheduler' => 'scheduler',
                   'admin' => 'admin',
                   'user' => 'user',
                  );
        if($functype != '' && !isset($types[$functype])) {
            $msg = xarML('Invalid function type for #(1) function #(2)() in module #(3)',
                         'user', 'modify', 'scheduler');
            throw BadParameterException($msg);
        }
        $job['functype'] = $functype; 

        if (!xarVarFetch('job_func','isset',$job_func,'',XARVAR_NOT_REQUIRED)) return;
        $job['func'] = $job_func;

        if (!xarVarFetch('job_interval','isset',$job_interval,'',XARVAR_NOT_REQUIRED)) return;
        $job['job_interval'] = $job_interval;

        if (!xarVarFetch('config','isset',$config,array(),XARVAR_NOT_REQUIRED)) return;
        if (empty($config)) {
            $config = array();
        }
        if (!empty($config['startdate'])) {
            $config['startdate'] = strtotime($config['startdate']);
        }
        if (!empty($config['enddate'])) {
            $config['enddate'] = strtotime($config['enddate']);
        }

        if (!xarVarFetch('job_trigger','int',$job_trigger,0,XARVAR_NOT_REQUIRED)) return;
        $triggers = xarModAPIFunc('scheduler','user','triggers');
        if (!isset($triggers[$job_trigger])) {
            $msg = xarML('Invalid trigger type for #(1) function #(2)() in module #(3)',
                         'user', 'modify', 'scheduler');
            throw BadParameterException($msg);
        }
        $job['job_trigger'] = $job_trigger;

        if (!xarVarFetch('checktype','int',$checktype,1,XARVAR_NOT_REQUIRED)) return;
        $checktypes = xarmodAPIFunc('scheduler','user','sources');
        if (!isset($checktypes[$checktype])) {
            $msg = xarML('Invalid checktype type for #(1) function #(2)() in module #(3)',
                         'user', 'modify', 'scheduler');
            throw BadParameterException($msg);
        }
        $job['checktype'] = $checktype;

        if (!xarVarFetch('checkvalue','isset',$checkvalue,'',XARVAR_NOT_REQUIRED)) return;
        $job['checkvalue'] = $checkvalue;

        if ($job_interval == '0c' && !empty($config['crontab'])) {
            $config['crontab']['nextrun'] = xarModAPIFunc('scheduler','user','nextrun',
                                                          $config['crontab']);
        }
        $job['config'] = $config;

        xarModAPIFunc('scheduler','admin','create', $job);

        xarResponseRedirect(xarModURL('scheduler', 'admin', 'modifyconfig'));
        return true;
    }

    xarResponseRedirect(xarModURL('scheduler', 'admin', 'modifyconfig'));
    return true;
}
?>
