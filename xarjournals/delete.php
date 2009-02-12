<?php
/**
 * labAccounting Module - An account management module using journals and journals
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage labAccounting Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego
 */
function labaccounting_journals_delete($args)
{
    
    if (!xarVarFetch('journalid', 'id', $journalid)) return;
    if (!xarVarFetch('returnurl', 'str', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);
    
    $item = xarModAPIFunc('labaccounting',
                         'journals',
                         'get',
                         array('journalid' => $journalid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

//    if (!xarSecurityCheck('DeleteXTask',1,'Item',$journalid)) return;

    if(!$returnurl) $returnurl = xarServerGetVar('HTTP_REFERER');

    if (empty($confirm)) {
        $data = xarModAPIFunc('labaccounting','admin','menu');

        $data['journalid'] = $journalid;
        $data['returnurl'] = $returnurl;

        $data['item'] = $item;
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    
    $transactionlist = xarModAPIFunc('labaccounting','journaltransactions','getall',array('journalid'=>$journalid));
    
    if($transactionlist === false) return;
    
    foreach($transactionlist as $transinfo) {
        if (!xarModAPIFunc('labaccounting',
                         'journaltransactions',
                         'delete',
                         array('transactionid' => $transinfo['transactionid']))) {
            return;
        }
    }
    
    if (!xarModAPIFunc('labaccounting',
                     'journals',
                     'delete',
                     array('journalid' => $journalid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Journal Deleted'));

    xarResponseRedirect($returnurl);

    return true;
}

?>
