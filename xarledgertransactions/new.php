<?php

function labaccounting_ledgertransactions_new($args)
{
    extract($args);

    if (!xarVarFetch('ledgerid', 'id', $ledgerid)) return;
    if (!xarVarFetch('inline', 'str', $inline, $inline, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('labaccounting','admin','menu');

    if (!xarSecurityCheck('AddLedger')) {
        return;
    }
    
    $ledgerinfo = xarModAPIFunc('labaccounting','ledgers','get',array('ledgerid' => $ledgerid));
    
    if($ledgerinfo == false) return;
    
    $data['ledgerinfo'] = $ledgerinfo;
    
    $data['returnurl'] = $returnurl;

    $data['authid'] = xarSecGenAuthKey();
    $data['inline'] = $inline;
    $data['ledgerid'] = $ledgerid;
    
    $data['item'] = array('source' => "",
                        'sourceid' => 0,
                        'creatorid' => xarSessionGetVar('uid'),
                        'title' => "",
                        'details' => "",
                        'transnum' => "",
                        'amount' => "0.00",
                        'status' => "",
                        'transdate' => xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",time()));

    return $data;
}

?>
