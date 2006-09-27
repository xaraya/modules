<?php

function xproject_admin_chngstatus($args)
{
    if (!xarVarFetch('projectlist', 'array', $projectlist, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('newstatus', 'str::', $newstatus, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    
    if(is_array($projectlist)) {
        foreach($projectlist as $projectid => $checked) {
            if(!xarModAPIFunc('xproject',
                            'admin',
                            'chngstatus',
                            array('projectid'        => $projectid,
                                'newstatus'              => $newstatus))) {
                return;
            }
        }
    }

    xarSessionSetVar('statusmsg', xarML('Project Importance Changed'));

    if(!empty($returnurl)) {
        xarResponseRedirect($returnurl);
        return true;
    }

    xarResponseRedirect(xarModURL('xproject', 'admin', 'view'));

    return true;
}

?>