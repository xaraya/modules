<?php

function labaccounting_journaltransactions_multi_create($args)
{
    if (!xarVarFetch('journalid',  'isset', $journalid)) {return;}
    if (!xarVarFetch('title', 'array::', $title)) return;
    if (!xarVarFetch('transtype', 'array::', $transtype, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('amount', 'array::', $amount, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('transdate', 'array::', $transdate, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('details', 'str:1:', $details, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('transnum', 'str:1:', $transnum, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('source', 'id', $source, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sourceid', 'str::', $sourceid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('verified', 'str::', $verified, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cleared', 'str::', $cleared, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);
    
    if (!xarSecConfirmAuthKey()) return;
    
//    if(!$returnurl) $returnurl = $_SERVER['HTTP_REFERER'];
//    if(!$returnurl) $returnurl = xarServerGetVar('HTTP_REFERER');
    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'journals', 'display', array('journalid' => $journalid));

    $ttl_transactions = count($title);
    
    foreach($title as $k => $item_title) {
        $item_transtype = $transtype[$k];
        $item_transdate = $transdate[$k];
        $item_amount = $amount[$k];
        
        if(!empty($item_title)) {
            $transid = xarModAPIFunc('labaccounting',
                                'journaltransactions',
                                'create',
                                array('journalid'       => $journalid,
                                    'title'             => $item_title,
                                    'transtype'         => $item_transtype,
                                    'amount'            => $item_amount,
                                    'transdate'         => $item_transdate,
                                    
                                    'details'           => $details,
                                    'creatorid'         => xarSessionGetVar('uid'),
                                    'transnum'          => $transnum,
                                    'source'            => $source,
                                    'sourceid'          => $sourceid,
                                    'status'            => $status,
                                    'verified'          => $verified,
                                    'cleared'           => $cleared,
                                    'isinvoice'         => 0));
        }    
    
        if (!isset($transid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    }
    
    xarSessionSetVar('statusmsg', xarML('Transactions Created'));

    xarResponseRedirect($returnurl);

    return true;
}

?>
