<?php


// list of last 10 contact log records
// form to search records
// secondary form to create new log records
// list of users in CSR group along with last log created

function dossier_user_process($args) {

    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ltr', 'str::', $ltr, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'str::', $sortby, 'sortcompany', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'str::', $private, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cat_id', 'str::', $cat_id, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('q', 'str::', $q, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('searchphone', 'str::', $searchphone, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('agentuid', 'int:1:', $agentuid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('logtype', 'str::', $logtype, '', XARVAR_NOT_REQUIRED)) return;
    
    if (!xarVarFetch('userid', 'int:1:', $userid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactid', 'isset::', $contactid, "search", XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fname', 'str::', $fname, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lname', 'str::', $lname, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('company', 'str::', $company, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str::', $title, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('email_1', 'str::', $email_1, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phone_work', 'str::', $phone_work, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('logdate', 'isset::', $logdate, NULL, XARVAR_NOT_REQUIRED)) return;
    
    extract($args);
            
    if (!xarSecurityCheck('TeamDossierAccess', 0, 'Contact')) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    if(!empty($logdate)) {
        if (!preg_match('/[a-zA-Z]+/',$logdate)) {
            $logdate .= ' GMT';
        }
        $logdate = strtotime($logdate);
        if ($logdate === false) $logdate = -1;
        if ($logdate >= 0) {
            // adjust for the user's timezone offset
            $logdate -= xarMLS_userOffset($logdate) * 3600;
        }
        $logdate = date("Y-m-d H:i:s", $logdate);
    }
    
    if($contactid == "newcontact") {

        $contactid = xarModAPIFunc('dossier',
                            'admin',
                            'create',
                            array('cat_id' 	    => $cat_id,
                                'agentuid'	    => $agentuid,
                                'userid'	    => 0,
                                'private'	    => 1,
                                'lname'	        => $lname,
                                'fname'	        => $fname,
                                'title'		    => $title,
                                'company'	    => $company,
                                'phone_work'	=> $phone_work,
                                'email_1'	    => $email_1,
                                'notes'	        => $notes));
    
    
        if (!isset($contactid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
        xarSessionSetVar('statusmsg', xarML('Contact Record Created'));
    
        if (empty($notes)) {
    
            xarResponseRedirect(xarModURL('dossier','admin','display',array('contactid'=>$contactid)));
        
            return true;
    
        }
    }
    
    if (is_numeric($contactid) && $contactid > 0 && !empty($notes)) {

        $worklogid = xarModAPIFunc('dossier',
                            'logs',
                            'create',
                            array('contactid'    => $contactid,
                                'ownerid'        => $agentuid,
                                'logtype'        => $logtype,
                                'logdate'        => $logdate,
                                'notes'          => $notes));
    
    
        if (!isset($worklogid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
        xarSessionSetVar('statusmsg', xarML('Contact Log Created'));
    
        xarResponseRedirect(xarModURL('dossier','user','callrotator'));
    
        return true;
    
    }
    
    // else $contactid = "search"
    
    $stafflist = array();
    $staffgroupid = xarModGetVar('dossier','csr_group');
    if($staffgroupid) {
        $stafflist = xarModAPIFunc('roles','user','getall',array('group'=>$staffgroupid));
    }
    
    $data = xarModAPIFunc('dossier', 'user', 'menu');
    
    $data['currentdate'] = date("Y-m-d H:i:s", xarMLS_userTime());
    
    $data['stafflist'] = $stafflist;
    
    $data['agentuid'] = $agentuid;
    
    $data['ltr'] = $ltr;
    $data['sortby'] = $sortby;
    $data['startnum'] = $startnum;
    $data['private'] = $private ? $private : "off";
    $data['cat_id'] = $cat_id;
    $data['q'] = $q;
    $data['searchphone'] = $searchphone;
    
    $data['logtype'] = $logtype;
    $data['notes'] = $notes;

    $data['contacts_objectid'] = xarModGetVar('dossier', 'contacts_objectid');
    
    $items = array();
    if(!empty($q) || true) {
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
    if(!empty($q)) {
        $logrecords = xarModAPIFunc('dossier', 'logs', 'getall',
                                    array('startnum' => $startnum,
                                      'numitems' => xarModGetVar('dossier','itemsperpage')));
        if (!isset($logrecords) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    }
        
    $data['logrecords'] = $logrecords;
                                    
                                    
        
	return $data;


}

?>