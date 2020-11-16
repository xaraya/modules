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
//Formating
/*define('MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR',1);
define('MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR',1);
define('MODULE_PAYMENT_SAFERPAYGW_FONT',1);
define('MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR',1);
define('MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR',1);
define('MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR',1);
define('MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR',1);
define('MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR',1);
define('MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR',1);
*/
sys::import('modules.payments.class.basicpayment');

class SaferPay extends BasicPayment
{
    const MODULE_PAYMENT_SAFERPAYGW_ERROR_TITLE  = 'There has been an error processing your credit card';
    const MODULE_PAYMENT_SAFERPAYGW_ERROR_HTTP_FAILURE = 'Http Failure';
        
    public $title;
        
    public $description;
        
    public $gateway_url;
        
    public $live;
        
    public $error_flag;
        
    public $error_msg;
    public $payinit_url;
    public $curlInfo;
    public $http_code;
    public $authnet_values = array();
    

    public function __construct()
    {
        $this->live = false;

        if ($this->live) {
            $this->gateway_url = 'https://www.saferpay.com/hosting/CreatePayInit.asp';
        } else {
            $this->gateway_url = 'https://www.saferpay.com/hosting/CreatePayInit.asp';
        }
    }

    // class methods
    public function update_status(array $args=array())
    {
        $this->authnet_values = $this->getParams($args);
        
        $this->pre_confirmation_check();
        
        xarSession::setVar('SAFERPAY_FLAG', 'ACTIVE');
        
        return true;
    }
    
