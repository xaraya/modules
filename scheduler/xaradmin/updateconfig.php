<?php

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function scheduler_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;

    if (!xarVarFetch('trigger','str:1:',$trigger,'disabled',XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('scheduler', 'trigger', $trigger);

    if ($trigger == 'external') {
        if (!xarVarFetch('checktype','isset',$checktype,'',XARVAR_NOT_REQUIRED)) return;
        xarModSetVar('scheduler', 'checktype', $checktype);
        if (!xarVarFetch('checkvalue','isset',$checkvalue,'',XARVAR_NOT_REQUIRED)) return;
        xarModSetVar('scheduler', 'checkvalue', $checkvalue);
    }

    if (!xarVarFetch('jobs','isset',$jobs,array(),XARVAR_NOT_REQUIRED)) return;
    if (empty($jobs)) {
        $jobs = array();
    }
    $savejobs = array();
    foreach ($jobs as $job) {
        if (!empty($job['module']) && !empty($job['type']) && !empty($job['func'])) {
            $savejobs[] = $job;
        }
    }
    $serialjobs = serialize($savejobs);
    xarModSetVar('scheduler','jobs',$serialjobs);

    if (!xarVarFetch('lastrun','isset',$lastrun,time(),XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('scheduler','lastrun',$lastrun);

    xarModCallHooks('module','updateconfig','scheduler',
                    array('module' => 'scheduler'));

    xarResponseRedirect(xarModURL('scheduler', 'admin', 'modifyconfig'));

    return true;
}

?>
