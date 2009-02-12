<?php

function labaccounting_admin_populatechart()
{
    $object = xarModAPIFunc('dynamicdata','user','getitems',
                          array('module'    => 'labaccounting',
                                'itemtype'  => 5,
                                'numitems'  => -1,
                                'startnum'  => 0,
                                'getobject' => 1));

    $activeaccounts = array(1020,1040,1100,1500,1510,1520,2000,2335,2390,2730,2740,4000,5000,5200,5730,6100,6200,6650,6850,8050,8400,8600,8650,8800);
                                
    if(count($object->items) <= 0) {
        $chartlist = xarModAPIFunc('labAccounting',
                                'user',
                                'getchartdropdown');
    
                                
        foreach($chartlist as $chartinfo) {
            if(!is_numeric($chartinfo['id'])) {
                $ledgertype = $chartinfo['id'];
            } else {
                $itemid = false;
                $object = xarModAPIFunc('dynamicdata','user','getobject',
                                                 array('module' => 'labaccounting', 'itemtype' => 5));
                if (!isset($object)) return;
                
                $object->properties['chartid']->value = 0;
                $object->properties['accountnumstart']->value = $chartinfo['id'];
                $object->properties['ledgertype']->value = $ledgertype;
                $object->properties['active']->value = in_array($chartinfo['id'], $activeaccounts) ? 1 : 0;
                $object->properties['accttype']->value = $chartinfo['name'];
                
                switch($ledgertype) {
                    case "Assets":
                        $object->properties['normalbalance']->value = "Debit";
                        break;
                    case "Liabilities":
                        $object->properties['normalbalance']->value = "Credit";
                        break;
                    case "Equity":
                        $object->properties['normalbalance']->value = "Debit";
                        break;
                    case "Revenue":
                        $object->properties['normalbalance']->value = "Credit";
                        break;
                    case "Cost of Goods Sold":
                        $object->properties['normalbalance']->value = "Debit";
                        break;
                    case "Expenses":
                        $object->properties['normalbalance']->value = "Debit";
                        break;
                }
                
                $object->properties['notes']->value = "";
                $itemid = $object->createItem();
                if (empty($itemid)) return "failed!";
            }    
        }
    }     
    
    xarResponseRedirect(xarModURL('labaccounting','admin','view'));
    return true;
}
?>