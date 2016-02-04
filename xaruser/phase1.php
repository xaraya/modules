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
    //Psspl:import the query class 
    sys::import('xaraya.structures.query');
    function payments_user_phase1()
    {
        //Psspl:Create the Query object
        $q = new Query();
        $prefix = xarDB::getPrefix();
        //if (!xarSecConfirmAuthKey()) return;
        if (!xarSecurityCheck('SubmitPayments')) return;
        
        //Psspl:Implemented the code for return url.
        //if(!xarVarFetch('return_url', 'array', $data['return_url'],  NULL, XARVAR_DONT_SET)) {return;}
        if (!xarVarFetch('allowEdit_Payment', 'int', $data['allowEdit_Payment'],   null,    XARVAR_DONT_SET)) {return;}
        /*Psspl:Removed the condition for checking paymentmethod selected.  
        if (!xarVarFetch('paymentmethod',      'int:0:', $paymentmethod,   null,    XARVAR_DONT_SET)) {return;}
        */
        //Psspl:check if user select 'MakeChanges'.
        if (!xarVarFetch('MakeChanges',      'int:0:', $MakeChanges,   null,    XARVAR_DONT_SET)) {return;}
        //Psspl:Checked the condition for Makechanges.
        if($MakeChanges)
        {
            if (!xarVarFetch('paymentmethod',      'int:0:', $paymentmethod,   null,    XARVAR_DONT_SET)) {return;}
        }
        
        /*
        //Psspl:Implemented the code for return url.
        $return_url_property = DataPropertyMaster::getProperty(array('name' => 'array'));       
        $return_url_property->initialization_associative_array = 1;         
        $return_url_property->checkInput('return_url');
        $data['return_url'] = $return_url_property->value;
        */
        //if(!xarVarFetch('return_url', 'str:1:', $return_url,  "a:0:{}", XARVAR_DONT_SET)) {return;}
        $return_url = xarSession::getVar('return_url');
        try {
            $data['return_url'] = unserialize($return_url);
        } catch (Exception $e) {
            $data['return_url'] = array();
        }
        
        $data['authid'] = xarSecGenAuthKey();
        
        $authid=$data['authid'];
        
        //Psspl : added time for more security and resolving error in paypal of unique transaction ID.
        $authid .= time();
        
        xarSession::setVar('AUTHID',$authid);
        
        //Psspl: Fetched the gateway id from the module variables 
        $gatewayid=xarModVars::get('payments', 'gateway');
        //Psspl:IF the input is not good repropose the previous page.       
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
        $fields = unserialize(xarSession::GetVar('paymentfields'));
        //Psspl:set the fieldvalues previously selected.
        if(($fields != null || $fields != '') && $MakeChanges) {
            $paymentobject->setFieldValues($fields);
        }
        $data['payment_object'] = $paymentobject;
        $data['payment_properties'] = $paymentobject->getProperties();


        // Get the order information
        $orderobjectname = xarModVars::get('payments','orderobject');
        $orderobject = DataObjectMaster::getObject(array('name' => $orderobjectname));
        if(!$MakeChanges) {
            // Check for an order object, suppress exception if not found
            $isvalid = $orderobject->checkInput();
            if (!$isvalid) {
                $data['order_object'] = $orderobject;
               //Psspl:Added the code for saferpay gateway support.
                if($gatewayid == PAYMENT_GATEWAY_SAFERPAY){ 
                    $data['object'] = $data['order_object'];
                    
                    $data['MakeChanges']=null;
                    
                    xarSession::setVar('error_message' , "");
                    
                    return xarTplModule('payments','user', 'amount',$data);
                }
                $data['order_properties'] = $orderobject->getProperties();
                return xarTplModule('payments','user', 'onestep',$data);
            }
            $itemid = $orderobject->createItem();
            xarSession::setVar('orderfields',serialize(xarSession::getVar('orderfields')));
        } else {
            //Psspl:set the fieldvalues previously selected.
            $fields = unserialize(xarSession::getVar('orderfields'));
            if(($fields != null || $fields != '')) {
                $orderobject->setFieldValues($fields);
            }
            //Psspl: modified the code for return url.
            $data['return_url'] = unserialize(xarSession::getVar('return_url'));
//            xarSession::delVar('return_url'); 
        }
        $data['order_object'] = $orderobject;
        $data['order_properties'] = $orderobject->getProperties();

        //Psspl:Save the order object information.
        // We have valid input, save it
        $paymentfields = $paymentobject->getFieldValues();
        xarSession::SetVar('paymentfields',serialize($paymentfields));
        $orderfields = $orderobject->getFieldValues();
        xarSession::SetVar('orderfields',serialize($orderfields));
        
        //Psspl: modified the code for storing return url into session.
//        xarSession::setVar('return_url',serialize($data['return_url']));
        
        // Check for demo mode 
        $demousers = unserialize(xarModVars::get('payments','demousers'));
        if (xarModVars::get('payments','enable_demomode') && in_array(xarUserGetVar('uname'),$demousers)) {
            return xarTplModule('payments','user','demomode1',array('returnurl' => ''));
        }

        //Psspl:Added the code for paypal gateway.  
        if ($gatewayid == PAYMENT_GATEWAY_PAYPAL or $gatewayid == PAYMENT_GATEWAY_SAFERPAY or $gatewayid == PAYMENT_GATEWAY_GESTPAY)
        {
            //Psspl:Commented the repeated code.
            //Psspl : Commented the security check statement for resolving error.           
            //if (!xarSecConfirmAuthKey()) return;
            //$data['authid'] = xarSecGenAuthKey();

            $paymentfields = $paymentobject->getFieldValues();
            xarSession::SetVar('paymentfields',serialize($paymentfields));
            $orderfields = $orderobject->getFieldValues();
            $orderfields['amount'] = $orderfields['net_amount'];
            
            xarSession::SetVar('orderfields',serialize($orderfields));
            //Psspl: modified the code for storing return url into session.
//            xarSession::setVar('return_url',serialize($data['return_url']));
            
            if($gatewayid == PAYMENT_GATEWAY_PAYPAL)
                xarSession::setVar('PAYPAL_FLAG','Inactive');
            //Psspl:Added the code for saferpay gateway support.
            if($gatewayid == PAYMENT_GATEWAY_SAFERPAY){
                xarSession::setVar('SAFERPAY_FLAG','Inactive');
                if($orderfields['net_amount'] <= 0) {
                    
                    $amount_error = "*Amount not valid";
                    xarSession::setVar('error_message' , $amount_error);
                    $data['object'] = $data['order_object'];
                    $data['properties'] = $data['order_properties'];
                    $data['errorFlag'] = 1;
                    $data['MakeChanges'] = null;
                    return xarTplModule('payments','user', 'amount',$data);
                }
            }
            
            $object = DataObjectMaster::getObject(array('name' => 'payments_gateways'));
            $module_id = xarMod::getRegID(xarMod::getName());
            $object->getItem(array('itemid' => xarModItemVars::get('payments','gateway',$module_id)));
            $data['gateway'] = $object->getFieldValues();

            if (xarModVars::get('payments','runpayments')) 
            {
                //sys::import('modules.payments.class.' . $data['gateway']['class']);
                include_once(sys::code().$data['gateway']['class_path']);
                if(class_exists($data['gateway']['class'])) 
                {
                    $objgateway = new $data['gateway']['class'];
                    xarSession::setVar('gateway', $data['gateway']['class']);
                    if ($data['gateway']['id'] == PAYMENT_GATEWAY_PAYPAL or $data['gateway']['id'] == PAYMENT_GATEWAY_SAFERPAY or $data['gateway']['id'] = PAYMENT_GATEWAY_GESTPAY)
                    {
                        // ICETODO hardcoded
                        $args = array();
                        switch ($orderfields['amount']) {
                            case 30:
                            $args['title'] = xarML('Attestation');
                            $args['description'] = xarML('Attestation');
                            break;
                            case 48:
                            $args['title'] = xarML('Basis Package Subscription');
                            $args['description'] = xarML('Basis Package Subscription');
                            break;
                            case 58:
                            $args['title'] = xarML('Family Package Subscription');
                            $args['description'] = xarML('Family Package Subscription');
                            break;
                        }
                        
                        //$authid=$data['authid'];
                        //xarSession::setVar('AUTHID',$authid);
                        $response = $objgateway->update_status();
                    }
                    else
                    {
                        $response = $objgateway->update_status();
                    }
                    $data['status'] = $response;
                    //Psspl:Added the code for saferpay gateway support.
                    if($data['gateway']['id'] == PAYMENT_GATEWAY_SAFERPAY or $data['gateway']['id'] = PAYMENT_GATEWAY_GESTPAY) {
                        if(xarSession::getVar('error_message')){
                            
                            $data['object'] = $data['order_object'];
                            $data['properties'] = $data['order_properties'];
                            $data['errorFlag'] = 1;
                            $data['MakeChanges'] = null;
                            return xarTplModule('payments','user', 'amount',$data);
                        }
                    }
                    if(xarSession::getVar('error_message'))
                    {
                        xarController::redirect(xarModURL('payments', 'user', 'phase1',array('paymentmethod'=>$paymentmethod,'MakeChanges'=>1,'errorFlag'=>1)));
                        return true;
                    }
                } 
                else 
                {
                    throw new ClassNotFoundException($data['gateway']['class']);
                }
            }
       }
        return $data;
    }

?>
