<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Select existing payment method or create new one to use for this transaction
 */
function shop_user_paymentmethod() 
{

    // Redirects at the start of the user functions are just a way to make sure someone isn't where they don't need to be
    $shippingaddress = xarSession::getVar('shippingaddress');
    if (empty($shippingaddress)) {
        xarResponse::redirect(xarModURL('shop','user','shippingaddress'));
        return true;
    }
    $shop = xarSession::getVar('shop');
    if (!xarUserIsLoggedIn() || empty($shop)) {
        xarResponse::redirect(xarModURL('shop','user','main'));
        return true;
    }

    if(!xarVarFetch('proceedsaved', 'str', $proceedsaved, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('proceednew', 'str', $proceednew, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('paymentmethod', 'str', $paymentmethod, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('remove', 'str', $remove, NULL, XARVAR_NOT_REQUIRED)) {return;}

    $cust = xarMod::APIFunc('shop','user','customerinfo'); 
    $data['cust'] = $cust; 

    sys::import('modules.dynamicdata.class.objects.master');
    sys::import('modules.dynamicdata.class.properties.master');

    $shippingobject = DataObjectMaster::getObject(array('name' => 'shop_shippingaddresses'));
    $shippingobject->getItem(array('itemid' => xarSession::getVar('shippingaddress')));
    $shippingvals = $shippingobject->getFieldValues();
    $data['shippingvals'] = $shippingvals;

    // Get the saved payment methods, if any exist
    $mylist = DataObjectMaster::getObjectList(array('name' => 'shop_paymentmethods'));
    $filters = array(
                     'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
                    'where' => 'customer eq ' . xarUserGetVar('id'),
                    );
    $paymentmethods = $mylist->getItems($filters);
    if (count($paymentmethods) > 0) {
        $data['paymentmethods'] = $paymentmethods;
    }

    $myfields = array('first_name', 'last_name', 'street_addr', 'city_addr', 'state_addr', 'postal_code', 'card_type','card_num', 'cvv2', 'exp_date');
    $data['myfields'] = $myfields;

    $paymentobject = DataObjectMaster::getObject(array('name' => 'shop_paymentmethods'));
    $properties = $paymentobject->getProperties();
    $data['properties'] = $properties;

    if ($remove) {
        if ($remove == xarSession::getVar('paymentmethod')) {
            xarSession::delVar('paymentmethod');
        }
        $paymentobject->getItem(array('itemid' => $remove));
        $paymentobject->deleteItem();
        xarResponse::redirect(xarModURL('shop','user','paymentmethod'));
        return true;
    }

    foreach ($myfields as $field) {
        $propids[$field] = 'dd_' . $properties[$field]->id; 
        $data[$field] = ''; 
    }
    $data['propids'] = $propids;

    $selectedpaymentmethod = xarSession::getVar('paymentmethod');
    if(!empty($selectedpaymentmethod)) {
        $data['paymentmethod'] = $selectedpaymentmethod;
    }

    // If we're using a saved payment method...
    if ($proceedsaved) {
        
        xarSession::setVar('paymentmethod',$paymentmethod);
        xarResponse::redirect(xarModURL('shop','user','order')); 
        return true;

    } elseif ($proceednew) {  // We're not using a saved payment method...
        
        $errors = xarSession::getVar('errors');
        foreach ($myfields as $field) {
            $isvalid = $paymentobject->properties[$field]->checkInput();
            
            ${$field} = $paymentobject->properties[$field]->getValue();
            $values[$field] = ${$field};
            $values['customer'] = xarUserGetVar('id');
            
            if (!$isvalid) {
                $errors[$field] = true;
            } else {    
                if ($field != 'card_num') {
                    $payment[$field] = ${$field};
                    $data[$field] = ${$field}; 
                }
                unset($errors[$field]); // In case we previously submitted invalid input in this field
            }

        } // end foreach

        if (isset($exp_date)) {
            $exp_month = substr($exp_date,0,2);
            $exp_year = substr($exp_date,2,4);
            $reverse_date = $exp_year . $exp_month;
            $minimum_date = date('ym',time());
            if ($minimum_date > $reverse_date) {
                $errors['exp_date'] = true;
            }
        } 

        if (isset($errors)) {
            xarSession::setVar('errors',$errors);
        }
        
        if (!empty($errors)) { 
            return xarTplModule('shop','user','paymentmethod', $data);
        } else {
            $paymentobject->setFieldValues($values,1);
            xarSession::setVar('paymentmethod',$paymentobject->createItem());
            xarResponse::redirect(xarModURL('shop','user','order')); 
            return true;
        }
        
    }
    return $data;

}

?>