    public function getParams(array $args=array())
    {
        $object = DataObjectMaster::getObjectList(array('name' => 'payments_gateways_config'));
        $object->getProperties();

        
        $items = $object->getItems(array('where' => 'configuration_group_id eq 2'));

        $aryParams = array();
        
        foreach ($items as $key => $val) {
            switch ($val['configuration_key']) {
                case 'MODULE_PAYMENT_SAFERPAYGW_ACCOUNT_ID':
                    //$aryParams['ACCOUNTID'] = $val['configuration_value'];
                    $aryParams['ACCOUNTID'] = isset($args['accountid']) ? urlencode($args['accountid']) : urlencode($val['configuration_value']);
                    break;
                case 'MODULE_PAYMENT_SAFERPAYGW_CCCVC':
                    //$aryParams['CCCVC'] = $val['configuration_value'];
                    $aryParams['CCCVC'] = isset($args['cccvc']) ? urlencode($args['cccvc']) : urlencode($val['configuration_value']);
                    break;
                case 'MODULE_PAYMENT_SAFERPAYGW_DEFAULT_LANGUAGE':
                    //$aryParams['LANGID'] = $val['configuration_value'];
                    $aryParams['LANGID'] = isset($args['langid']) ? urlencode($args['langid']) : urlencode($val['configuration_value']);
                    break;
                case 'MODULE_PAYMENT_SAFERPAYGW_TEXT_TITLE':
                    //$aryParams['TITLE'] = isset($args['title']) ? urlencode($args['title']) : urlencode($val['configuration_value']);;
                    break;
                case 'MODULE_PAYMENT_SAFERPAYGW_TEXT_DESCRIPTION':
                    $aryParams['DESCRIPTION'] = isset($args['description']) ? urlencode($args['description']) : urlencode($val['configuration_value']);
                    break;
                case 'MODULE_PAYMENT_SAFERPAYGW_CCNAME':
                    //$aryParams['CCNAME'] = $val['configuration_value'];
                    $aryParams['CCNAME'] = isset($args['ccname']) ? urlencode($args['ccname']) : urlencode($val['configuration_value']);
                    // no break
                case 'MODULE_PAYMENT_SAFERPAYGW_ALLOWCOLLECT':
                    //$aryParams['ALLOWCOLLECT'] = $val['configuration_value'];
                    $aryParams['ALLOWCOLLECT'] = isset($args['allowcollect']) ? urlencode($args['allowcollect']) : urlencode($val['configuration_value']);
                    // no break
                case 'MODULE_PAYMENT_SAFERPAYGW_DELIVERY':
                    //$aryParams['DELIVERY'] = $val['configuration_value'];
                    $aryParams['DELIVERY'] = isset($args['delivery']) ? urlencode($args['delivery']) : urlencode($val['configuration_value']);

                    // no break
                default:
                    break;
            }
        }
        
        $fields = unserialize(xarSession::getVar('orderfields'));
        
        if (is_array($fields)) {
            $aryParams["CURRENCY"] = isset($args['currency']) ? $args['currency'] : $fields['currency'];
            //Psspl:Added the two more places in amount.
            //The amount to be reserved specified in minor currency unit.
            //E.g. EUR 1.35 must be passed as 135.
            $aryParams["AMOUNT"] = round($fields['net_amount']);
            $aryParams["AMOUNT"] .="00";
        }
        //Psspl: modified the code for allowEdit_payment.
        if (!xarVarFetch('allowEdit_Payment', 'int', $allowEdit_Payment, null, XARVAR_DONT_SET)) {
            return;
        }
        
//        $aryParams["SUCCESSLINK"] = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER['ORIG_PATH_INFO'] . "?module=payments%26func=phase3";
//        $aryParams["FAILLINK"] = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER['ORIG_PATH_INFO'] . "?module=payments%26func=amount%26MakeChanges=1%26allowEdit_Payment=$allowEdit_Payment";
//        $aryParams["BACKLINK"] = "http://" . $_SERVER["HTTP_HOST"] . $_ENV['ORIG_PATH_INFO'] . "?module=payments%26func=amount%26MakeChanges=1%26allowEdit_Payment=$allowEdit_Payment"; /* return URL if user cancelled */
        
//        $aryParams["SUCCESSLINK"] = xarModURL('payments','user','phase3');
        // Hardcoded for Icetodo. Return back to subscription.
        /*
        $return_url_property = DataPropertyMaster::getProperty(array('name' => 'array'));
        $return_url_property->initialization_associative_array = 1;
        $return_url_property->checkInput('return_url');
        $return_url = unserialize($return_url_property->value);
        */
        //if(!xarVarFetch('return_url', 'str:1:', $return_url,  "a:0:{}", XARVAR_DONT_SET)) {return;}
        $return_url = xarSession::getVar('return_url');
        try {
            $return_url = unserialize($return_url);
        } catch (Exception $e) {
            $return_url = array();
        }
        
        if ($return_url['success_return_link']) {
            $aryParams["SUCCESSLINK"] = $return_url['success_return_link'];
            $aryParams["SUCCESSLINK"] = str_replace('&amp;', '%26', $aryParams["SUCCESSLINK"]);
        } else {
            $aryParams["SUCCESSLINK"] = xarModURL('subscriptions', 'user', 'subscribe', array('phase' => 'subscribe_createcontract'));
            $aryParams["SUCCESSLINK"] = str_replace('&', '%26', $aryParams["SUCCESSLINK"]);
        }
        if ($return_url['cancel_return']) {
            $aryParams["BACKLINK"] = $return_url['cancel_return'];
            $aryParams["BACKLINK"] = str_replace('&amp;', '%26', $aryParams["BACKLINK"]);
        } else {
            $aryParams["BACKLINK"] = xarModURL('subscriptions', 'user', 'subscribe');
            $aryParams["BACKLINK"] = str_replace('&', '%26', $aryParams["BACKLINK"]);
        }
        $aryParams["FAILLINK"] = xarModURL('subscriptions', 'user', 'subscribe');
        $aryParams["FAILLINK"] = str_replace('&', '%26', $aryParams["FAILLINK"]);

        $aryParams["ORDERID"] = xarSession::getVar('AUTHID');
                
        /*
        if ( defined('MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR') && tep_not_null(MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR) ) {
            $strAttributes .= '&MENUCOLOR='.MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR;
        }
        if ( defined('MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR') && tep_not_null(MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR) ) {
            $strAttributes .= '&MENUFONTCOLOR='.MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR;
        }
        if ( defined('MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR') && tep_not_null(MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR) ) {
            $strAttributes .= '&BODYFONTCOLOR='.MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR;
        }
        if ( defined('MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR') && tep_not_null(MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR) ) {
            $strAttributes .= '&BODYCOLOR='.MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR;
        }
        if ( defined('MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR') && tep_not_null(MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR) ) {
            $strAttributes .= '&HEADFONTCOLOR='.MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR;
        }
        if ( defined('MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR') && tep_not_null(MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR) ) {
            $strAttributes .= '&HEADCOLOR='.MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR;
        }
        if ( defined('MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR') && tep_not_null(MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR) ) {
            $strAttributes .= '&HEADLINECOLOR='.MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR;
        }
        if ( defined('MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR') && tep_not_null(MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR) ) {
            $strAttributes .= '&LINKCOLOR='.MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR;
        }
        */

        return $aryParams;
    }
    
