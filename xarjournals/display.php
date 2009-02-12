<?php
/**
 * AccessMethods Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AccessMethods Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego
 */
function labaccounting_journals_display($args)
{
    extract($args);
    if (!xarVarFetch('journalid', 'id', $journalid)) return;
    if (!xarVarFetch('monthshown', 'isset::', $monthshown, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('yearshown', 'isset::', $yearshown, date('Y'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'isset', $startnum, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('order', 'isset', $order, "chrono", XARVAR_DONT_SET)) {return;}

    $uid = xarSessionGetVar('uid');
    
    $data = xarModAPIFunc('labaccounting','admin','menu');
    
    $data['monthshown'] = $monthshown;
    
    $data['yearshown'] = $yearshown;
    
    $data['journalid'] = $journalid;
    $data['status'] = '';
    $data['authid'] = xarSecGenAuthKey();

    $item = xarModAPIFunc('labaccounting',
                          'journals',
                          'get',
                          array('journalid' => $journalid));

    if (!isset($item)) {
        $msg = xarML('Not authorized to access #(1) items',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return $msg;
    }
    
    list($item['account_title']) = xarModCallHooks('item',
                                         'transform',
                                         $item['journalid'],
                                         array($item['account_title']));
    
    
    $parentinfo = array();
    if($item['parentid'] > 0) {
        $parentinfo = xarModAPIFunc('labaccounting', 'journals', 'get',
                                            array('journalid' => $item['parentid']));
                                            
        if($parentinfo == false) return;
    }
    $data['parentinfo'] = $parentinfo;
    
    $subjournals = xarModAPIFunc('labaccounting', 'journals', 'getall', array('parentid' => $journalid));
                                        
    if($subjournals === false) return;
    
    $data['subjournals'] = $subjournals;
        
    $activeledgerlist = xarModAPIFunc('labaccounting', 'journals', 'getall_ledgers',
                                        array('journalid' => $journalid));
                                        
    if(!is_array($activeledgerlist)) return;
    
    $data['item'] = $item;
    
    $data['activeledgerlist'] = $activeledgerlist;
    
    $journalidlist = array($journalid);
    foreach($subjournals as $journalinfo) {
        $journalidlist[] = $journalinfo['journalid'];
    }
    
    $transactionlist = xarModAPIFunc('labaccounting','journaltransactions','getall',
                                    array('journalidlist'=>$journalidlist,
                                        'monthshown' => $monthshown,
                                        'yearshown' => $yearshown,
                                        'startnum' => $startnum));
    
    if($order != "chrono") {
        $transactionlist = array_reverse($transactionlist);
    }
    
    if($transactionlist === false) return;
    
    $data['transactionlist'] = $transactionlist;
    
    $data['order'] = $order;
    
    $ledgertransactionlist = xarModAPIFunc('labaccounting','journaltransactions','getall_ledgertransactions',array('journalid'=>$journalid));
    
    if($ledgertransactionlist === false) return;
    
    $data['ledgertransactionlist'] = $ledgertransactionlist;
    
    $startmonth = $monthshown ? $monthshown : 1;
    $balancedate = xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",mktime(0,0,0,$startmonth,1,$yearshown));
    $beginningbalance = array();
    $beginningbalance[0] = xarModAPIFunc('labaccounting', 'journaltransactions', 'getbalance',
                                array('journalid' => $journalid,
                                    'balancedate' => $balancedate));
    foreach($activeledgerlist as $ledgerinfo) {
        $beg_bal = xarModAPIFunc('labaccounting', 'ledgertransactions', 'getbalance',
                                array('ledgerid' => $ledgerinfo['ledgerid'],
                                    'balancedate' => $balancedate));
        
        if($beg_bal === false) return;
    
        $beginningbalance[$ledgerinfo['ledgerid']] = $beg_bal ? $beg_bal : 0;
    }
    
    $data['beginningbalance'] = $beginningbalance;
    
    $data['startdate'] = $balancedate;
    
    $itemsperpage = xarModGetUserVar('labaccounting', 'itemsperpage', $uid);
    $itemsperpage = $itemsperpage ? $itemsperpage : xarModGetVar('labAccounting', 'itemsperpage');
    if($itemsperpage > 0) {
        $data['pager'] = xarTplGetPager(
                                $startnum,
                                xarModAPIFunc('labaccounting', 'journaltransactions', 'countitems', 
                                            array('journalidlist'=>$journalidlist,
                                                    'monthshown' => $monthshown,
                                                    'yearshown' => $yearshown)),
                                xarModURL('labaccounting', 'journals', 'display', 
                                            array('journalid' => $journalid,
                                                    'monthshown' => $monthshown,
                                                    'yearshown' => $yearshown,
                                                    'startnum' => '%%')),
                                $itemsperpage);
    } else {
        $data['pager'] = "";
    }
    
    $hooks = xarModCallHooks('item',
                             'display',
                             $journalid,
                             xarModURL('labaccounting',
                                       'journals',
                                       'display',
                                       array('journalid' => $journalid)));
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }

    return $data;
}
?>
