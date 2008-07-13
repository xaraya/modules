<?php

function xtasks_user_quickproject($args)
{        
    extract($args);
    
    if (!xarVarFetch('contactid', 'int:1:', $contactid, 0, XARVAR_NOT_REQUIRED)) return;
    
    if (!xarSecurityCheck('AddXTask')) {
        return;
    }
    
    $data['contactid'] = $contactid;
    
    if($contactid > 0) {
        $data['newprojectform'] = xarModFunc('xproject', 'admin', 'new', array('clientid' => $contactid));
        
    //    xarModAPILoad('xtaskss', 'user');
        $projectlist = xarModAPIFunc('xproject', 'user', 'getall', array('clientid' => $contactid, 'status' => xarModGetVar('xproject', 'activestatus')));
        if (!isset($projectlist) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
        
        $data['projectlist'] = $projectlist;
    } else {
        $data['projectlist'] = array();
        $data['newprojectform'] = "xproject_admin_new";
    }
        
    return $data;
}

?>
