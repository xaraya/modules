<?php
/**
 * labAccounting Module - An account management module using ledgers and journals
 *
 * @package modules
 * @copyright (C) 2002-2005 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage labAccounting Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego
 */
function labaccounting_journaltransactions_delete($args)
{
    
    if (!xarVarFetch('transactionid', 'id', $transactionid)) return;
    if (!xarVarFetch('returnurl', 'str', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);

    if (!isset($returnurl)) {
        $returnurl = $_SERVER['HTTP_REFERER'];
    }
    $item = xarModAPIFunc('labaccounting',
                         'journaltransactions',
                         'get',
                         array('transactionid' => $transactionid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteLedger',1,'Item',$item['journalid'])) return;

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
                     'journaltransactions',
                     'delete',
                     array('transactionid' => $transactionid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Journal Transaction Deleted'));

    xarResponseRedirect(xarModURL('labaccounting','journals','display', array('journalid' => $item['journalid'])));

    return true;
}

?>
