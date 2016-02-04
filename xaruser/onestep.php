<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

    sys::import('xaraya.structures.query');

    function payments_user_onestep()
    {
        if (!xarSecurityCheck('SubmitPayments')) return;
        
        //Psspl:Implemented the code for return url.
        //if(!xarVarFetch('return_url', 'array', $data['return_url'],  NULL, XARVAR_DONT_SET)) {return;}
        if (!xarVarFetch('allowEdit_Payment', 'int', $data['allowEdit_Payment'],   null,    XARVAR_DONT_SET)) {return;}
        
        // Check if a product id was passed
        if(!xarVarFetch('product_id', 'int', $data['product_id'],   null,    XARVAR_DONT_SET)) {return;}
        //Psspl:Implemented the code for return url.
    	$return_url_property = DataPropertyMaster::getProperty(array('name' => 'array')); 		
    	$return_url_property->initialization_associative_array = 1;         
    	$return_url_property->checkInput('return_url');
    	$data['return_url'] = $return_url_property->value;
        
        // Check for gateway
        $module_id = xarSession::getVar('clientmodule');
        $gatewayid = xarModVars::get('payments', 'gateway',$module_id);
        if (empty($gatewayid)) {
            return xarTplModule('payments','user','errors',array('layout' => 'no_gateway'));
        }

        // Check for the anonymous user
        $allowanonpay = xarModVars::get('payments', 'allowanonpay',$module_id);
        if (!xarUserIsLoggedIn() && !$allowanonpay) {
            xarController::redirect(xarModURL('roles','user','showloginform'));
            return true;
        }

        //Psspl:Create the Query object
        $q = new Query();
        $prefix = xarDB::getPrefix();

        //Psspl:check if user selected 'MakeChanges'.
        if (!xarVarFetch('MakeChanges',      'int:0:', $MakeChanges,   null,    XARVAR_DONT_SET)) {return;}
        //Psspl:Checked the condition for Makechanges.
        if($MakeChanges)
        {
            if (!xarVarFetch('paymentmethod',      'int:0:', $paymentmethod,   null,    XARVAR_DONT_SET)) {return;}
        }
          
        $data['authid'] = xarSecGenAuthKey();
        
        //Psspl:If the input is not good repropose the previous page.       
        //Psspl: Added code for filling the credit card type
        //       combobox from the payment_relation table
        $strCcType="";
        //Psspl:define array type.
        $ar1=array();       
        $relalionObject=DataObjectMaster::getObjectList(array('name' => 'payments_relation'));
        $data['properties2']=$relalionObject->getProperties();
        $data['items2']=$relalionObject->getItems(array('where' => "gateway_id eq $gatewayid"));
        foreach($data['items2'] as $key1=>$value1)
        {
            foreach($value1 as $k1 => $v1)
            {
                if($k1=='paymentmethod_id')
                {
                 $object1=DataObjectMaster::getObjectList(array('name' => 'payments_paymentmethods'));
                 $data['properties1']=$object1->getProperties();
                 $data['items3']=$object1->getItems(array('where' => "id eq $v1"));
                 foreach($data['items3'] as $key=>$value)
                 {
                    foreach($value as $k => $v)
                    {
                     if($k=='id')
                     {
                            //Pssp:Removed the unnesesary code.
                    //$strCcType.="$v,";
                            $id=$v;

                     }
                     if($k=='name')
                     {
                            //Pssp:Removed the unnesesary code.
                         //$strCcType.="$v;";
                            //Psspl:Stored data in array.
                            $ar1[$id]=$v;
                     }
                    }   
                 }
                }
            }
        }
        //Psspl:Sort the array.
        natsort($ar1);
        foreach ($ar1 as $key=>$val)
        {
            $strCcType.=$key.",".$val.";";
        }

        $len=strlen($strCcType);
        $query = "Update xar_dynamic_properties Set configuration='a:7:{s:14:\"display_layout\";s:7:\"default\";s:15:\"display_tooltip\";s:0:\"\";s:23:\"initialization_function\";s:0:\"\";s:19:\"initialization_file\";s:0:\"\";s:25:\"initialization_collection\";s:0:\"\";s:22:\"initialization_options\";s:$len:\"$strCcType\";s:25:\"initialization_other_rule\";s:0:\"\";}' where source='xar_payments_creditcards.cc_type'";
        if (!$q->run($query)) return;
        
        // Get the payment information
        $paymentobject = DataObjectMaster::getObject(array('name' => 'payments_ccpayments'));
        if(!$MakeChanges) {
            // If this is the first time we're here, display an empty form
        } else {
             //Psspl:set the fieldvalues previously selected.
            $fields = unserialize(xarSession::getVar('paymentfields'));
            if(($fields != null || $fields != '') && $MakeChanges) {
                $paymentobject->setFieldValues($fields);
            }
            //Psspl: modified the code for return url.
            $data['return_url'] = unserialize(xarSession::GetVar('return_url'));
            xarSession::delVar('return_url'); 
        }
        $data['payment_object'] = $paymentobject;
        $data['payment_properties'] = $paymentobject->getProperties();
        

        // Get the order information
        // Add any parameters to be passed
        $args['name'] = xarModVars::get('payments','orderobject');
        if (isset($product_id)) $args['product_id'] = $product_id;
        
        $orderobject = DataObjectMaster::getObject($args);
        if(!$MakeChanges) {
            // Check for an order object, suppress exception if not found
            $isvalid = $orderobject->checkInput(array(),1);
            if (!$isvalid) {
                // No values available, use defaults
                $orderobject->properties['id']->setValue(0);
                $orderobject->properties['currency']->setValue(xarModVars::get('payments', 'defaultcurrency',$module_id));
                $orderobject->properties['net_amount']->setValue(xarModVars::get('payments', 'defaultamount',$module_id));
//                $itemid = $orderobject->createItem();
            }
            xarSession::setVar('orderfields',serialize(xarSession::getVar('orderfields')));
        } else {
            //Psspl:set the fieldvalues previously selected.
            $fields = unserialize(xarSession::getVar('orderfields'));
            if(($fields != null || $fields != '')) {
                $orderobject->setFieldValues($fields);
            }
        }
        $data['order_object'] = $orderobject;
        $data['order_properties'] = $orderobject->getProperties();
        
        return $data;
    }

?>
