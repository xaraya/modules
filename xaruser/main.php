<?php
 
function labaccounting_user_main($args)
{
    if(!xarVarFetch('journaltype','str', $journaltype, 'all', XARVAR_NOT_REQUIRED)) {return;}
            
    extract($args);
    
    if (!xarSecurityCheck('JournalClient')) return;

    $data = xarModAPIFunc('labaccounting','user','menu');
    
    $journals = xarModAPIFunc('labaccounting', 'journals', 'getall', array('owneruid' => xarUserGetVar('uid'), 'journaltype' => $journaltype));
    
    if($journals === false) return;
    
    $data['journals'] = $journals;
    
    return $data;

}

?>