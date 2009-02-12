<?php
/**
 * xTasks Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function labaccounting_journaltransactions_update($args)
{
    extract($args);
    
    if (!xarVarFetch('transactionid', 'id', $transactionid)) return;
    if (!xarVarFetch('journalid', 'id', $journalid)) return;
    if (!xarVarFetch('transtype', 'str:1:', $transtype, $transtype, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str:1:', $title, $title, XARVAR_NOT_REQUIRED)) return;
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

    if (!xarSecConfirmAuthKey()) return;
    
//    if(!$returnurl) $returnurl = $_SERVER['HTTP_REFERER'];
//    if(!$returnurl) $returnurl = xarServerGetVar('HTTP_REFERER');
//    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'journals', 'view');

    $transactioninfo = xarModAPIFunc('labaccounting',
                        'journaltransactions',
                        'get',
                        array('transactionid'    => $transactionid));

    if(!xarModAPIFunc('labaccounting',
                        'journaltransactions',
                        'update',
                        array('transactionid'    => $transactionid,
                            'journalid'         => $journalid,
                            'transtype'         => $transtype,
                            'title'             => $title,
                            'details'           => $details,
                            'creatorid'         => xarSessionGetVar('uid'),
                            'transnum'          => $transnum,
                            'amount'            => $amount,
                            'transdate'         => $transdate,
                            'isinvoice'         => $isinvoice,
                            'source'            => $source,
                            'sourceid'          => $sourceid,
                            'status'            => $status,
                            'verified'          => $verified,
                            'cleared'           => $cleared))) { return; }


    if (!isset($journalid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'journals', 'display', array('journalid' => $transactioninfo['journalid'], 'yearshown' => date('Y', strtotime($transdate))));

    xarSessionSetVar('statusmsg', xarMLByKey('JOURNALTRANSACTIONUPDATED'));

    xarResponseRedirect($returnurl);

    return true;
}

?>
