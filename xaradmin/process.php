<?php

function xtasks_admin_process($args)
{
    extract($args);
    
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskcheck',     'array',     $taskcheck,     $taskcheck,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskfocus',     'array',     $taskfocus,     $taskfocus,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('newmodid',   'isset', $newmodid,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('newitemtype',  'isset', $newitemtype,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('newobjectid',  'isset', $newobjectid,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('status', 'str', $status, $status, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('chngstatus', 'str', $chngstatus, $chngstatus, XARVAR_NOT_REQUIRED)) return;
    
    if (!xarSecConfirmAuthKey()) return;
    
    if($chngstatus == "Go") {
        
        if(is_array($taskcheck)) {
            foreach($taskcheck as $subtaskid => $v) {
                $taskinfo = xarModAPIFunc('xtasks','user','get',array('taskid'=>$subtaskid));
                $taskinfo['status'] = $status;
                if(!xarModAPIFunc('xtasks',
                                'admin',
                                'update',
                                $taskinfo)) {
                    return;
                }
            }
        }
    
    
    } else {
    
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
                                    array('taskid'                => $subtaskid,
                                        'parentid'              => $taskid,
                                        'modid'              => $newmodid,
                                        'itemtype'              => $newitemtype,
                                        'objectid'              => $newobjectid))) {
                        return;
                    }
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