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
  sys::import('modules.payments.class.basicpayment');

  define('MODULE_PAYMENT_IPAYMENT_CC_ACCOUNT_NUMBER', 'IPAYMENT_CC_ACCOUNT_NUMBER');  
  define('MODULE_PAYMENT_IPAYMENT_CC_USER_ID', 'IPAYMENT_CC_USER_ID');
  define('MODULE_PAYMENT_IPAYMENT_CC_USER_PASSWORD', 'IPAYMENT_CC_USER_PASSWORD');
  define('MODULE_PAYMENT_IPAYMENT_CC_TRANSACTION_METHOD', 'IPAYMENT_CC_TRANSACTION_METHOD');
  define('MODULE_PAYMENT_IPAYMENT_CC_TRANSACTION_SERVER', 'IPAYMENT_CC_TRANSACTION_SERVER');
  //Psspl:Define the Error_heading and Error_message.
  define('MODULE_PAYMENT_IPAYMENT_CC_ERROR_HEADING', 'There has been an error processing your credit card');
  define('MODULE_PAYMENT_IPAYMENT_CC_ERROR_MESSAGE', 'Please check your credit card details!');
  
  class iPayment_cc extends BasicPayment 
  {
    var $enabled, $gateway_url;
    var $authnet_values = array();

// class constructor
    public function __construct() 
    {
      $this->enabled = true;
      //$this->authnet_values = $this->getParams();          
	  $this->gateway_url = 'https://ipayment.de/merchant/99999/processor.php';
    }

    function getParams(Array $args=array()) 
    {
        $object = DataObjectMaster::getObjectList(array('name' => 'payments_gateways_config'));
        $object->getProperties();
        $items = $object->getItems(array('where' => 'configuration_group_id eq 7'));
        $aryParams = array();
        foreach ($items as $key => $val) {
            switch ($val['configuration_key']) {
	          case MODULE_PAYMENT_IPAYMENT_CC_ACCOUNT_NUMBER:
			    //$this->gateway_url = 'https://ipayment.de/merchant/' . $val['configuration_value'] . '/processor.php';
			    $accountId = isset($args['accountid']) ? $args['accountid'] : $val['configuration_value'];
			    $this->gateway_url = 'https://ipayment.de/merchant/' . $accountId . '/processor.php';
            	break;
	          case MODULE_PAYMENT_IPAYMENT_CC_USER_ID:
	            //$aryParams['trxuser_id'] = $val['configuration_value'];
	            $aryParams['trxuser_id'] = isset($args['trxuser_id']) ? urlencode($args['trxuser_id']) : urlencode($val['configuration_value']);
	            break;
	          case MODULE_PAYMENT_IPAYMENT_CC_USER_PASSWORD:
	          	//$aryParams['trxpassword'] = $val['configuration_value'];
	          	$aryParams['trxpassword'] = isset($args['trxpassword']) ? urlencode($args['trxpassword']) : urlencode($val['configuration_value']);
	          	break;
	          case MODULE_PAYMENT_IPAYMENT_CC_TRANSACTION_METHOD:
	          	//$aryParams['trx_typ'] = $val['configuration_value'];
	          	$aryParams['trx_typ'] = isset($args['trx_typ']) ? urlencode($args['trx_typ']) : urlencode($val['configuration_value']);
	          	break;
			  case MODULE_PAYMENT_IPAYMENT_CC_TRANSACTION_SERVER:
            	//$this->enabled = (strtolower($val['configuration_value']) == "true")?true:false;
			  	if(isset($args['enabled'])){
					$this->enabled = (strtolower($args['enabled']) == 'true') ? true : false;	          			      		
                }else {
                	$this->enabled = (strtolower($val['configuration_value']) == "true") ? true : false;
                }	          	
	          	break;
	          default:
	            break;
            }
        }
        $aryParams["silent"] = "1";
        //Psspl:Removed the hard coded currancy type.
        //$aryParams["trx_currency"] = "USD";
        $aryParams["trx_paymenttyp"] = "cc";				
        
        $fields = unserialize(xarSession::GetVar('paymentfields'));
        if(is_array($fields)) {
        	//Psspl:Added the input curracy type.
        	//$aryParams["trx_currency"] = $fields['currancy'];
            $aryParams["cc_number"] = $fields['number'];
            $aryParams["cc_expdate_month"] = date("m", $fields['expiration_date']);
            $aryParams["cc_expdate_year"] = date("y", $fields['expiration_date']);			
            $aryParams["cc_checkcode"] = $fields['control_number'];
            $aryParams["addr_name"] = $fields['name'];
            //$aryParams["trx_amount"] = round($fields['amount']);
        }
        
        //Psspl : added the code for getting amount and currency information.
        $fields = unserialize(xarSession::GetVar('orderfields'));
        if(is_array($fields)) {
              $aryParams["trx_amount"] = round($fields['amount']);
              $aryParams["trx_currency"] = $fields['currency'];
        }

		//Psspl: modified the code for allowEdit_payment.		
		if(!xarVar::fetch('allowEdit_Payment', 'int', $allowEdit_Payment,   null,    xarVar::DONT_SET)) {return;}
		
		$redirect_url = xarController::URL('payments','user','phase4');
        //$aryParams["redirect_url"] = "http://" . $_ENV['HTTP_HOST'] . $_ENV['ORIG_PATH_INFO'] . "?module=payments&func=phase4&allowEdit_Payment=$allowEdit_Payment";
        $aryParams["redirect_url"] = xarController::URL('payments','user','phase4',array('allowEdit_Payment' => $allowEdit_Payment));
        $aryParams["redirect_url"] = str_replace('&','%26',$aryParams["redirect_url"]);
        
        //Psspl:Added the Silent error URL.
        //$aryParams["silent_error_url"] = "http://" . $_ENV['HTTP_HOST'] . $_ENV['ORIG_PATH_INFO'] . "?module=payments&func=phase4&allowEdit_Payment=$allowEdit_Payment";		        
        $aryParams["silent_error_url"] = xarController::URL('payments','user','phase4',array('allowEdit_Payment' => $allowEdit_Payment));
        $aryParams["redirect_url"] = str_replace('&','%26',$aryParams["redirect_url"]);

        return $aryParams;
    }
    
// class methods
    function update_status(Array $args=array()) 
    {
      $status = false;
      $this->authnet_values = $this->getParams($args);
      if ($this->enabled == true) {
        $status = $this->sendTransactionToGateway($this->gateway_url);
      }
      return $result;
    }

    function sendTransactionToGateway($url) 
    {
		header( "location:" . $url . "?" .
			"silent=1&" .
			"trx_paymenttyp=cc&" .
			"trxuser_id=" . $this->authnet_values['trxuser_id'] . "&" .
			"trxpassword=" . $this->authnet_values['trxpassword'] . "&" .
			"trx_currency=" . $this->authnet_values['trx_currency'] . "&" .	
			"trx_amount=" . $this->authnet_values['trx_amount'] . "&" .	
			"trx_typ=" . $this->authnet_values['trx_typ'] . "&" .
			"addr_name=" . urlencode($this->authnet_values['addr_name']) . "&" .	
			"cc_number=" . $this->authnet_values['cc_number'] . "&" .
			"cc_expdate_month=" . $this->authnet_values['cc_expdate_month'] . "&" .
			"cc_expdate_year=" . $this->authnet_values['cc_expdate_year'] . "&" .
			"cc_checkcode=" . $this->authnet_values['cc_checkcode'] . "&" .
			"redirect_url=" . urlencode($this->authnet_values['redirect_url']) );
    }
    
    function javascript_validation() 
        {
            return false;
        }

    function selection() 
    {
        return false;
    }

    function pre_confirmation_check() 
    {
        return false;
    }

    function confirmation() 
    {
        return false;
    }

    function process_button() 
    {
        return false;
    }

    function before_process() 
    {
        return false;
    }

    function after_process() 
    {
        return false;
    }

    function output_error() 
    {
      return false;
    }

    function get_error() 
    {
        return false;
    }

    function check() 
    {
        return false;
    }

    function install() 
    {
        return false;
    }

    function remove() 
    {
        return false;
    }

    function keys() 
    {
        return false;
    }
  }?>
