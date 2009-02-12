<?php

function labaccounting_user_test()
{
    $data = array();
/*
    $chartlist = pnModAPIFunc('labAccounting',
                            'user',
                            'getchartdropdown');

                            
    foreach($chartlist as $chartinfo) {
        echo "<br>".$chartinfo['id']." - ".$chartinfo['name'];
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
            $object->properties['active']->value = false;
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
//            echo "<pre>"; print_r($object); echo "</pre>";
            $itemid = $object->createItem();
            if (empty($itemid)) return "failed!";
            echo " :: ".$itemid;
        }    
    }
    echo "<br><br><br>end test.";
  */
    $object = xarModAPIFunc('dynamicdata','user','getitems',
                          array('module'    => 'labaccounting',
                                'itemtype'  => 5,
                                'numitems'  => -1,
                                'startnum'  => 0,
                                'getobject' => 1));
    $data['object'] = &$object;                                
echo "<pre>"; print_r($data['object']); die("</pre>");
    return $data;
}
?>