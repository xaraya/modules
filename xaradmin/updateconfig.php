<?php

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function scheduler_admin_updateconfig()
{
    if (!xarSecurityCheck('AdminScheduler')) return;
    if (!xarSecConfirmAuthKey()) return;

    if (!xarVarFetch('trigger','str:1:',$trigger,'disabled',XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('scheduler', 'trigger', $trigger);

    if ($trigger == 'external') {
        if (!xarVarFetch('checktype','isset',$checktype,'',XARVAR_NOT_REQUIRED)) return;
        xarModSetVar('scheduler', 'checktype', $checktype);
        if (!xarVarFetch('checkvalue','isset',$checkvalue,'',XARVAR_NOT_REQUIRED)) return;
        xarModSetVar('scheduler', 'checkvalue', $checkvalue);
    }

    if (!xarVarFetch('reset','isset',$reset,0,XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('jobs','isset',$jobs,array(),XARVAR_NOT_REQUIRED)) return;
    if (empty($jobs)) {
        $jobs = array();
    }
    $savejobs = array();
    foreach ($jobs as $job) {
        if (!empty($job['module']) && !empty($job['type']) && !empty($job['func']) && !empty($job['interval'])) {
            if (!empty($reset)) {
                $job['lastrun'] = 0;
                $job['result'] = '';
            }
            $savejobs[] = $job;
        }
    }
    $serialjobs = serialize($savejobs);
    xarModSetVar('scheduler','jobs',$serialjobs);

    if (!empty($reset)) {
        xarModSetVar('scheduler','lastrun',0);
        xarModDelVar('scheduler','running');
    }

    xarModCallHooks('module','updateconfig','scheduler',
                    array('module' => 'scheduler'));

    xarResponseRedirect(xarModURL('scheduler', 'admin', 'modifyconfig'));

    return true;
}
?>