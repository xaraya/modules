<?php

function labaccounting_ledgers_update($args)
{
    if (!xarVarFetch('ledgerid', 'id', $ledgerid)) return;
    if (!xarVarFetch('parentid', 'id', $parentid, $parentid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'id', $ownerid, $ownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('accttype', 'str::', $accttype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('chartacctnum', 'str::', $chartacctnum, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('newchartacctnum', 'str::', $newchartacctnum, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('account_title', 'str:1:', $account_title, $account_title, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('normalbalance', 'str::', $normalbalance, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);
    
    if (!xarSecConfirmAuthKey()) return;
    
    if(!empty($newchartacctnum)) {
        $chartacctnum = $newchartacctnum;
    }
    
    if (!xarModAPIFunc('labaccounting',
					'ledgers',
					'update',
					array('ledgerid'	=> $ledgerid,
                        'parentid'      => $parentid,
                        'ownerid'       => $ownerid,
                        'accttype'      => $accttype,
                        'chartacctnum'  => $chartacctnum,
                        'account_title' => $account_title,
                        'normalbalance' => $normalbalance,
                        'notes'         => $notes))) {
		return;
	}


	xarSessionSetVar('statusmsg', xarML('Ledger Updated'));

    if(!empty($returnurl)) {
        xarResponseRedirect($returnurl);
        return true;
    }
    
    xarResponseRedirect(xarModURL('labaccounting', 'ledgers', 'view'));

    return true;
}

?>