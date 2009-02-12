<?php
/**
 * labAccounting Module - An account management module using ledgers and journals
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
function labaccounting_ledgertransactions_delete($args)
{
    
    if (!xarVarFetch('transactionid', 'id', $transactionid)) return;
    if (!xarVarFetch('returnurl', 'str', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);

    if (!isset($returnurl)) {
        $returnurl = $_SERVER['HTTP_REFERER'];
    }
    $item = xarModAPIFunc('labaccounting',
                         'ledgertransactions',
                         'get',
                         array('transactionid' => $transactionid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteLedger',1,'Item',$item['ledgerid'])) return;

    if (empty($confirm)) {
        $data = xarModAPIFunc('labaccounting','admin','menu');

        $data['transactiondetails'] = $item;
        $data['transactionid'] = $transactionid;
        $data['returnurl'] = $returnurl;

        $data['item'] = $item;
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    
    if (!xarModAPIFunc('labaccounting',
                     'ledgertransactions',
                     'delete',
                     array('transactionid' => $transactionid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Ledger Transaction Deleted'));

    xarResponseRedirect(xarModURL('labaccounting','ledgers','display', array('ledgerid' => $item['ledgerid'])));

    return true;
}

?>
