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
        xarController::redirect(xarModURL('shop','user','shippingaddress'));
        return true;
    }
    $shop = xarSession::getVar('shop');
    if (!xarUserIsLoggedIn() || empty($shop)) {
        xarController::redirect(xarModURL('shop','user','main'));
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
	$nameval = $shippingobject->properties['name']->value;
	$namearray = $shippingobject->properties['name']->getValueArray($nameval);
	$shippingvals['first'] = $namearray['first'];
	$shippingvals['last'] = $namearray['last'];
    $data['shippingvals'] = $shippingvals;

    // Get the saved payment methods, if any exist
    $mylist = DataObjectMaster::getObjectList(array('name' => 'shop_paymentmethods'));
    $filters = array(
                     'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
                    'where' => 'customer eq ' . xarUserGetVar('id'),
                    );
    $paymentmethods = $mylist->getItems($filters);
    $data['paymentmethods'] = $paymentmethods;

    $data['paymentobject'] = DataObjectMaster::getObject(array('name' => 'shop_paymentmethods')); 
    $data['paymentobject']->properties['name']->display_show_salutation = false;
    $data['paymentobject']->properties['name']->display_show_middlename = false;
	$data['streetrows'] = 2;
    $data['paymentobject']->properties['address']->display_rows = $data['streetrows'];
    $data['paymentobject']->properties['address']->display_show_country = false;
    $data['properties'] = $data['paymentobject']->getProperties();

    if ($remove) {
        if ($remove == xarSession::getVar('paymentmethod')) {
            xarSession::delVar('paymentmethod');
        }
        $data['paymentobject']->getItem(array('itemid' => $remove));
        $data['paymentobject']->deleteItem();
        xarController::redirect(xarModURL('shop','user','paymentmethod'));
        return true;
    }

    $selectedpaymentmethod = xarSession::getVar('paymentmethod');
    if(!empty($selectedpaymentmethod)) {
        $data['paymentmethod'] = $selectedpaymentmethod;
    }

    // If we're using a saved payment method...
    if ($proceedsaved) {
        
        xarSession::setVar('paymentmethod',$paymentmethod);
        xarController::redirect(xarModURL('shop','user','order')); 
        return true;

    } elseif ($proceednew) {  // We're not using a saved payment method...
        
        $isvalid = $data['paymentobject']->checkInput();

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
        
        if (!$isvalid) { 
            return xarTplModule('shop','user','paymentmethod', $data);
        } else {       
			$data['paymentobject']->properties['customer']->setValue(xarUserGetVar('id'));
			xarSession::setVar('paymentmethod',$data['paymentobject']->createItem());
            xarController::redirect(xarModURL('shop','user','order')); 
            return true;
        }
        
    }
    return $data;
}

?>