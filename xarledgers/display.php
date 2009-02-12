<?php
/**
 * XProject Module - A simple task management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego
 */
function labaccounting_ledgers_display($args)
{
    extract($args);
    
    if (!xarVarFetch('ledgerid', 'id', $ledgerid)) return;
    if (!xarVarFetch('monthshown', 'isset::', $monthshown, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('yearshown', 'isset::', $yearshown, NULL, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('labaccounting','admin','menu');
    
    $data['monthshown'] = $monthshown;
    
    $data['yearshown'] = $yearshown ? $yearshown : date("Y");
    
    $data['ledgerid'] = $ledgerid;

    $item = xarModAPIFunc('labaccounting',
                          'ledgers',
                          'get',
                          array('ledgerid' => $ledgerid));

    if (!isset($item)) return;
    
    list($item['title']) = xarModCallHooks('item',
                                         'transform',
                                         $item['ledgerid'],
                                         array($item['account_title']));
    
    $data['item'] = $item;
    $data['authid'] = xarSecGenAuthKey();
    $data['title'] = $item['title'];

    $data['parentid'] = "";
    $data['parentname'] = "";
    $data['parenturl'] = "";
    if($item['parentid'] > 0) {
        $parentinfo = xarModAPIFunc('labaccounting',
                              'ledgers',
                              'get',
                              array('ledgerid' => $item['parentid']));
                              
        if (xarCurrentErrorType() == XAR_SYSTEM_EXCEPTION && xarCurrentErrorID() == 'ID_NOT_EXIST') {
            xarErrorHandled();
        }
    
        if ($parentinfo) {
            $data['parentid'] = $parentinfo['ledgerid'];
            $data['parentname'] = $parentinfo['title'];
            $data['parenturl'] = xarModURL('labaccounting', 'ledgers', 'display', array('ledgerid' => $data['parentid']));
        }
    }
    
    $transactionlist = xarModAPIFunc('labaccounting', 'ledgertransactions', 'getall', array('ledgerid' => $ledgerid,'yearshown' => $yearshown,'monthshown' => $monthshown));
    
    if($transactionlist === false) return;
    
    $data['transactionlist'] = $transactionlist;
    
    // get last X transactions of prior month before and first X of next month after?
    $startdate = xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",mktime(0,0,0,$monthshown,1,$yearshown));
    
    $beginningbalance = xarModAPIFunc('labaccounting', 'ledgertransactions', 'getbalance',
                            array('ledgerid' => $ledgerid,
                                'balancedate' => $startdate));
    
    if($beginningbalance === false) return;
    
    $data['startdate'] = $startdate;
    
    $data['beginningbalance'] = $beginningbalance;

    return $data;
}
?>