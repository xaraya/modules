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
function labaccounting_user_account($args)
{
    extract($args);
    if (!xarVarFetch('journalid', 'id', $journalid)) return;
    if (!xarVarFetch('monthshown', 'isset::', $monthshown, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('yearshown', 'isset::', $yearshown, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'isset', $startnum, NULL, XARVAR_DONT_SET)) {return;}

    $uid = xarSessionGetVar('uid');
    
    $data = xarModAPIFunc('labaccounting','admin','menu');
    
    $data['monthshown'] = $monthshown;
    
    $data['yearshown'] = $yearshown ? $yearshown : date("Y");
    
    $data['journalid'] = $journalid;
    $data['status'] = '';
    $data['authid'] = xarSecGenAuthKey();

    $item = xarModAPIFunc('labaccounting',
                          'journals',
                          'get',
                          array('journalid' => $journalid));

    if (!isset($item)) {
        $msg = xarML('Not authorized to access that account',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return $msg;
    }
    
    list($item['account_title']) = xarModCallHooks('item',
                                         'transform',
                                         $item['journalid'],
                                         array($item['account_title']));
    
    $data['item'] = $item;
    
    $journalidlist = array($journalid);
    
    $transactionlist = xarModAPIFunc('labaccounting','journaltransactions','getall',
                                    array('journalidlist'=>$journalidlist,
                                        'monthshown' => $monthshown,
                                        'yearshown' => $yearshown,
                                        'startnum' => $startnum));
    
    if($transactionlist === false) return;
    
    $data['transactionlist'] = $transactionlist;
    
    $data['beginningbalance'] = 0;
    
    $data['startdate'] = "";
    
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
