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
function labaccounting_journals_create($args)
{
    extract($args);
    
    if (!xarVarFetch('parentid', 'id', $parentid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('owneruid', 'id', $owneruid, $owneruid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactid', 'id', $contactid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('currency', 'str::', $currency, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('account_title', 'str:1:', $account_title, $account_title, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('journaltype', 'str::', $journaltype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('agentuid', 'id', $agentuid, $agentuid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('acctnum', 'str::', $acctnum, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('acctlogin', 'str::', $acctlogin, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('accturl', 'str::', $accturl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('acctpwd', 'str::', $acctpwd, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('opendate', 'str::', $opendate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('closedate', 'str::', $closedate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('billdate', 'str::', $billdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invoicefreq', 'str::', $invoicefreq, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invoicefrequnits', 'str::', $invoicefrequnits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    
//    if(!$returnurl) $returnurl = $_SERVER['HTTP_REFERER'];
    if(!$returnurl) $returnurl = xarServerGetVar('HTTP_REFERER');
    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'journals', 'view');

    $journalid = xarModAPIFunc('labaccounting',
                        'journals',
                        'create',
                        array('parentid'        => $parentid,
                            'owneruid'          => $owneruid,
                            'contactid'         => $contactid,
                            'currency'          => $currency,
                            'account_title'     => $account_title,
                            'journaltype'       => $journaltype,
                            'agentuid'          => $agentuid,
                            'acctnum'           => $acctnum,
                            'acctlogin'         => $acctlogin,
                            'accturl'           => $accturl,
                            'acctpwd'           => $acctpwd,
                            'notes'             => $notes,
                            'opendate'          => $opendate,
                            'closedate'         => $closedate,
                            'billdate'          => $billdate,
                            'status'            => $status,
                            'invoicefreq'       => $invoicefreq,
                            'invoicefrequnits'  => $invoicefrequnits));


    if (!isset($journalid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('JOURNALCREATED'));

    xarResponseRedirect($returnurl);

    return true;
}

?>
