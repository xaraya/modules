<?php

function labaccounting_journals_group_update($args)
{
    if (!xarVarFetch('journals', 'array', $journals, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('journaltype', 'str::', $journaltype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('parentid', 'array::', $parentid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);
    
    if (!xarSecConfirmAuthKey()) return;
    echo "parentid: <pre>";
    print_r($parentid);
    echo "</pre>";
    if(is_array($parentid)) $parentidarray = each($parentid);
    if(isset($parentidarray['key'])) $parentid = $parentidarray['key'];
    echo "parentid: <pre>";
    print_r($parentid);
    echo "</pre>";
    
    foreach($journals as $journalid => $flag) {
    
        $journalinfo = xarModAPIFunc('labaccounting', 'journals', 'get', array('journalid' => $journalid));
        
        if($journalinfo == false) return;
    
        if($journalinfo['journaltype'] != $journaltype || $journalinfo['status'] != $status || $parentid > 0) {
            
            if($parentid > 0) {
                $journalinfo['parentid'] = $parentid;
            } else {
            
                $journalinfo['journaltype'] = $journaltype;
                
                if(!empty($status)) {
                    $journalinfo['status'] = $status;
                }
            
            }
        
            if (!xarModAPIFunc('labaccounting',
        					'journals',
        					'update',
        					$journalinfo)) {
        		return;
        	}
        }
    }
    
	xarSessionSetVar('statusmsg', xarML('Journal Type Updated'));

    if(!empty($returnurl)) {
        xarResponseRedirect($returnurl);
        return true;
    }
    
    xarResponseRedirect(xarModURL('labaccounting', 'journals', 'view', array('journaltype' => $journaltype)));

    return true;
}

?>