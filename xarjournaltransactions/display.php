<?php

function labaccounting_journaltransactions_display($args)
{
    extract($args);
    if (!xarVarFetch('transactionid', 'id', $transactionid)) return;

    $data = xarModAPIFunc('labaccounting','admin','menu');
    
    $data['transactionid'] = $transactionid;
    
    $item = xarModAPIFunc('labaccounting',
                          'journaltransactions',
                          'get',
                          array('transactionid' => $transactionid));

    if (!isset($item)) {
        $msg = xarML('Not authorized to access #(1) items',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return $msg;
    }
    
    $data['item'] = $item;
    
    $journalinfo = xarModAPIFunc('labaccounting', 'journals', 'get', array('journalid' => $item['journalid']));
    
    if($journalinfo == false) return;
    
    $data['journalinfo'] = $journalinfo;
    
    return $data;
}
?>
