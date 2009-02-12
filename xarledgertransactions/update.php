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
function labaccounting_ledgertransactions_update($args)
{
    extract($args);
    
    if (!xarVarFetch('transactionid', 'id', $transactionid)) return;
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
    if(!$returnurl) $returnurl = xarServerGetVar('HTTP_REFERER');
    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'ledgers', 'view');

    $transactioninfo = xarModAPIFunc('labaccounting',
                        'ledgertransactions',
                        'get',
                        array('transactionid'    => $transactionid));

    if(!xarModAPIFunc('labaccounting',
                        'ledgertransactions',
                        'update',
                        array('transactionid'    => $transactionid,
                            'creatorid'     => $creatorid,
                            'title'         => $title,
                            'details'       => $details,
                            'transnum'      => $transnum,
                            'amount'        => $amount,
                            'status'        => $status,
                            'transdate'     => $transdate))) { return; }


    if (!isset($ledgerid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'ledgers', 'display', array('ledgerid' => $transactioninfo['ledgerid']));

    xarSessionSetVar('statusmsg', xarMLByKey('LEDGERTRANSACTIONUPDATED'));

    xarResponseRedirect($returnurl);

    return true;
}

?>
