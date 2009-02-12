<?php

function labaccounting_journals_modify($args)
{
	extract($args);
    
    if (!xarVarFetch('journalid',     'id',     $journalid,     $journalid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('inline',     'isset',     $inline,     '',     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl',     'str::',     $returnurl,     '',     XARVAR_NOT_REQUIRED)) return;
	
    if (empty($returnurl)) {
        $returnurl = xarModURL('labaccounting', 'journals', 'display', array('journalid' => $journalid));
    }
    
	$item = xarModAPIFunc('labaccounting',
                         'journals',
                         'get',
                         array('journalid' => $journalid));
	
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditJournal', 1, 'Item', "$item[account_title]:All:$journalid")) {
        return;
    }
    
    $ledgerlist = xarModAPIFunc('labaccounting', 'ledgers', 'getall');
    
    if(!is_array($ledgerlist)) return;
    
    $activeledgers = xarModAPIFunc('labaccounting', 'journals', 'getall_ledgers',
                                        array('journalid' => $journalid));
    
    if(!is_array($activeledgers)) return;
    
    $activeledgeridlist = array();
    $activeledgerlist = array();
    foreach($activeledgers as $activeledger) {
        $activeledgeridlist[] = $activeledger['ledgerid'];
        $activeledgerlist[$activeledger['ledgerid']] = $activeledger;
    }
    
    $ledgers = array();
    foreach($ledgerlist as $ledgerinfo) {
        $ledgerinfo['activebalance'] = "none";
        if(in_array($ledgerinfo['ledgerid'], $activeledgeridlist)) {
            $ledgerinfo['activebalance'] = $activeledgerlist[$ledgerinfo['ledgerid']]['normalbalance'];
        }
        $ledgers[$ledgerinfo['ledgerid']] = $ledgerinfo;
    }
    
    $journals = xarModAPIFunc('labaccounting', 'journals', 'getall', array('journaltype' => $item['journaltype']));
    
    if($journals === false) return;
    
	$data = array();

    $data['journals_objectid'] = xarModGetVar('labaccounting', 'journals_objectid');
    
	$data['journalid'] = $item['journalid'];
    
	$data['item'] = $item;
    
	$data['journals'] = $journals;
    
	$data['ledgerlist'] = $ledgers;
    
	$data['inline'] = $inline;
    
	$data['returnurl'] = $returnurl;
	
    $data['authid'] = xarSecGenAuthKey();
	
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));
    
    return $data;
}

?>