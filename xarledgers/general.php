<?php

function labaccounting_ledgers_general($args)
{
    extract($args);
    
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('owneruid',   'int', $owneruid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby',   'str', $orderby,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('accttype',   'str', $accttype,   '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('labaccounting', 'admin', 'menu');

    $data['ledgers_objectid'] = xarModGetVar('labaccounting', 'ledgers_objectid');
    
    $data['orderby'] = $orderby;
    
    $items = xarModAPIFunc('labaccounting', 'ledgers', 'getall',
                            array('owneruid' => $owneruid,
                                'accttype' => $accttype,
                                'orderby' => $orderby,
                                'startnum' => $startnum,
                                'numitems' => xarModGetVar('labaccounting','itemsperpage')));
                                
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $items[$i]['link'] = xarModURL('labaccounting',
            'ledgers',
            'display',
            array('ledgerid' => $item['ledgerid']));
        if (xarSecurityCheck('EditLedger', 0, 'Item', "$item[account_title]:All:$item[ledgerid]")) {
            $items[$i]['editurl'] = xarModURL('labaccounting',
                'ledgers',
                'modify',
                array('ledgerid' => $item['ledgerid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteLedger', 0, 'Item', "$item[account_title]:All:$item[ledgerid]")) {
            $items[$i]['deleteurl'] = xarModURL('labaccounting',
                'ledgers',
                'delete',
                array('ledgerid' => $item['ledgerid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
    }
    
    $data['items'] = $items;
    
    $data['accttype'] = $accttype;
    
    $uid = xarUserGetVar('uid');
    
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