<?php


// list of last 10 contact log records
// form to search records
// secondary form to create new log records
// list of users in CSR group along with last log created and number of calls "today"

function dossier_user_callrotator($args) {

    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ltr', 'str::', $ltr, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'str::', $sortby, 'sortcompany', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'str::', $private, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cat_id', 'str::', $cat_id, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('q', 'str::', $q, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('searchphone', 'str::', $searchphone, '', XARVAR_NOT_REQUIRED)) return;
    
    extract($args);
    
    $agentuid = xarUserGetVar('uid');
    
    if (!xarSecurityCheck('TeamDossierAccess', 0, 'Contact', "All:All:All:".$agentuid)) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $stafflist = array();
    $staffgroupid = xarModGetVar('dossier','csr_group');
    if($staffgroupid) {
        $stafflist = xarModAPIFunc('roles','user','getall',array('group'=>$staffgroupid));
    }
    
    $data = xarModAPIFunc('dossier', 'user', 'menu');
    
    $data['stafflist'] = $stafflist;
    
    $data['ltr'] = $ltr;
    $data['sortby'] = $sortby;
    $data['startnum'] = $startnum;
    $data['private'] = $private ? $private : "off";
    $data['cat_id'] = $cat_id;
    $data['q'] = $q;
    $nowtime = time();
    
    $items = array();
    if(!empty($q)) {
        $items = xarModAPIFunc('dossier', 'user', 'getall',
                                array('ltr' => $ltr,
                                      'sortby' => $sortby,
                                      'private' => $private,
                                      'cat_id' => $cat_id,
                                      'q' => $q,
                                      'searchphone' => $searchphone,
                                      'startnum' => $startnum,
                                      'numitems' => xarModGetVar('dossier','itemsperpage')));
        if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    }
        
    $data['items'] = $items;
    
    $logrecords = array();
    if(!empty($q) || true) {
        $logrecords = xarModAPIFunc('dossier', 'logs', 'getall',
                                    array('startnum' => $startnum,
                                          'numitems' => xarModGetVar('dossier','itemsperpage'),
                                          'mindate' => date("Y-m-d H:i:s",strtotime("last year")) // arbitrary date, just fall back on itemsperpage...
                                        )
                                   );
        if (!isset($logrecords) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    }
        
    $data['logrecords'] = $logrecords;
                                    
                                    
        
	return $data;


}

?>