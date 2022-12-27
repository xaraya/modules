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
function payments_user_phase2()
{
        //      if (!xarSec::confirmAuthKey()) return;
    if (!xarSecurity::check('SubmitPayments')) {
        return;
    }
    //Psspl:Set the error_message to null string.
    xarSession::setVar('error_message', "");

    // Check for demo mode
    $demousers = unserialize(xarModVars::get('payments', 'demousers'));
    if (xarModVars::get('payments', 'enable_demomode') && in_array(xarUser::getVar('uname'), $demousers)) {
        return xarTpl::module('payments', 'user', 'demomode2', ['returnurl' => '']);
    }

    //Psspl:Implemented the code for return url.
    //if(!xarVar::fetch('return_url', 'array', $data['return_url'],  NULL, xarVar::DONT_SET)) {return;}
    if (!xarVar::fetch('allowEdit_Payment', 'int', $data['allowEdit_Payment'], null, xarVar::DONT_SET)) {
        return;
    }

    //Psspl:Implemented the code for return url.
    $return_url_property = DataPropertyMaster::getProperty(['name' => 'array']);
    $return_url_property->initialization_associative_array = 1;
    $return_url_property->checkInput('return_url');
    $data['return_url'] = $return_url_property->value;

    //Psspl: modified the code for storing return url into session.
    xarSession::setVar('return_url', serialize($data['return_url']));
    // Get the order object
    $orderobjectname = xarModVars::get('payments', 'orderobject');
    $orderobject = DataObjectMaster::getObject(['name' => $orderobjectname]);
    // Check for an order object, suppress exception if not found
    $isvalidorder = $orderobject->checkInput([], 1);
    $orderProperties = $orderobject->getProperties();
    $data['order_object'] = $orderobject;
    $data['order_properties'] = $orderProperties;

    // Get the payment object
    $paymentobject = DataObjectMaster::getObject(['name' => 'payments_ccpayments']);

    //Psspl: Removed regex_rule which is not allow to validate valid Credit Card number.
    //TODO: Need to find out a way to apply regex_rule also to validate Credit Card number.
    //$object->properties['number']->validation_regex = $data['paymentmethod']['regex_rule'];
    $isvalidpayment = $paymentobject->checkInput([], 1);
    $data['payment_object'] = $paymentobject;
    $data['payment_properties'] = $paymentobject->getProperties();

    $data['authid'] = xarSec::genAuthKey();

    // If the input is not good repropose the previous page
    if (!$isvalidorder || !$isvalidpayment) {
        $module_id = xarSession::getVar('clientmodule');
        $process = xarModVars::get('payments', 'process', $module_id);
        switch ($process) {
            case 0:
            default:
                return xarTpl::module('payments', 'user', 'errors', ['layout' => 'no_process']);
            case 1:
                return xarTpl::module('payments', 'user', 'phase1', $data);
            case 2:
                return xarTpl::module('payments', 'user', 'phase1', $data);
            case 3:
                return xarTpl::module('payments', 'user', 'onestep', $data);
        }
    }

    // We have valid input, save it
    $paymentfields = $paymentobject->getFieldValues();
    xarSession::SetVar('paymentfields', serialize($paymentfields));
    $orderfields = $orderobject->getFieldValues();
    xarSession::SetVar('orderfields', serialize($orderfields));

    //Psspl:Assigned the cc_type to paymentmethod.
    $paymentmethod=$paymentfields['cc_type'];
    //Psspl:Added the method for creating the paymentmethod object.
    $object = DataObjectMaster::getObject(['name' => 'payments_paymentmethods']);
    $object->getItem(['itemid' => $paymentmethod]);
    $data['paymentmethod'] = $object->getFieldValues();

    return $data;
}
