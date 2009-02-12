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
function labaccounting_journals_invoice($args)
{
    if (!xarVarFetch('transactionid', 'id', $transactionid)) return;
    if (!xarVarFetch('printable', 'isset', $printable, NULL, XARVAR_NOT_REQUIRED)) return;
    
    extract($args);

    $uid = xarSessionGetVar('uid');
    
    $data = xarModAPIFunc('labaccounting','admin','menu');
    
    $data['status'] = '';
    $data['authid'] = xarSecGenAuthKey();
    
    $data['transactionid'] = $transactionid;

    $transaction = xarModAPIFunc('labaccounting',
                          'journaltransactions',
                          'get',
                          array('transactionid' => $transactionid));
    if($transaction == false) return;
    
    $data['transaction'] = $transaction;
    
    $journalid = $transaction['journalid'];
    
    $data['journalid'] = $journalid;

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
    
    $data['item'] = $item;
    
    $invoicebalance = xarModAPIFunc('labaccounting', 'journaltransactions', 'getbalance',
                                array('journalid' => $journalid,
                                    'balancedate' => $transaction['transdate']));
    if($invoicebalance === false) return;
    
    $data['invoicebalance'] = $invoicebalance;

    $previnvoice = xarModAPIFunc('labaccounting',
                          'journaltransactions',
                          'getprevinvoice',
                          array('nextinvoice' => $transaction));
                          
    if($previnvoice == false) {
        // FOR ALL TRANSACTIONS FROM THE LAST INVOICE TO CURRENT
        $startdate = "";
        
        $data['beginningbalance'] = 0.00;
    } else {

        $startdate = $previnvoice['transdate'];
    
        $beginningbalance = xarModAPIFunc('labaccounting', 'journaltransactions', 'getbalance',
                                    array('journalid' => $journalid,
                                        'balancedate' => $previnvoice['transdate']));
        if($beginningbalance === false) return;
        
        $data['beginningbalance'] = $beginningbalance;
    }
    
    $enddate = $transaction['transdate'];
//echo $enddate." - ".$startdate."<pre>"; print_r($previnvoice); die("</pre>");
    
    $transactionlist = xarModAPIFunc('labaccounting','journaltransactions','getinvoice', 
                                    array('journalid'=>$journalid, 
                                        'startdate' => $startdate,
                                        'enddate' => $enddate));
    
    if($transactionlist === false) return;
    
    $data['transactionlist'] = $transactionlist;
    
    $data['startdate'] = $startdate;
    
    $data['enddate'] = $enddate;
    
    $contactinfo = array();
    if($item['contactid'] > 0) {
        $contactinfo = xarModAPIFunc('dossier', 'user', 'get', array('contactid' => $item['contactid']));
        
        if($contactinfo == false) return;
    
    }

    $data['contactinfo'] = $contactinfo;
    
    $location = array();
    if(isset($contactinfo['billinglocid']) && $contactinfo['billinglocid'] > 0) {
        $location = xarModAPIFunc('dossier','locations','getcontact',array('contactid' => $item['contactid'], 'locationid' => $contactinfo['billinglocid']));
        if($location === false) return;
    } elseif (isset($contactinfo['userid']) && $contactinfo['userid'] > 0) {
        $locid = xarModGetUserVar('labaccounting','myaddressid',$contactinfo['userid']);
        if($locid) {
            $location = xarModAPIFunc('dossier','locations','getcontact',array('contactid' => $item['contactid'], 'locationid' => $locid));
            if($location === false) return;
        }
    }    

    if(!isset($location)) {
        $locationlist = xarModAPIFunc('dossier','locations','getallcontact',array('contactid' => $item['contactid']));
        if(is_array($locationlist) && count($locationlist) > 0) {
            $location = $locationlist[0];
        }
    }
    
    $data['location'] = $location;    
    
    $agentinfo = array();
    $agentlocation = array();
    if($item['agentuid'] > 0) {
        $agentid = xarModGetUserVar('labaccounting', 'myagentid', $item['agentuid']);
        if($agentid > 0) {
            $agentinfo = xarModAPIFunc('dossier', 'user', 'get', array('contactid' => $agentid));
            
            if($agentinfo == false) return;
    
            $agentlocation = array();
            if ($agentinfo['userid'] > 0) {
                $agentlocid = xarModGetUserVar('labaccounting','myaddressid',$item['agentuid']);
                if($agentlocid) {
                    $agentlocation = xarModAPIFunc('dossier','locations','getcontact',array('contactid' => $agentid, 'locationid' => $agentlocid));
                    if($agentlocation === false) return;
                }
            } elseif($agentinfo['billinglocid'] > 0) {
                $agentlocation = xarModAPIFunc('dossier','locations','getcontact',array('contactid' => $agentid, 'locationid' => $agentinfo['billinglocid']));
                if($agentlocation === false) return;
            }    
        
            if(!isset($agentlocation)) {
                $agentlocationlist = xarModAPIFunc('dossier','locations','getallcontact',array('contactid' => $agentid));
                if(is_array($agentlocationlist) && count($agentlocationlist) > 0) {
                    $agentlocation = $agentlocationlist[0];
                }
            }
        }
    }

    $data['agentinfo'] = $agentinfo;
    $data['agentlocation'] = $agentlocation;
    
    if($printable) {
        return xarTplModule('labaccounting','journals','invoice', $data, 'print');
    } else {
        return $data;
    }
}
?>
