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
function labaccounting_journaltransactions_updateledgers($args)
{
    extract($args);
    
    if (!xarVarFetch('transactionid', 'id', $transactionid)) return;
    if (!xarVarFetch('journalid',  'isset', $journalid)) {return;}
    if (!xarVarFetch('ledgeramounts', 'isset::', $ledgeramounts, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    
//    if(!$returnurl) $returnurl = $_SERVER['HTTP_REFERER'];
    if(!$returnurl) $returnurl = xarServerGetVar('HTTP_REFERER');
    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'journals', 'display', array('journalid' => $journalid));

    $transactioninfo = xarModAPIFunc('labaccounting',
                        'journaltransactions',
                        'get',
                        array('transactionid'   => $transactionid));

    if(!xarModAPIFunc('labaccounting',
                        'journaltransactions',
                        'updateledgers',
                        array('journaltransid'   => $transactionid,
                            'journalid'         => $journalid,
                            'ledgeramounts'     => $ledgeramounts))) { return; }


    if (!isset($journalid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    if(!$returnurl) $returnurl = xarModURL('labaccounting', 'journals', 'display', array('journalid' => $transactioninfo['journalid']));

    xarSessionSetVar('statusmsg', xarMLByKey('JOURNALLEDGERSUPDATED'));

    xarResponseRedirect($returnurl);

    return true;
}

?>
