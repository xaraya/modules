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
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @param string trigger
 */
function scheduler_admin_updateconfig()
{
    if (!xarSecurityCheck('AdminScheduler')) return;
    if (!xarSecConfirmAuthKey()) return;

    if (!xarVarFetch('trigger','str:1:',$trigger,'disabled',XARVAR_NOT_REQUIRED)) return;
    xarModVars::set('scheduler', 'trigger', $trigger);

    if ($trigger == 'external') {
        if (!xarVarFetch('checktype','isset',$checktype,'',XARVAR_NOT_REQUIRED)) return;
        xarModVars::set('scheduler', 'checktype', $checktype);
        if (!xarVarFetch('checkvalue','isset',$checkvalue,'',XARVAR_NOT_REQUIRED)) return;
        xarModVars::set('scheduler', 'checkvalue', $checkvalue);
    }

    if (!xarVarFetch('reset','isset',$reset,0,XARVAR_NOT_REQUIRED)) return;

    $serialjobs = xarModVars::get('scheduler', 'jobs');
    if (empty($serialjobs)) {
        $oldjobs = array();
    } else {
        $oldjobs = unserialize($serialjobs);
    }

    if (!xarVarFetch('jobs','isset',$jobs,array(),XARVAR_NOT_REQUIRED)) return;
    if (empty($jobs)) {
        $jobs = array();
    }
    $savejobs = array();
    foreach ($jobs as $id => $job) {
        if (!empty($job['module']) && !empty($job['type']) && !empty($job['func']) && !empty($job['interval'])) {
            if (!empty($reset)) {
                $job['lastrun'] = 0;
                $job['result'] = '';
            }
            if (empty($id)) {
                // get the next job id
                $maxid = xarModVars::get('scheduler','maxjobid');
                if (empty($maxid)) $maxid = 0;
                $maxid++;
                xarModVars::set('scheduler','maxjobid',$maxid);
                $id = $maxid;
            } elseif (!empty($oldjobs[$id])) {
                // get the extra configuration from the original job
                if (isset($oldjobs[$id]['config'])) {
                    $job['config'] = $oldjobs[$id]['config'];
                }
            }
            $savejobs[$id] = $job;
        }
    }
    $serialjobs = serialize($savejobs);
    xarModVars::set('scheduler','jobs',$serialjobs);

    if (!empty($reset)) {
        xarModVars::set('scheduler','lastrun',0);
        xarModVars::delete('scheduler','running');
    }

    xarModCallHooks('module','updateconfig','scheduler',
                    array('module' => 'scheduler'));

    xarResponseRedirect(xarModURL('scheduler', 'admin', 'modifyconfig'));

    return true;
}
?>
