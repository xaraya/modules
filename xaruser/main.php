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
/**
 * Main user GUI function, entry point
 *
 */

    function payments_user_main()
    {
        // Security Check
        if (!xarSecurityCheck('ReadPayments')) return;
        if (!xarVarFetch('clientmodule','str',      $clientmodule, 'payments', XARVAR_NOT_REQUIRED)) return;
        
        //Psspl:Implemented the code for return url.
        //if(!xarVarFetch('return_url', 'array', $data['return_url'],  NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('allowEdit_Payment', 'int', $data['allowEdit_Payment'],   null,    XARVAR_DONT_SET)) {return;}
        
        // Check if a product id was passed
        if(!xarVarFetch('product_id', 'int', $data['product_id'],   null,    XARVAR_DONT_SET)) {return;}
        
        //Psspl:Implemented the code for return url.
        $return_url_property = DataPropertyMaster::getProperty(array('name' => 'array'));       
        $return_url_property->initialization_associative_array = 1;         
        $return_url_property->checkInput('return_url');
        $data['return_url'] = $return_url_property->value;
        
        $module_id = xarMod::getRegID($clientmodule);
        xarSession::setVar('clientmodule',$module_id);
        //Psspl: modified the code for deleting return url session.
        xarSession::delVar('return_url'); 

        //Pssspl:Set the session value Inactive for resolving error.
        xarSession::setVar('SAFERPAY_FLAG','Inactive');
        xarSession::setVar('PAYPAL_FLAG','Inactive');

        //Psspl:Added the code for saferpay, paypalstandard and gestpay.
        $gatewayid = xarModVars::get('payments', 'gateway');
        $valid_gatewayid = array('saferpay'=>PAYMENT_GATEWAY_SAFERPAY,
                                 'paypalstandard'=>PAYMENT_GATEWAY_PAYPAL,        
                                 'gestpay' => PAYMENT_GATEWAY_GESTPAY);        
        $result = in_array($gatewayid,$valid_gatewayid);

        // Add any parameters to be passed
        $args = array();
        if (isset($product_id)) $args['product_id'] = $product_id;
        
        switch (xarModItemVars::get('payments', 'process',$module_id)) {
            default:
                return xarTplModule('payments','user','errors',array('layout' => 'no_process'));                
            case 1:
                xarController::redirect(xarModURL('payments', 'user', 'amount'));
                break;
            case 2:
                if(!in_array($gatewayid,$valid_gatewayid))
                    xarController::redirect(xarModURL('payments', 'user', 'method'));
                else 
                    xarController::redirect(xarModURL('payments', 'user', 'amount'));
                break;
            case 3:
                if(!in_array($gatewayid,$valid_gatewayid))
                    xarController::redirect(xarModURL('payments', 'user', 'onestep', $args));
                else 
                    xarController::redirect(xarModURL('payments', 'user', 'amount'));
                break;   
            default:             
                $redirect = xarModVars::get('payments','frontend_page');
                if (!empty($redirect)) {
                    $truecurrenturl = xarServer::getCurrentURL(array(), false);
                    $urldata = xarMod::apiFunc('roles','user','parseuserhome',array('url'=> $redirect,'truecurrenturl'=>$truecurrenturl));
                    xarController::redirect($urldata['redirecturl']);
                } else {
                    xarController::redirect(xarModURL('payments', 'user', 'test'));
                }
                break;
        }
        return true;
    }
?>
