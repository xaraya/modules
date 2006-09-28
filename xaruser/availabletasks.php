<?php

function xtasks_user_availabletasks($args)
{
    extract($args);
    
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby',   'str', $orderby,   '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('xtasks', 'admin', 'menu');

    $data['xtasks_objectid'] = xarModGetVar('xtasks', 'xtasks_objectid');
    
    $data['orderby'] = $orderby;
//    xarModAPILoad('xtaskss', 'user');
    $items = xarModAPIFunc('xtasks', 'user', 'getall',
                            array('mymemberid' => 0,
                                'statusfilter' => "Active",
                                'orderby' => $orderby,
                                'startnum' => $startnum,
                                'numitems' => xarModGetVar('xtasks','itemsperpage')));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $data['items'] = $items;
    $data['authid'] = xarSecGenAuthKey();
    
    $data['returnurl'] = xarModURL('xtasks', 'user', 'availabletasks', 
                    array('startnum' => '%%',
                            'orderby' => $orderby));
    
    $uid = xarUserGetVar('uid');
    
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xtasks', 'user', 'countitems', 
                    array('memberid' => 0,
                            'statusfilter' => "Active")),
        $data['returnurl'],
        xarModGetUserVar('xtasks', 'itemsperpage', $uid));
        
    return $data;
}

?>