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
function labaccounting_ledgers_create($args)
{
    extract($args);
    
    if (!xarVarFetch('parentid', 'id', $parentid, $parentid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'id', $ownerid, $ownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('accttype', 'str::', $accttype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('chartacctnum', 'str::', $chartacctnum, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('account_title', 'str:1:', $account_title, $account_title, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('normalbalance', 'str::', $normalbalance, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    
//    if(!$returnurl) $returnurl = $_SERVER['HTTP_REFERER'];
    if(!$returnurl) $returnurl = xarServerGetVar('HTTP_REFERER');
    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'ledgers', 'view');

    $ledgerid = xarModAPIFunc('labaccounting',
                        'ledgers',
                        'create',
                        array('parentid'    => $parentid,
                            'ownerid'      => $ownerid,
                            'accttype'      => $accttype,
                            'chartacctnum'  => $chartacctnum,
                            'account_title' => $account_title,
                            'normalbalance' => $normalbalance,
                            'notes'         => $notes));


    if (!isset($ledgerid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'ledgers', 'display', array('ledgerid' => $ledgerid));

    xarSessionSetVar('statusmsg', xarMLByKey('LEDGERCREATED'));

    xarResponseRedirect($returnurl);

    return true;
}

?>
