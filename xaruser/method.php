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
    function payments_user_method()
    {
        if (!xarSecurity::check('SubmitPayments')) return;

        // Check for gateway
        $module_id = xarSession::getVar('clientmodule');
        $gateway = xarModVars::get('payments', 'gateway',$module_id);
        if (empty($gateway)) {
            return xarTpl::module('payments','user','errors',array('layout' => 'no_gateway'));
        }

        // Check for the anonymous user
        $allowanonpay = xarModVars::get('payments', 'allowanonpay',$module_id);
        if (!xarUser::isLoggedIn() && !$allowanonpay) {
            xarController::redirect(xarController::URL('roles','user','showloginform'));
            return true;
        }

        //Psspl:Check the Paymentmethod previousaly selected or not
        if (!xarVar::fetch('paymentmethod',      'int:0:', $paymentmethod,   null,    xarVar::DONT_SET)) {return;}
        //Psspl:make variable for paymetmethod and assign to null.
        $data['paymentmethod']="";
        $data['MakeChanges']="";
        if($paymentmethod)
        {
            //store previosaly selcted value to variable
            $data['paymentmethod']=$paymentmethod;
            if (!xarVar::fetch('paymentmethod',      'int:0:', $paymentmethod,   null,    xarVar::DONT_SET)) {return;}
            //Psspl: Get value of submit button pressed on previous page.
            if (!xarVar::fetch('MakeChanges',      'str', $MakeChanges,  "",    xarVar::NOT_REQUIRED)) {return;}
            $data['MakeChanges']=$MakeChanges;
        }
        $data['authid'] = xarSec::genAuthKey();

// if there is nothing in the customers cart, redirect them to the shopping cart page

// if no shipping method has been selected, redirect the customer to the shipping method selection page

// avoid hack attempts during the checkout procedure by checking the internal cartID

// Stock Check

// if no billing destination address was selected, use the customers own address as default

// verify the selected billing address
        $object = DataObjectMaster::getObjectList(array('name' => 'payments_paymentmethods'));
        $data['properties'] = $object->getProperties();
        $data['items'] = $object->getItems(array('where' => 'state eq 3'));

        $data['authid'] = xarSec::genAuthKey();
        //Psspl: Added dummy data in order object to show on confirmation page.
        //Remove this code when it goes to production.
        $orderobjectname = xarModVars::get('payments','orderobject');
        if(!empty($orderobjectid)) {
            $orderobject = DataObjectMaster::getObject(array('name' => $orderobjectname));
            $orderobject->getItem(array('itemid' => 1));
            $orderobject->properties['id']->value = $data['authid'];
            $orderobject->properties['amount']->value = '10.0';
            $itemid = $orderobject->createItem();
            xarSession::setVar('itemid',$itemid);
            if (empty($itemid)) return;
        }
        return $data;
    }

?>
