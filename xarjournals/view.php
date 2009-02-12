<?php

function labaccounting_journals_view($args)
{
    extract($args);
    
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('owneruid',   'int', $owneruid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('q',   'str', $q,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby',   'str', $orderby,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('statuslist',   'array::', $statuslist,   array('Active'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('journaltype',   'str', $journaltype,   '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('labaccounting', 'admin', 'menu');
    
    $data['orderby'] = $orderby;
    
    $items = xarModAPIFunc('labaccounting', 'journals', 'getall',
                            array('owneruid' => $owneruid,
                                'journaltype' => $journaltype,
                                'statuslist' => $statuslist,
                                'q' => $q,
                                'orderby' => $orderby,
                                'nested' => true,
                                'startnum' => $startnum,
                                'numitems' => xarModGetVar('labaccounting','itemsperpage')));
                                
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $data['items'] = $items;
    
    $data['q'] = $q;
    
    $data['journaltype'] = $journaltype;
    
    $data['statuslist'] = $statuslist;
    
    $uid = xarUserGetVar('uid');

    $data['authid'] = xarSecGenAuthKey();
    
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('labaccounting', 'journals', 'countitems', 
                    array('ownerid' => $owneruid)),
        xarModURL('labaccounting', 'journals', 'view', 
                    array('startnum' => '%%',
                            'orderby' => $orderby,
                            'ownerid' => $owneruid)),
        xarModGetUserVar('labaccounting', 'itemsperpage', $uid));
        
    return $data;
}

?>