<?php

function xtasks_user_quick($args)
{        
    extract($args);
    
    if (!xarVarFetch('contactid', 'int:1:', $contactid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'int:1:', $projectid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskid', 'int:1:', $taskid, 0, XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('xtasks', 'admin', 'menu');

    if (!xarSecurityCheck('AddXTask')) {
        return;
    }
        
    $data['returnurl'] = xarModURL('xtasks', 'user', 'quick');
    
    $uid = xarUserGetVar('uid');
        
    return $data;
}

?>
