<?php

function labaccounting_journaltransactions_modify($args)
{
    extract($args);

    if (!xarVarFetch('transactionid', 'id', $transactionid)) return;
    if (!xarVarFetch('inline', 'str', $inline, $inline, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creatorid', 'int', $creatorid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str', $returnurl, 0, XARVAR_NOT_REQUIRED)) return;
    
    $data = array();
    
    $data['transactionid'] = $transactionid;
    
    $transactioninfo = xarModAPIFunc('labaccounting', 'journaltransactions', 'get', array('transactionid' => $transactionid));
    
    if($transactioninfo == false) return;
    
    $data['transactioninfo'] = $transactioninfo;
    
	$item = xarModAPIFunc('labaccounting',
                         'journals',
                         'get',
                         array('journalid' => $transactioninfo['journalid']));
	
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditJournal', 1, 'Item', "$item[account_title]:All:$transactioninfo[journalid]")) {
        return;
    }
    
    $journallist = xarModAPIFunc('labaccounting', 'journals', 'getall', array('journaltype' => $item['journaltype']));
    
    if($journallist == false) return;
    
    $data['journallist'] = $journallist;

    if (!xarSecurityCheck('AddJournal')) {
        return;
    }
    
    if(!empty($returnurl)) {
        $data['returnurl'] = $returnurl;
    } else {
        $data['returnurl'] = xarModURL('labaccounting', 'journals', 'display', array('journalid' => $transactioninfo['journalid'], 'yearshown' => date('Y', strtotime($transactioninfo['transdate']))));
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['journalid'] = $transactioninfo['journalid'];
    $data['inline'] = $inline;
    $data['creatorid'] = $creatorid;
    
    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add'));

    return $data;
}

?>
