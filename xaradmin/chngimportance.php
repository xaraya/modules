<?php

function xtasks_admin_chngimportance($args)
{
    if (!xarVarFetch('taskid', 'id', $taskid)) return;
    if (!xarVarFetch('mode', 'str:1:', $mode, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    if(!xarModAPIFunc('xtasks',
					'admin',
					'chngimportance',
					array('taskid'	        => $taskid,
                        'mode'              => $mode))) {
		return;
	}


	xarSessionSetVar('statusmsg', xarML('Task Importance Changed'));

    if(!empty($returnurl)) {
        xarResponseRedirect($returnurl);
        return true;
    }
    
    xarResponseRedirect(xarModURL('xtasks', 'admin', 'view'));

    return true;
}

?>