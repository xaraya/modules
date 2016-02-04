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
function payments_user_phase3()
{
    if (!xarSecurityCheck('SubmitPayments')) return;
    //Psspl:Implemented the code for return url.
    //if(!xarVarFetch('return_url', 'array', $data['return_url'],  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('allowEdit_Payment', 'int', $data['allowEdit_Payment'],   null,    XARVAR_DONT_SET)) {return;}
    
     //Psspl:Implemented the code for return url.
     $return_url_property = DataPropertyMaster::getProperty(array('name' => 'array'));      
     $return_url_property->initialization_associative_array = 1;         
     $return_url_property->checkInput('return_url');
     $data['return_url'] = $return_url_property->value;           

    //Psspl: modified the code for return url.
    $data['return_url'] = unserialize(xarSession::GetVar('return_url'));
    xarSession::delVar('return_url'); 

    // Check for demo mode 
    $demousers = unserialize(xarModVars::get('payments','demousers'));
    if (xarModVars::get('payments','enable_demomode') && in_array(xarUserGetVar('uname'),$demousers)) {
        $data['status'] = xarML('A simulated payment has been completed');
        if(!empty($data['return_url']['success_return_link'])){
            xarController::redirect($data['return_url']['success_return_link']);          
            return true;
        } 
        return $data;
    }

    //Psspl:Added the code for paypal gateway.
    $flag=xarSession::getVar('PAYPAL_FLAG');
    if ($flag == 'ACTIVE')
    {
        xarSession::setVar('PAYPAL_FLAG','Inactive');
        
        //Psspl: modified the code for return url.
        $data['return_url'] = unserialize(xarSession::GetVar('return_url'));
        xarSession::delVar('return_url'); 
        
        $object = DataObjectMaster::getObject(array('name' => 'payments_gateways'));
        $module_id = xarMod::getRegID(xarMod::getName());
        $object->getItem(array('itemid' => xarModVars::get('payments','gateway',$module_id)));
        $data['gateway'] = $object->getFieldValues(); 
        
        $gateway=$data['gateway']['name'];
        
        sys::import('modules.payments.class.paypalstatus');
        $objPaypalstatus = new paypalstatus();
        $data['status'] = $objPaypalstatus->displayStatus();
        
        $orderfields = unserialize(xarSession::getVar('orderfields'));
        xarSession::setVar('product_amount',$orderfields['amount']);

        //Psspl : Added code for success return link.
        if(!empty($data['return_url']['success_return_link'])){
            xarController::redirect($data['return_url']['success_return_link']);            
            return true;
        }        
        return $data;
    }   
    //Psspl:Added the code for saferpay gateway support.    
    $saferpay_flag = xarSession::getVar('SAFERPAY_FLAG');
    if ($saferpay_flag == 'ACTIVE'){
        xarSession::setVar('SAFERPAY_FLAG','Inactive');
        
        //Psspl: modified the code for storing return url into session.
        $data['return_url'] = unserialize(xarSession::getVar('return_url'));
        xarSession::delVar('return_url');
        
        $object = DataObjectMaster::getObject(array('name' => 'payments_gateways'));
        $module_id = xarMod::getRegID(xarMod::getName());
        $object->getItem(array('itemid' => xarModVars::get('payments','gateway',$module_id)));
        $data['gateway'] = $object->getFieldValues(); 
        
        $gateway=$data['gateway']['name'];
        sys::import('modules.payments.class.' . strtolower($data['gateway']['class']));
        
        $objgateway = new $data['gateway']['class']();
        $data['status'] = $objgateway->displayStatus();
        
        // ICETODO hardcoded
        $orderfields = unserialize(xarSession::getVar('orderfields'));
        xarSession::setVar('product_amount',$orderfields['net_amount']);
        // End ICETODO hardcoded
        
        // Remove the session vars we used
        xarSession::setVar('orderfields',serialize(array()));
        xarSession::setVar('paymentfields',serialize(array()));
        
/*        if(!empty($data['return_url']['success_return_link'])){
            //Psspl:Implemented the code for calling success return API function.
            $success_return = explode("," , $data['return_url']['success_return_link']);
             
            xarModAPIFunc($success_return[0],$success_return[1],$success_return[2] ,array('status' => $data['status'] , 'success_return_link' => $data['return_url']['success_return_link']));
            return true;
        }
*/        //Psspl : Added code for success return link.
        if(!empty($data['return_url']['success_return_link'])){
            xarController::redirect($data['return_url']['success_return_link']);            
            return true;
        }        
        return $data;
    }
    
    //Psspl:Added the code for GestPay gateway support.    
    
//    xarSession::setVar('GESTPAY_FLAG','ACTIVE');
    $gestpay_flag = xarSession::getVar('GESTPAY_FLAG');
    if ($gestpay_flag == 'ACTIVE'){
        xarSession::setVar('GESTPAY_FLAG','Inactive');
        
        //Psspl: modified the code for storing return url into session.
        $data['return_url'] = unserialize(xarSession::getVar('return_url'));
        xarSession::delVar('return_url');
        
        $object = DataObjectMaster::getObject(array('name' => 'payments_gateways'));
        $module_id = xarMod::getRegID(xarMod::getName());
        $object->getItem(array('itemid' => xarModVars::get('payments','gateway',$module_id)));
        $data['gateway'] = $object->getFieldValues(); 
        
        $gateway = $data['gateway']['name'];
        sys::import('modules.payments.class.' . strtolower($data['gateway']['class']));
        
        $objgateway = new $data['gateway']['class']();
        $data['status'] = $objgateway->displaystatus();
        
        // Remove the session vars we used
        xarSession::setVar('orderfields',serialize(array()));
        xarSession::setVar('paymentfields',serialize(array()));
        
        //Psspl : Added code for success return link.
        if(!empty($data['return_url']['success_return_link'])){
            xarController::redirect($data['return_url']['success_return_link']);            
            return true;
        } 
        return $data;
    }
        if (!xarVarFetch('paymentmethod',      'int:0:', $paymentmethod,   0,    XARVAR_DONT_SET)) {return;}

        //Psspl:Get the Item of selected gateway.
        $object = DataObjectMaster::getObject(array('name' => 'payments_gateways'));
        $module_id = xarMod::getRegID(xarMod::getName());
        $object->getItem(array('itemid' => xarModVars::get('payments','gateway',$module_id)));
        $data['gateway'] = $object->getFieldValues(); 
        
        $object = DataObjectMaster::getObject(array('name' => 'payments_paymentmethods'));
        $object->getItem(array('itemid' => $paymentmethod));
        $data['paymentmethod'] = $object->getFieldValues();
        
        //Psspl: Get value of submit button pressed on previous page.
        if (!xarVarFetch('MakeChanges',      'str', $MakeChanges,  "",    XARVAR_NOT_REQUIRED)) {return;}
        if($MakeChanges) {
            $module_id = xarSession::getVar('clientmodule');
            $process = xarModVars::get('payments', 'process',$module_id);
            //Psspl:Modified the code for allowEdit_payment.
            switch ($process) {
                case 0:
                default:
                    return xarTplModule('payments','user','errors',array('layout' => 'no_process'));                
                case 1:
                    xarController::redirect(xarModURL('payments', 'user', 'phase1',array('paymentmethod'=>$paymentmethod,'MakeChanges'=>1 , 'allowEdit_Payment' => $data['allowEdit_Payment'])));
                case 2:
                    xarController::redirect(xarModURL('payments', 'user', 'phase1',array('paymentmethod'=>$paymentmethod,'MakeChanges'=>1 , 'allowEdit_Payment' => $data['allowEdit_Payment'])));
                case 3:
                    xarController::redirect(xarModURL('payments', 'user', 'onestep',array('paymentmethod'=>$paymentmethod,'MakeChanges'=>1 , 'allowEdit_Payment' => $data['allowEdit_Payment'])));
            }
            return true;
        }

        // Get the payments data
        $paymentobject = DataObjectMaster::getObject(array('name' => 'payments_ccpayments'));
        $fields = unserialize(xarSession::GetVar('paymentfields'));
        $paymentobject->setFieldValues($fields);
        $data['payment_object'] = $paymentobject;
        $data['payment_properties'] = $paymentobject->getProperties();
        $data['authid'] = xarSecGenAuthKey();

        // Save the transaction to the db if so configured
        if (xarModVars::get('payments','savetodb')) {
            $paymentobject->createItem();
        }

        //Psspl: Check the payment gateway class is available under class folder.
        //If yes then process the credit card transaction using that class.
        if (xarModVars::get('payments','runpayments')) {
            sys::import('modules.payments.class.' . strtolower($data['gateway']['class']));
            if(class_exists($data['gateway']['class'])) {
                $objgateway = new $data['gateway']['class'];
                xarSession::setVar('gateway', $data['gateway']['class']);            
                $response = $objgateway->update_status();
                $data['status'] = $response;
                //Psspl:Added the code for Error Handling.
                if(xarSession::getVar('error_message'))
                {
                    //Psspl: modified the code for allowEdit_payment.
                    xarController::redirect(xarModURL('payments', 'user', 'onestep',array('paymentmethod'=>$paymentmethod,'MakeChanges'=>1,'errorFlag'=>1,'allowEdit_Payment' => $data['allowEdit_Payment'])));
                    return true;
                }
            } else {
                throw new ClassNotFoundException($data['gateway']['class']);
            }
        }
        
        if (xarModVars::get('payments','alertemail')) {
            
        }

        // Update the order information
        $data['orderobject'] = null;
        $orderobjectname = xarModVars::get('payments','orderobject');
        $orderobject = DataObjectMaster::getObject(array('name' => $orderobjectname));
        $fields = unserialize(xarSession::GetVar('orderfields'));
        $orderobject->setFieldValues($fields);
        $orderobject->updateItem(array('itemid' => $fields['id']));
        
        // Remove the session vars we used
        xarSession::setVar('orderfields',serialize(array()));
        xarSession::setVar('paymentfields',serialize(array()));
        
       if(!empty($data['return_url']['success_return'])){ 
            //Psspl:Implemented the code for calling success return API function.
            $success_return = explode("," , $data['return_url']['success_return']);
            xarModAPIFunc($success_return[0],$success_return[1],$success_return[2] ,array('status' => $data['status'] , 'success_return_link' => $data['return_url']['success_return_link']));
            
            return true;
        }
        //Psspl : Added code for success return link.
        if(!empty($data['return_url']['success_return_link'])){
            xarController::redirect($data['return_url']['success_return_link']);            
            return true;
        }
        return $data;
    }

?>