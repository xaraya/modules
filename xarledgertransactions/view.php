<?php

function labaccounting_ledgertransactions_view($args)
{
    extract($args);
    
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startdate', 'isset::', $startdate, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ledgerid',   'int', $ledgerid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby',   'str', $orderby,   '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('labaccounting', 'admin', 'menu');
    
    $data['orderby'] = $orderby;
    
    $ledgerinfo = xarModAPIFunc('labaccounting', 'ledgers', 'get',
                            array('ledgerid' => $ledgerid));
    
    if($ledgerinfo == false) return;
    
    $data['ledgerinfo'] = $ledgerinfo;
    
    $transactionlist = xarModAPIFunc('labaccounting', 'ledgertransactions', 'getall',
                            array('ledgerid' => $ledgerid));
    
    if($transactionlist === false) return;
    
    $data['transactionlist'] = $transactionlist;
    
    if($startdate == true) {
        $beginningbalance = xarModAPIFunc('labaccounting', 'ledgertransactions', 'getbalance',
                                array('ledgerid' => $ledgerid,
                                    'balancedate' => $startdate));
        
        if($beginningbalance === false) return;
    } else {
        $beginningbalance = 0;
    }
    
    $data['beginningbalance'] = $beginningbalance;
    
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('labaccounting', 'ledgers', 'countitems', 
                    array('owneruid' => $owneruid)),
        xarModURL('labaccounting', 'ledgers', 'view', 
                    array('startnum' => '%%',
                            'orderby' => $orderby,
                            'owneruid' => $owneruid)),
        xarModGetUserVar('labaccounting', 'itemsperpage', $uid));
        
    return $data;
}

?>