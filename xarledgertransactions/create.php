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
function labaccounting_ledgertransactions_create($args)
{
    extract($args);
    
    if (!xarVarFetch('ledgerid', 'id', $ledgerid)) return;
    if (!xarVarFetch('creatorid', 'str::', $creatorid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str:1:', $title, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('details', 'str::', $details, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('transnum', 'str::', $transnum, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('amount', 'str::', $amount, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('transdate', 'str::', $transdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    
//    if(!$returnurl) $returnurl = $_SERVER['HTTP_REFERER'];
//    if(!$returnurl) $returnurl = xarServerGetVar('HTTP_REFERER');
    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'ledgers', 'display',array('ledgerid'=>$ledgerid));
    
    if($transdate) {
        $transdate = strtotime($transdate);
        $transdate -= xarMLS_userOffset(strtotime($transdate)) * 3600;
        $transdate = gmdate('Y-m-d H:i:s', $transdate);
    }
    
    $transactionid = xarModAPIFunc('labaccounting',
                        'ledgertransactions',
                        'create',
                        array('ledgerid'    => $ledgerid,
                            'creatorid'     => $creatorid,
                            'title'         => $title,
                            'details'       => $details,
                            'transnum'      => $transnum,
                            'amount'        => $amount,
                            'status'        => $status,
                            'transdate'     => $transdate));


    if (!isset($ledgerid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'ledgertransactions', 'display', array('transactionid' => $transactionid));

    xarSessionSetVar('statusmsg', xarMLByKey('LEDGERTRANSACTIONCREATED'));

    xarResponseRedirect($returnurl);

    return true;
}

?>
