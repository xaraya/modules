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
  
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_LOGIN_ID', 'AUTHORIZENET_CC_AIM_LOGIN_ID');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRAN_KEY', 'AUTHORIZENET_CC_AIM_TRANSACTION_KEY');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_MD5_HASH', 'AUTHORIZENET_CC_AIM_MD5_HASH');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRANSACTION_SERVER', 'AUTHORIZENET_CC_AIM_TRANSACTION_SERVER');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRANSACTION_MODE', 'AUTHORIZENET_CC_AIM_TRANSACTION_MODE');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRANSACTION_METHOD', 'AUTHORIZENET_CC_AIM_TRANSACTION_METHOD');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ORDER_STATUS_ID', 'AUTHORIZENET_CC_AIM_ORDER_STATUS_ID');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_CURL', 'AUTHORIZENET_CC_AIM_CURL');
  //Psspl:Defined the different Error Messages.  
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_TITLE', 'There has been an error processing your credit card');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_GENERAL', 'Please try again and if problems persist, please try another payment method.');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_DECLINED', 'This credit card transaction has been declined. Please try again and if problems persist, please try another credit card or payment method.');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_INVALID_EXP_DATE', 'The credit card expiration date is invalid. Please check the card information and try again.');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_EXPIRED', 'The credit card has expired. Please try again with another card or payment method.');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_CVC', 'The credit card check number (CVC) is invalid. Please check the card information and try again.');
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_INVALID_AMOUNT', 'Please enter a valid amount.');  
  //Psspl:Added the Code for Http_failure.    
  define('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_HTTP_FAILURE', 'Http Failure');
  
  class authorizenet_cc_aim extends BasicPayment 
  {
    //Psspl:Added the Code initializing  Http_failure variable.       
    var $enabled, $live, $gateway_url, $cc_aim_curl,$http_code,$curlInfo,$respose_reson_text;
    var $authnet_values = array();

// class constructor
    public function __construct() 
    {
      $this->enabled = true;
      $this->live = false;
      //$this->authnet_values = $this->getParams();
    }

    function getParams(Array $args=array()) 
    {
        $object = DataObjectMaster::getObjectList(array('name' => 'payments_gateways_config'));
        $object->getProperties();
        $items = $object->getItems(array('where' => 'configuration_group_id eq 8'));
        $aryParams = array();
        foreach ($items as $key => $val) {
            switch ($val['configuration_key']) {
              case MODULE_PAYMENT_AUTHORIZENET_CC_AIM_LOGIN_ID:
                //$aryParams['x_login'] = $val['configuration_value'];
                $aryParams['x_login'] = isset($args['x_login']) ? urlencode($args['x_login']) : urlencode($val['configuration_value']);
                break;
              case MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRAN_KEY:
                //$aryParams['x_tran_key'] = $val['configuration_value'];
                $aryParams['x_tran_key'] = isset($args['x_tran_key']) ? urlencode($args['x_tran_key']) : urlencode($val['configuration_value']);
                break;
              case MODULE_PAYMENT_AUTHORIZENET_CC_AIM_MD5_HASH:
                break;
              case MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRANSACTION_SERVER:
            	//$this->live = ($val['configuration_value'] == 'Test')?false:true;            	
              	if(isset($args['live'])){
					$this->live = ($args['live'] == 'Test') ? false : true;	          			      		
                }else {
                	$this->live = ($val['configuration_value'] == 'Test') ? false : true;
                }
                if($this->live) {
          			$this->gateway_url = 'https://secure.authorize.net/gateway/transact.dll';
      			} else {
          			$this->gateway_url = 'https://test.authorize.net/gateway/transact.dll';
      			}
                break;
              case MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRANSACTION_MODE:
            	//$aryParams['x_test_request'] = ($val['configuration_value'] == 'Test')?'TRUE':'FALSE';
              	if(isset($args['x_test_request'])){
					$aryParams['x_test_request'] = ($args['x_test_request'] == 'Test') ? 'TRUE' : 'FALSE';	          			      		
                }else {
                	$aryParams['x_test_request'] = ($val['configuration_value'] == 'Test') ? 'TRUE' : 'FALSE';
                }                
                break;
              case MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRANSACTION_METHOD:
              	//$aryParams['x_type'] = (($val['configuration_value'] == 'Capture') ? 'AUTH_CAPTURE' : 'AUTH_ONLY');               
            	if(isset($args['x_type'])){
					$aryParams['x_type'] = (($args['x_type'] == 'Capture') ? 'AUTH_CAPTURE' : 'AUTH_ONLY');	          			      		
                }else {
                	$aryParams['x_type'] = (($val['configuration_value'] == 'Capture') ? 'AUTH_CAPTURE' : 'AUTH_ONLY');
                }
                break;
              case MODULE_PAYMENT_AUTHORIZENET_CC_AIM_CURL:
              	//$this->cc_aim_curl = $val['configuration_value'];  
              	$this->cc_aim_curl = isset($args['cc_aim_curl']) ? $args['cc_aim_curl'] : $val['configuration_value'];            	
                break;
              case MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ORDER_STATUS_ID:
              default:
                break;
            }
        }
        $aryParams["x_version"] = "3.1";
        $aryParams["x_delim_char"] = ",";
        $aryParams["x_delim_data"] = "TRUE";
        $aryParams["x_url"] = "FALSE";
        $aryParams["x_method"] = "CC";
        $aryParams["x_relay_response"] = "FALSE";
        $aryParams["x_trans_id"] = xarSession::getVar('AUTHID');
        
        $fields = unserialize(xarSession::GetVar('paymentfields'));
        if(is_array($fields)) {
            $aryParams["x_card_num"] = $fields['number'];
            $aryParams["x_exp_date"] = date("my", $fields['expiration_date']);
            $aryParams["x_card_code"] = $fields['control_number'];
            $aryParams["x_first_name"] = $fields['name'];
            //Psspl:Comment the code for resolving orderobject issue.
            //$aryParams["x_amount"] = isset($fields['amount'])?$fields['amount']:"0.1";
        }
        
        $fields = unserialize(xarSession::GetVar('orderfields'));
        if(is_array($fields)) {
              $aryParams["x_amount"] = isset($fields['amount'])?$fields['amount']:"0.1";
            //$aryParams["x_currency_code"] = $fields['currency'];
        }
        
        return $aryParams;
    }
    
// class methods
    function update_status(Array $args=array()) 
    {
        $status = false;
        $this->authnet_values = $this->getParams($args);
      if ($this->enabled == true) {
        $fields = "";
        foreach( $this->authnet_values as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";
        $status = $this->sendTransactionToGateway($this->gateway_url, $fields);
        //Psspl:Removed the Commeted code.
        //Psspl:Added the code for Error handling.
        if (!empty($status)) {
            $regs = preg_split("/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/", $status);

            foreach ($regs as $key => $value)
            {
                $regs[$key] =$value;
            }
        }
        else
        {
            $regs = array('-1', '-1', '-1');
        }
        $this->error = false;

        if ($regs[0] == '1') {
        } else {
            switch ($regs[2]) {
                case '-1':
                    $this->error = 'HTTP_FAILURE';
                    break;
                case '5':
                    $this->error = 'invalid_amount';
                    break;
				
                case '7':
                    $this->error = 'invalid_expiration_date';
                    break;

                case '8':
                    $this->error = 'expired';
                    break;

                case '6':
                case '17':
                case '28':
                    $this->error = 'declined';
                    break;
                    
                case '78':
                    $this->error = 'cvc';
                    break;
                   	
                	    
                default:
                    $this->error = 'general';
                    break;
            }
        }
       
        if ($this->error != false) {
			//Psspl:Modified code for resolving the issue of regs[3].
			//regs array not set when http failure error occurs.
        	$this->respose_reson_text = isset($regs[3])?$regs[3]:'';
             $error_message = $this->get_error();
             //$error_message .= "$regs[3]";
            xarSession::setVar('error_message' , $error_message);
        } else {

        }
        //TODO: Comment below statement on live production server.
        //Below statement will display the reponse got from Authorized.Net
          return $this->generateResponse($status);
      }
      return $result;
    }

    function sendTransactionToGateway($url, $parameters) 
    {
      $server = parse_url($url);

      if (isset($server['port']) === false) {
        $server['port'] = ($server['scheme'] == 'https') ? 443 : 80;
      }

      if (isset($server['path']) === false) {
        $server['path'] = '/';
      }

      if (isset($server['user']) && isset($server['pass'])) {
        $header[] = 'Authorization: Basic ' . base64_encode($server['user'] . ':' . $server['pass']);
      }

      if (function_exists('curl_init')) {
        $curl = curl_init($server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : ''));
        curl_setopt($curl, CURLOPT_PORT, $server['port']);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);

        $result = curl_exec($curl);
        $this->curlInfo = curl_getinfo($curl);
        curl_close($curl);
        $this->http_code=$this->curlInfo['http_code'];
        if($this->curlInfo['http_code'] != 200) {
            return false;
        }
      } else {
        exec(escapeshellarg($this->cc_aim_curl) . ' -d ' . escapeshellarg($parameters) . ' "' . $server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : '') . '" -P ' . $server['port'] . ' -k', $result);
        $result = implode("\n", $result);
      }

      return $result;
    }
    
    function generateResponse($resp)
    {
        $output = '<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">';
        $text = $resp;
        $h = substr_count($text, ",");
        $h++;
            for($j=1; $j <= $h; $j++){
            $p = strpos($text, ",");
            if ($p === false) { // note: three equal signs
                $output .= "<tr>";
                $output .= "<td class=\"e\">";
                    //  x_delim_char is obviously not found in the last go-around
                    if($j>=69){
                        $output .= "Merchant-defined (".$j."): ";
                        $output .= ": ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $text;
                        $output .= "<br>";
                    } else {
                        $output .= $j;
                        $output .= ": ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $text;
                        $output .= "<br>";
                    }
                $output .= "</td>";
                $output .= "</tr>";
            }else{
                $p++;
                //  We found the x_delim_char and accounted for it . . . now do something with it
                //  get one portion of the response at a time
                $pstr = substr($text, 0, $p);
        
                //  this prepares the text and returns one value of the submitted
                //  and processed name/value pairs at a time
                //  for AIM-specific interpretations of the responses
                //  please consult the AIM Guide and look up
                //  the section called Gateway Response API
                $pstr_trimmed = substr($pstr, 0, -1); // removes "," at the end
        
                if($pstr_trimmed==""){
                    $pstr_trimmed="NO VALUE RETURNED";
                }
                $output .= "<tr>";
                $output .= "<td class=\"e\">";
                switch($j){
                    case 1:
                        $output .= "Response Code: ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $fval="";
                        if($pstr_trimmed=="1"){
                            $fval="Approved";
                        }elseif($pstr_trimmed=="2"){
                            $fval="Declined";
                        }elseif($pstr_trimmed=="3"){
                            $fval="Error";
                        }
                        $output .= $fval;
                        $output .= "<br>";
                        break;
                    case 2:
                        $output .= "Response Subcode: ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 3:
                        $output .= "Response Reason Code: ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 4:
                        $output .= "Response Reason Text: ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 5:
                        $output .= "Approval Code: ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 6:
                        $output .= "AVS Result Code: ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 7:
                        $output .= "Transaction ID: ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 8:
                        $output .= "Invoice Number (x_invoice_num): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 9:
                        $output .= "Description (x_description): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 10:
                        $output .= "Amount (x_amount): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 11:
                        $output .= "Method (x_method): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 12:
                        $output .= "Transaction Type (x_type): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 13:
                        $output .= "Customer ID (x_cust_id): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 14:
                        $output .= "Cardholder First Name (x_first_name): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 15:
                        $output .= "Cardholder Last Name (x_last_name): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 16:
                        $output .= "Company (x_company): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 17:
                        $output .= "Billing Address (x_address): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 18:
                        $output .= "City (x_city): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 19:
                        $output .= "State (x_state): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 20:
                        $output .= "ZIP (x_zip): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 21:
                        $output .= "Country (x_country): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 22:
                        $output .= "Phone (x_phone): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 23:
                        $output .= "Fax (x_fax): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 24:
                        $output .= "E-Mail Address (x_email): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 25:
                        $output .= "Ship to First Name (x_ship_to_first_name): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 26:
                        $output .= "Ship to Last Name (x_ship_to_last_name): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 27:
                        $output .= "Ship to Company (x_ship_to_company): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 28:
                        $output .= "Ship to Address (x_ship_to_address): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 29:
                        $output .= "Ship to City (x_ship_to_city): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 30:
                        $output .= "Ship to State (x_ship_to_state): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 31:
                        $output .= "Ship to ZIP (x_ship_to_zip): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 32:
                        $output .= "Ship to Country (x_ship_to_country): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 33:
                        $output .= "Tax Amount (x_tax): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 34:
                        $output .= "Duty Amount (x_duty): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 35:
                        $output .= "Freight Amount (x_freight): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 36:
                        $output .= "Tax Exempt Flag (x_tax_exempt): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 37:
                        $output .= "PO Number (x_po_num): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 38:
                        $output .= "MD5 Hash: ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    case 39:
                        $output .= "Card Code Response: ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $fval="";
                        if($pstr_trimmed=="M"){
                            $fval="M = Match";
                        }elseif($pstr_trimmed=="N"){
                            $fval="N = No Match";
                        }elseif($pstr_trimmed=="P"){
                            $fval="P = Not Processed";
                        }elseif($pstr_trimmed=="S"){
                            $fval="S = Should have been present";
                        }elseif($pstr_trimmed=="U"){
                            $fval="U = Issuer unable to process request";
                        }else{
                            $fval="NO VALUE RETURNED";
                        }
                        $output .= $fval;
                        $output .= "<br>";
                        break;
                    case 40:
                    case 41:
                    case 42:
                    case 43:
                    case 44:
                    case 45:
                    case 46:
                    case 47:
                    case 48:
                    case 49:
                    case 50:
                    case 51:
                    case 52:
                    case 53:
                    case 54:
                    case 55:
                    case 55:
                    case 56:
                    case 57:
                    case 58:
                    case 59:
                    case 60:
                    case 61:
                    case 62:
                    case 63:
                    case 64:
                    case 65:
                    case 66:
                    case 67:
                    case 68:
                        $output .= "Reserved (".$j."): ";
                        $output .= "</td>";
                        $output .= "<td class=\"v\">";
                        $output .= $pstr_trimmed;
                        $output .= "<br>";
                        break;
                    default:
                        if($j>=69){
                            $output .= "Merchant-defined (".$j."): ";
                            $output .= ": ";
                            $output .= "</td>";
                            $output .= "<td class=\"v\">";
                            $output .= $pstr_trimmed;
                            $output .= "<br>";
                        } else {
                            $output .= $j;
                            $output .= ": ";
                            $output .= "</td>";
                            $output .= "<td class=\"v\">";
                            $output .= $pstr_trimmed;
                            $output .= "<br>";
                        }
                        break;
                }
                $output .= "</td>";
                $output .= "</tr>";
                // remove the part that we identified and work with the rest of the string
                $text = substr($text, $p);
            }
        }
        return $output .= "</table>";
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
        //Psspl:Implemented the code for error handling.
        $error_message = MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_GENERAL;
        
        switch ($this->error) {
            //Psspl:Added the Code for Http_failure menu.     
            case 'HTTP_FAILURE':
                $error_message = MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_HTTP_FAILURE ." ,Http Code:$this->http_code";
                $dump=var_export($this->curlInfo,true);
                $error_message .="<br>Curl info message=$dump";
                break;
            
            case 'invalid_amount':
                $error_message = MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_INVALID_AMOUNT;
                break;

            case 'invalid_expiration_date':
                $error_message = MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_INVALID_EXP_DATE;
                break;

            case 'expired':
                $error_message = MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_EXPIRED;
                break;

            case 'declined':
                $error_message = MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_DECLINED;
                break;

            case 'cvc':
                $error_message = MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_CVC;
                break;

            default:
                $error_message = MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_GENERAL;
                $error_message .= "<br>$this->respose_reson_text";
                //$dump=var_export($this->curlInfo,true);
                //$error_message .="<br>Curl info message=$dump";
                break;
        }
        //Psspl:Gives the curl_info status.
        //$dump=var_export($this->curlInfo,true);
        //$error_message .="<br>Curl info message=$dump";
        
        $this->error ="<B>".MODULE_PAYMENT_AUTHORIZENET_CC_AIM_ERROR_TITLE;
        $this->error.="</B><br /><table border='0.5' width='100%' bgcolor='#160'><tr><td width=100%'>";
        $this->error.=$error_message."</td></tr></table>";

        return $this->error;
        //return false;
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
