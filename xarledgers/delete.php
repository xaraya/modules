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
function labaccounting_ledgers_delete($args)
{
    
    if (!xarVarFetch('ledgerid', 'id', $ledgerid)) return;
    if (!xarVarFetch('returnurl', 'str', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);

    if (!isset($returnurl)) {
        $returnurl = $_SERVER['HTTP_REFERER'];
    }
    $item = xarModAPIFunc('labaccounting',
                         'ledgers',
                         'get',
                         array('ledgerid' => $ledgerid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteLedger',1,'Item',$ledgerid)) return;

    if (empty($confirm)) {
        $data = xarModAPIFunc('labaccounting','admin','menu');

        $data['ledgerid'] = $ledgerid;
        $data['returnurl'] = $returnurl;

        $data['item'] = $item;
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    
    if (!xarModAPIFunc('labaccounting',
                     'ledgers',
                     'delete',
                     array('ledgerid' => $ledgerid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Ledger Deleted'));

    xarResponseRedirect(xarModURL('labaccounting','ledgers','general'));

    return true;
}

?>
