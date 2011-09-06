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
function scheduler_admin_modify()
{
    if (!xarVarFetch('itemid','id', $itemid)) {return;}

    if (!xarSecurityCheck('AdminScheduler')) return;

    $serialjobs = xarModVars::get('scheduler', 'jobs');
    if (empty($serialjobs)) {
        $jobs = array();
    } else {
        $jobs = unserialize($serialjobs);
    }

    if (empty($jobs[$itemid])) {
        xarResponse::redirect(xarModURL('scheduler', 'admin', 'modifyconfig'));
        return true;
    }

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
        if ($interval == '0c' && !empty($config['crontab'])) {
            $config['crontab']['nextrun'] = xarMod::apiFunc('scheduler','user','nextrun',
                                                          $config['crontab']);
        }
        $job['config'] = $config;

        $serialjobs = serialize($jobs);
        xarModVars::set('scheduler','jobs',$serialjobs);

        xarResponse::redirect(xarModURL('scheduler', 'admin', 'modify',
                                      array('itemid' => $itemid)));
        return true;
    }

    // Use the current job as $data
    $data = $job;

    $modules = xarModAPIFunc('modules', 'admin', 'getlist',
                             array('filter' => array('AdminCapable' => 1)));
    $data['modules'] = array();
    foreach ($modules as $module) {
        $data['modules'][$module['name']] = $module['displayname'];
    }
    $data['types'] = array( // don't translate API types
                           'scheduler' => 'scheduler',
                           'admin' => 'admin',
                           'user' => 'user',
                          );

    $data['triggers'] = xarModAPIFunc('scheduler','user','triggers');
    $data['sources'] = xarModAPIFunc('scheduler','user','sources');

    $data['itemid'] = $itemid;
    $data['authid'] = xarSecGenAuthKey();
    $data['intervals'] = xarMod::apiFunc('scheduler','user','intervals');

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