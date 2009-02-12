<?php

function labaccounting_journals_update($args)
{
    if (!xarVarFetch('journalid', 'id', $journalid)) return;
    if (!xarVarFetch('parentid', 'id', $parentid, $parentid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('owneruid', 'id', $owneruid, $owneruid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactid', 'id', $contactid, $contactid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('currency', 'str::', $currency, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('account_title', 'str:1:', $account_title, $account_title, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('journaltype', 'str::', $journaltype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('agentuid', 'id', $agentuid, $agentuid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('acctnum', 'str::', $acctnum, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('acctlogin', 'str::', $acctlogin, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('accturl', 'str::', $accturl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('acctpwd', 'str::', $acctpwd, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('opendate', 'str::', $opendate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('closedate', 'str::', $closedate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('billdate', 'str::', $billdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invoicefreq', 'str::', $invoicefreq, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invoicefrequnits', 'str::', $invoicefrequnits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ledgers', 'array', $ledgers, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('labaccounting',
					'journals',
					'update',
					array('journalid'	    => $journalid,
                        'parentid'          => $parentid,
                        'owneruid'          => $owneruid,
                        'contactid'         => $contactid,
                        'currency'          => $currency,
                        'account_title'     => $account_title,
                        'journaltype'       => $journaltype,
                        'agentuid'          => $agentuid,
                        'acctnum'           => $acctnum,
                        'acctlogin'         => $acctlogin,
                        'accturl'           => $accturl,
                        'acctpwd'           => $acctpwd,
                        'notes'             => $notes,
                        'opendate'          => $opendate,
                        'closedate'         => $closedate,
                        'billdate'          => $billdate,
                        'status'            => $status,
                        'invoicefreq'       => $invoicefreq,
                        'invoicefrequnits'  => $invoicefrequnits))) {
		return;
	}

	xarSessionSetVar('statusmsg', xarML('Journal Updated'));

    $ledgerlist = xarModAPIFunc('labaccounting', 'ledgers', 'getall');
    
    if(!is_array($ledgerlist)) return;

    $activeledgers = xarModAPIFunc('labaccounting', 'journals', 'getall_ledgers', array('journalid' => $journalid));
    
    if(!is_array($activeledgers)) return;

    $activeledgeridlist = array();
    $activelist = array();
    if(is_array($activeledgers)) {
        foreach($activeledgers as $ledgerinfo) {
            $activeledgeridlist[] = $ledgerinfo['ledgerid'];
            $activelist[$ledgerinfo['ledgerid']] = $ledgerinfo;
        }
    }

    if(is_array($ledgerlist)) {
        foreach($ledgerlist as $ledgerinfo) {
            if(in_array($ledgerinfo['ledgerid'], $activeledgeridlist)) {
                if($ledgers[$ledgerinfo['ledgerid']] == "none") {
                    if(!xarModAPIFunc('labaccounting','journals','removeledger',
                                    array('journalid' => $journalid,
                                        'ledgerid' => $ledgerinfo['ledgerid']))) {
                        return;
                    }
                } elseif($activelist[$ledgerinfo['ledgerid']]['normalbalance'] != $ledgers[$ledgerinfo['ledgerid']]) {
                    if(!xarModAPIFunc('labaccounting','journals','removeledger',
                                    array('journalid' => $journalid,
                                        'ledgerid' => $ledgerinfo['ledgerid']))) {
                        return;
                    }
                    if(!xarModAPIFunc('labaccounting','journals','addledger',
                                    array('journalid' => $journalid,
                                        'ledgerid' => $ledgerinfo['ledgerid'],
                                        'normalbalance' => $ledgers[$ledgerinfo['ledgerid']]))) {
                        return;
                    }
                }
            } else {
                if($ledgers[$ledgerinfo['ledgerid']] != "none") {
                    if(!xarModAPIFunc('labaccounting','journals','addledger',
                                    array('journalid' => $journalid,
                                        'ledgerid' => $ledgerinfo['ledgerid'],
                                        'normalbalance' => $ledgers[$ledgerinfo['ledgerid']]))) {
                        return;
                    }
                }
            }
        }
    }    

    if(!empty($returnurl)) {
        xarResponseRedirect($returnurl);
        return true;
    }
    
    xarResponseRedirect(xarModURL('labaccounting', 'journals', 'view'));

    return true;
}

?>