    public function getQueryParameter()
    {
        $strAttributes = 'ACCOUNTID=' . $this->authnet_values['ACCOUNTID'] .
                        '&LANGID=' . $this->authnet_values['LANGID'] .
                        '&AMOUNT=' . $this->authnet_values['AMOUNT'].
                        '&CURRENCY=' . $this->authnet_values['CURRENCY'] .
                        '&ALLOWCOLLECT=' .$this->authnet_values['ALLOWCOLLECT'].
                        '&ORDERID='. $this->authnet_values['ORDERID'].
                        '&DESCRIPTION=' . $this->authnet_values['DESCRIPTION'] .
                        '&SUCCESSLINK='.$this->authnet_values['SUCCESSLINK'].
                        '&DELIVERY='.$this->authnet_values['DELIVERY'].
                        '&CCCVC='. $this->authnet_values['CCCVC'].
                        '&CCNAME='. $this->authnet_values['CCNAME'].
                        '&FAILLINK='.$this->authnet_values['FAILLINK'].
                        '&BACKLINK='.$this->authnet_values['BACKLINK'];
        
        return $strAttributes;
    }
    
    public function pre_confirmation_check()
    {
        $strAttributes = $this->getQueryParameter();

        $url = $this->gateway_url.'?'.$strAttributes;

        //Set error messages to null.
        $this->error_msg = "";
        
        xarSession::setVar('error_message', "");
                    
        $payinit_url = $this->sendTransactionToGateway($url);

        if ($this->validateURL($payinit_url)) {
            return $this->process_url($payinit_url);
        } else {
            return false;
        }
    }
    
    public function validateURL($payinit_url)
    {
        $this->error_flag = 1;

        if ($payinit_url == null) {
            $this->error_flag = 0;
        } else {
            $check = substr($payinit_url, 0, 5);
            
            if ($check == "ERROR") {
                $this->error_flag = 0;
                $this->error_msg .= self::MODULE_PAYMENT_SAFERPAYGW_ERROR_TITLE;
                $this->error_msg .= "<br>".$payinit_url;
            }
        }
        if (!$this->error_flag) {
            $error_message=$this->get_error();
            xarSession::setVar('error_message', $error_message);
        }
        return $this->error_flag;
    }
    
    public function get_error()
    {
        $this->error .=  "<br /><table border='0.5' width='100%' bgcolor='#160'><tr><td width=100%'>";
        $this->error.=$this->error_msg."</td></tr></table>";
        return $this->error;
    }

