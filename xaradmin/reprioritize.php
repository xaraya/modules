<?php

function xproject_admin_reprioritize($args)
{
    if (!xarVarFetch('projectid', 'id', $projectid)) return;
    if (!xarVarFetch('mode', 'str:1:', $mode, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    if(!xarModAPIFunc('xproject',
                    'admin',
                    'reprioritize',
                    array('projectid'    => $projectid,
                        'mode'          => $mode))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Project Priority Changed'));

    if(!empty($returnurl)) {
        xarResponseRedirect($returnurl);
        return true;
    }

    xarResponseRedirect(xarModURL('xproject', 'admin', 'view'));

    return true;
}

?>