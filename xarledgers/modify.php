<?php

function labaccounting_ledgers_modify($args)
{
	extract($args);
    
    if (!xarVarFetch('ledgerid',     'id',     $ledgerid,     $ledgerid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl',     'str::',     $returnurl,     '',     XARVAR_NOT_REQUIRED)) return;
	
    if(empty($returnurl)) $returnurl = xarServerGetVar('HTTP_REFERER');
    
    if (empty($returnurl)) {
        $returnurl = xarModURL('labaccounting', 'ledgers', 'display', array('ledgerid' => $ledgerid));
    }
	$item = xarModAPIFunc('labaccounting',
                         'ledgers',
                         'get',
                         array('ledgerid' => $ledgerid));
	
	if (!isset($task) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditLedger', 1, 'Item', "$item[account_title]:All:$ledgerid")) {
        return;
    }
    
	$data = xarModAPIFunc('labaccounting', 'admin', 'menu');

    $data['ledgers_objectid'] = xarModGetVar('labaccounting', 'ledgers_objectid');
    
	$data['ledgerid'] = $item['ledgerid'];
    
	$data['item'] = $item;
    
	$data['returnurl'] = $returnurl;
	
    $data['authid'] = xarSecGenAuthKey();
	
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));
    
    $chartlist = xarModAPIFunc('dynamicdata','user','getitems',
                          array('module'    => 'labaccounting',
                                'itemtype'  => 5,
                                'where'     => "active eq 1",
                                'numitems'  => -1,
                                'startnum'  => 1));
    
    $chartitemlist = xarModAPIFunc('dynamicdata','user','getitems',
                          array('module'    => 'labaccounting',
                                'itemtype'  => 5,
                                'where'     => "accountnumstart le ".$item['chartacctnum'],
                                'sort'      => "accountnumstart DESC",
                                'numitems'  => 1,
                                'startnum'  => 1));
                                
    $chartiteminfo = array_shift($chartitemlist);
    
    $data['chartiteminfo'] = $chartiteminfo;

    $optionlist = array();
    $ledgertype = "";
    foreach($chartlist as $chartinfo) {
        if($chartinfo['ledgertype'] != $ledgertype) {
            $optionlist[] = array('id' => "", 'name' => " :: ".$chartinfo['ledgertype']." ::");
            $optionlist[] = array('id' => $chartinfo['accountnumstart'], 'name' => $chartinfo['accttype']);
            $ledgertype = $chartinfo['ledgertype'];
        } else {
            $optionlist[] = array('id' => $chartinfo['accountnumstart'], 'name' => $chartinfo['accttype']);
        }
    }
    $data['optionlist'] = $optionlist;
    
    return $data;
}

?>