    public function sendTransactionToGateway($sURL)
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($sURL);
        } else {
            throw new Exception('The cURL extension is not loaded');
        }

        curl_setopt($ch, CURLOPT_PORT, 443);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $sReturn = curl_exec($ch);

        $this->curlInfo = curl_getinfo($ch);

        $this->http_code=$this->curlInfo['http_code'];

        if ($this->curlInfo['http_code'] != 200) {
            $curl_failure_info = implode("<br>", $this->curlInfo);

            $this->error_msg = "<B>".self::MODULE_PAYMENT_SAFERPAYGW_ERROR_HTTP_FAILURE."</B>";
            
            //check for all curl information when failure.
            //$this->error_msg .= $curl_failure_info;
            return false;
        }
        curl_close($ch);
        return $sReturn;
    }

    public function process_url($transaction_url)
    {
        //send validated trasaction to saferpay.
        header("Location:$transaction_url");
    }

    public function process_button()
    {
        //the preperation for a payment here
        $process_button_string = '<script src="http://www.saferpay.com/OpenSaferpayScript.js"></script>';
        //end of the preperation for a payment here
        return $process_button_string;
    }

    public function after_process()
    {
        return false;
    }

    public function check()
    {
        return  true;
    }

    public function install()
    {
        return true;
    }
    
    public function displaystatus()
    {
        //Modified the code to get the response data from $DATA instead of $_SERVER as $_SERVER creates problem
        //for some PHP versions.
        if (!xarVarFetch('DATA', 'str:', $saferpaydata, null, XARVAR_DONT_SET)) {
            return;
        }

        $status = '<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">';
        $arr1 = array();

        $replace_arr = array('"', "/>", "<");
        $saferpaydata = str_replace($replace_arr, "", $saferpaydata);
        $valuedecode = rawurldecode($saferpaydata);
        $arr2 = explode(" ", $valuedecode);

        foreach ($arr2 as $k => $v) {
            $val  = explode('=', $v);

            $status_key = isset($val[0])?$val[0]:'null';
            
            $status_value = isset($val[1])?$val[1]:'null';
            
            $status_arr[$status_key] = $status_value;
        }
                
        foreach ($status_arr as $key => $value) {
            switch ($key) {
                
                case 'func':
                            break;
                case 'module':
                            break;
                case 'DATA':
                            break;
                case 'MSGTYPE':
                        $status  .=  "<tr><td class=\"v\">$key</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                        break;
                case 'KEYID':
                        $status  .=  "<tr><td class=\"v\">$key</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'ID':
                        $status  .=  "<tr><td class=\"v\">unique transaction identifier assigned by safer-pay.</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'IPCOUNTRY':
                        $status  .=  "<tr><td class=\"v\">IP COUNTRY</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'IP':
                        $status  .=  "<tr><td class=\"v\">$key</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'AMOUNT':
                        //convert the ammount back to the original format
                        //The amount to be reserved specified in minor currency unit.
                        //13500 EUR to 135.00 EUR
                        $value = substr($value, 0, (strlen($value)-2));
                        $value .= ".00";
                        $status  .=  "<tr><td class=\"v\">$key</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'CURRENCY':
                        $status  .=  "<tr><td class=\"v\">$key</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'PROVIDERID':
                        $status  .=  "<td class=\"v\">processor ID</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'PROVIDERNAME':
                        $status  .=  "<td class=\"v\">processor Name</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'Test':
                        $status  .=  "<tr><td class=\"v\">$key</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'Card':
                        $status  .=  "<tr><td class=\"v\">$key</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'ORDERID':
                        $status  .=  "<tr><td class=\"v\">ORDER ID</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'ACCOUNTID':
                        $status  .=  "<tr><td class=\"v\">Merchant account ID</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'ECI':
                        $status  .=  "<tr><td class=\"v\">Electronic Commerce Indicator</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'CAVV':
                        $status  .=  "<tr><td class=\"v\">Cardholder Authentication Verification Value</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'CCCOUNTRY':
                        $status  .=  "<tr><td class=\"v\">country code(2-letter ISO 3166 format)</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
                case 'SIGNATURE':
                        
                            break;
                default:
                        $status  .=  "<tr><td class=\"v\">$key</td>";
                        $status  .=  "<td class=\"v\">$value</td></tr>";
                            break;
            }
        }
        $status  .=  "</table>";
        
        return $status;
    }
}
