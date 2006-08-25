<?php

function xtasks_admin_updateassign($args)
{
    if (!xarVarFetch('taskid', 'id', $taskid)) return;
    if (!xarVarFetch('owner', 'id', $owner, $owner, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    if(!xarModAPIFunc('xtasks',
					'admin',
					'updateassign',
					array('taskid'	            => $taskid,
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