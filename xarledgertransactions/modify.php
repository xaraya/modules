<?php

function labaccounting_ledgertransactions_modify($args)
{
	extract($args);
    
    if (!xarVarFetch('transactionid',     'id',     $transactionid,     $transactionid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl',     'str::',     $returnurl,     '',     XARVAR_NOT_REQUIRED)) return;
	
    if(empty($returnurl)) $returnurl = xarServerGetVar('HTTP_REFERER');
    
    if (empty($returnurl)) {
        $returnurl = xarModURL('labaccounting', 'ledgers', 'display', array('ledgerid' => $ledgerid));
    }
	$item = xarModAPIFunc('labaccounting',
                         'ledgertransactions',
                         'get',
                         array('transactionid' => $transactionid));
	
	if (!isset($task) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditLedger', 1, 'Item', "$item[ledgerid]")) {
        return;
    }
    
	$data = xarModAPIFunc('labaccounting', 'admin', 'menu');

    $data['ledgertransactions_objectid'] = xarModGetVar('labaccounting', 'ledgertransactions_objectid');
    
	$data['transactionid'] = $item['transactionid'];
    
	$data['item'] = $item;
    
	$data['returnurl'] = $returnurl;
	
    $data['authid'] = xarSecGenAuthKey();
	
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));
    
    return $data;
}

?>