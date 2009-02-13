<?php

function xtasks_admin_updateassign($args)
{
    if (!xarVarFetch('taskid', 'id', $taskid)) return;
    if (!xarVarFetch('owner', 'id', $owner, $owner, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_end_planned', 'str', $date_end_planned, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    if(!xarModAPIFunc('xtasks',
                    'admin',
                    'updateassign',
                    array('taskid'                => $taskid,
                        'date_end_planned'      => $date_end_planned,
                        'description'           => $description,
                        'owner'                 => $owner))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Task Assigned'));

    if(!empty($returnurl)) {
        xarResponseRedirect($returnurl);
        return true;
    }
    
    xarResponseRedirect(xarModURL('xtasks', 'admin', 'view'));

    return true;
}

?>