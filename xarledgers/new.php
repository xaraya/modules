<?php

function labaccounting_ledgers_new($args)
{
    extract($args);

    if (!xarVarFetch('inline', 'str', $inline, $inline, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('labaccounting','admin','menu');

    if (!xarSecurityCheck('AddLedger')) {
        return;
    }
    
    $data['returnurl'] = $returnurl;
    
    $chartlist = xarModAPIFunc('dynamicdata','user','getitems',
                          array('module'    => 'labaccounting',
                                'itemtype'  => 5,
                                'where'     => "active eq 1",
                                'numitems'  => -1,
                                'startnum'  => 1));
                                
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

    $data['authid'] = xarSecGenAuthKey();
    $data['inline'] = $inline;
    
    $data['owneruid'] = xarSessionGetVar('uid');
    
    $data['parentid'] = 0;
    
    $data['date_start_planned'] = date("Y-m-d");

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add'));

    return $data;
}

?>
