<?php

function labaccounting_journaltransactions_create($args)
{
    if (!xarVarFetch('journalid',  'isset', $journalid)) {return;}
    if (!xarVarFetch('transtype', 'str:1:', $transtype, "", XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str:1:', $title)) return;
    if (!xarVarFetch('details', 'str:1:', $details, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('transnum', 'str::', $transnum, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('amount', 'str::', $amount, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('transdate', 'str::', $transdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('isinvoice', 'checkbox::', $isinvoice, 0, XARVAR_NOT_REQUIRED)) return;
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
    $yearshown = date("Y", strtotime($transdate));
    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'journals', 'display', array('journalid' => $journalid, 'yearshown' => $yearshown));

    $transid = xarModAPIFunc('labaccounting',
                        'journaltransactions',
                        'create',
                        array('journalid'       => $journalid,
                            'transtype'         => $transtype,
                            'title'             => $title,
                            'details'           => $details,
                            'creatorid'         => xarUserGetVar('uid'),
                            'transnum'          => $transnum,
                            'amount'            => $amount,
                            'transdate'         => $transdate,
                            'isinvoice'         => $isinvoice,
                            'source'            => $source,
                            'sourceid'          => $sourceid,
                            'status'            => $status,
                            'verified'          => $verified,
                            'cleared'           => $cleared));


    if (!isset($transid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('TRANSACTIONCREATED'));

    xarResponseRedirect($returnurl);

    return true;
}

?>
