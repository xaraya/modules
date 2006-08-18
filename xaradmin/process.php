<?php

function xtasks_admin_process($args)
{
    extract($args);
    
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskcheck',     'array',     $taskcheck,     $taskcheck,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskfocus',     'array',     $taskfocus,     $taskfocus,     XARVAR_NOT_REQUIRED)) return;
    
    if (!xarSecConfirmAuthKey()) return;
    
    if(is_array($taskfocus)) {
        list($taskid) = array_keys($taskfocus);
    }
    
    if(is_array($taskcheck)) {
        foreach($taskcheck as $subtaskid => $v) {
            $taskchecklist[] = $subtaskid;
            if($subtaskid != $taskid) {
                if(!xarModAPIFunc('xtasks',
            					'admin',
            					'inherit',
            					array('taskid'	            => $subtaskid,
                                    'parentid'              => $taskid))) {
            		return;
            	}
            }
        }
    }

	xarSessionSetVar('statusmsg', xarML('Task Updated'));

    if(!empty($returnurl)) {
        xarResponseRedirect($returnurl);
        return true;
    }
    
    xarResponseRedirect(xarModURL('xtasks', 'admin', 'view'));

    return true;
}

?>