<?php

function xtasks_user_quicktask($args)
{        
    extract($args);
    
    if (!xarVarFetch('projectid', 'int:1:', $projectid, 0, XARVAR_NOT_REQUIRED)) return;
    
    if (!xarSecurityCheck('AddXTask', 0)) {
        return;
    }
    
    $data['projectid'] = $projectid;
    
    if($projectid > 0) {        
    //    xarModAPILoad('xtaskss', 'user');
        $tasklist = xarModAPIFunc('xtasks', 'user', 'getall', array('projectid' => $projectid, 'mode' => "Open"));
        if (!isset($tasklist) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
        
        $data['tasklist'] = $tasklist;
    } else {
        $data['tasklist'] = array();
    }
        
    return $data;
}

?>
