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

class GestPay extends BasicPayment
{
    public const MODULE_PAYMENT_GESTPAYGW_ERROR_TITLE  = 'There has been an error processing your credit card';
    public const MODULE_PAYMENT_GESTPAYGW_ERROR_HTTP_FAILURE = 'Http Failure';

    public $title;

    public $description;

    public $gateway_url;

    public $live;

    public $error_flag;

    public $error_msg;
    public $payinit_url;
    public $curlInfo;
    public $http_code;
    public $authnet_values = [];
    public $ric_filepath;
    public $OTP_arr;
    public $OTP_reorder;

    public function __construct()
    {
        $this->live = false;

        if ($this->live) {
            $this->gateway_url = 'https://testecomm.sella.it/gestpay/pagam.asp';
        } else {
            $this->gateway_url = 'https://testecomm.sella.it/gestpay/pagam.asp';
        }
    }

    // class methods
    public function update_status(array $args=[])
    {
        $this->authnet_values = $this->getParams($args);

        $this->pre_confirmation_check();
        xarSession::setVar('GESTPAY_FLAG', 'ACTIVE');

        return true;
    }

    public function getParams(array $args=[])
    {
        $object = DataObjectMaster::getObjectList(['name' => 'payments_gateways_config']);
        $object->getProperties();
        $items = $object->getItems(['where' => 'configuration_group_id eq 9']);
        $aryParams = [];

        foreach ($items as $key => $val) {
            switch ($val['configuration_key']) {
                case 'GESTPAY_SHOP_LOGIN':   $aryParams['SHOPLOGIN'] = isset($args['shoplogin']) ? urlencode($args['shoplogin']) : urlencode($val['configuration_value']);
                                              break;
                case 'GESTPAY_RIC_FILEPATH': $aryParams['RIC_FILEPATH'] = $args['ric_filepath'] ?? $val['configuration_value'];
                                              break;
                case 'GESTPAY_OTP_REORDER': $this->OTP_reorder = $val['configuration_value'];
                                              break;
                default:                      break;
            }
        }

        $fields = unserialize(xarSession::getVar('orderfields'));

        if (is_array($fields)) {
            $aryParams["CURRENCY"] = $args['currency'] ?? $fields['currency'];
            $aryParams["CURRENCY"] = $this->getCurrencyCode($aryParams["CURRENCY"]);
            $aryParams["AMOUNT"] = $fields['amount'];
        }

        $aryParams["ORDERID"] = xarSession::getVar('AUTHID');

        //Code to take the OTP from the .ric file
        $this->ric_filepath = sys::root()."/html/".$aryParams['RIC_FILEPATH'];  // Get the full file path.
        $this->ric_filepath = trim(str_replace("\\", "/", $this->ric_filepath));
        if (!file_exists($this->ric_filepath)) {
            $msg = xarML('File "#(1)" does not exists', $this->ric_filepath);   //If file doesn't exists then generate an exception.
            throw new Exception($msg);
        } else {
            $this->OTP_arr = file($this->ric_filepath); //Get the file contents into an array of OTPs
            $aryParams["OTP"] = trim(array_shift($this->OTP_arr)); // Take out first OTP
        }

        return $aryParams;
    }

    public function getCurrencyCode($currency)
    {
        switch ($currency) {
            case "EUR": return 242;   // Euro
            case "CHF": return null;  // Swiss France not defined in the GestPay
            case "USD": return 1;     // US Dollar
        }
    }

    public function get_b_params_string()
    {
        $b_params=[
                        "PAY1_SHOPTRANSACTIONID" => $this->authnet_values['ORDERID'],
                        "PAY1_UICCODE" => $this->authnet_values['CURRENCY'],
                        "PAY1_AMOUNT" => $this->authnet_values['AMOUNT'],
                        "PAY1_OTP" => $this->authnet_values['OTP'],
                        ];

        $b = $separator = "";

        while ([$key, $val] = each($b_params)) {
            $b .= $separator."$key=$val";
            $separator = "*P1*";
        }

        return $b;
    }

    public function get_language_code($langcode)
    {
        switch ($langcode) {
            case 'it': return 1;
            case 'en': return 2;
            case 'es': return 3;
            case 'fr': return 4;
            case 'de': return 5;
        }
    }

    public function getQueryParameter()
    {
        $strAttributes = 'a=' . $this->authnet_values['SHOPLOGIN'] . '&b=' . $this->get_b_params_string();

        return $strAttributes;
    }

    //Pre-confirmation check if transaction information is right before sending it to the payment server.
    public function pre_confirmation_check()
    {
        //Get Admin name and email for sending email to admin
        sys::import('xaraya.structures.query');
        $prefix = xarDB::getPrefix();
        $dbconn = xarDB::getConn();
        $adminid = xarModVars::get('roles', 'admin');
        $query = "SELECT name, email FROM " . $prefix . "_roles WHERE id = $adminid";
        $result = $dbconn->Execute($query);
        if ($result) {
            [$recipient_name, $recipient_email] = $result->fields;
        }

        //Set error messages to null.
        $this->error_msg = "";
        xarSession::setVar('error_message', "");
        $this->error_flag = 1;
        $ErrorCode = "0";
        $ErrorDescription = "";

        if (empty($this->authnet_values['SHOPLOGIN'])) {
            $ErrorCode = "546";
            $ErrorDescription = "Shop ID not valid";
            $this->error_flag = 0;
        }
        if (empty($this->authnet_values['CURRENCY'])) {
            $ErrorCode = "552";
            $ErrorDescription = "Currency not valid";
            $this->error_flag = 0;
        }
        if (empty($this->authnet_values['AMOUNT'])) {
            $ErrorCode = "553";
            $ErrorDescription = "Amount not valid";
            $this->error_flag = 0;
        }
        if (empty($this->authnet_values['ORDERID'])) {
            $ErrorCode = "551";
            $ErrorDescription = "Shop Transaction ID not valid";
            $this->error_flag = 0;
        }
        if (empty($this->authnet_values['OTP'])) {
            $ErrorCode = "101";
            $ErrorDescription = "Internal Error (101) processing the payment. Please contact the system administrator";
            $this->error_flag = 0;

            //Send OTP Exhausted email to admin
            try {
                $send_mail = xarMod::apiFunc('mailer', 'user', 'send', ['name'             => 'OTP Exhausted Email',
                                                                         'locale'           => xarModUserVars::get('roles', 'locale', xarUser::getVar('id')),
                                                                         'sendername'       => xarUser::getVar('name'),
                                                                         'senderaddress'    => xarUser::getVar('email'),
                                                                         'recipientname'    => $recipient_name,
                                                                         'recipientaddress' => $recipient_email, ]);
            } catch (exception $e) {
            }
        }

        if (!$this->error_flag) {
            $this->error_msg = self::MODULE_PAYMENT_GESTPAYGW_ERROR_TITLE;
            $this->error_msg .= "<br/>Error Code :  $ErrorCode";
            $this->error_msg .= "<br/>Error Description : $ErrorDescription";

            $error_message = $this->get_error();
            xarSession::setVar('error_message', $error_message);

            return $this->error_flag;
        }

        //If no error found then check for number of OTPs left in the .ric file.
        //If total number of OTPs left are less than or equal to re-order level of OTPs, then send email to admin to download more OTPs.
        $OTP_count = count($this->OTP_arr);
        if ($OTP_count <= $this->OTP_reorder) {
            //Send OTP Re-Order email to admin
            try {
                $send_mail = xarMod::apiFunc('mailer', 'user', 'send', ['name'             => 'OTP Re-order Email',
                                                                         'locale'           => xarModUserVars::get('roles', 'locale', xarUser::getVar('id')),
                                                                         'sendername'       => xarUser::getVar('name'),
                                                                         'senderaddress'    => xarUser::getVar('email'),
                                                                         'recipientname'    => $recipient_name,
                                                                         'recipientaddress' => $recipient_email,
                                                                         'data'             => ['OTP_count' => $OTP_count], ]);
            } catch (exception $e) {
            }
        }

        //Replace the contents of file after removing the OTP.
        file_put_contents($this->ric_filepath, $this->OTP_arr);

        $strAttributes = $this->getQueryParameter();

        $url = $this->gateway_url.'?'.$strAttributes;

        $this->process_url($url);

        return true;
    }

    public function get_error()
    {
        $this->error .= "<br /><table border='0.5' width='100%' bgcolor='#160'><tr><td width=100%'>";
        $this->error .= $this->error_msg."</td></tr></table>";

        return $this->error;
    }

    public function process_url($transaction_url)
    {
        //send the validated transaction to the GestPay site.
        header("Location:$transaction_url");
    }

    public function displayStatus()
    {
        $status_arr = [];
        $argv = parse_str($_SERVER['QUERY_STRING']);

        $status = '<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">';

        if (isset($a)) {
            $status  .=  "<tr><td class=\"v\">Shop Login</td>";
            $status  .=  "<td class=\"v\">$a</td></tr>";
        }

        $separator = "*P1*";
        $param_b = explode($separator, $b);

        foreach ($param_b as $trans_param) {
            $trans_param_arr[] = explode("=", $trans_param);
        }

        foreach ($trans_param_arr as $value) {
            switch ($value[0]) {

                case 'PAY1_TRANSACTIONRESULT': $status  .=  "<tr><td class=\"v\">Transaction Result</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_SHOPTRANSACTIONID': $status  .=  "<tr><td class=\"v\">Shop Transaction ID</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_BANKTRANSACTIONID': $status  .=  "<tr><td class=\"v\">Bank Transaction ID</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_UICCODE': $status  .=  "<tr><td class=\"v\">Currency Code</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_AMOUNT': $status  .=  "<tr><td class=\"v\">Amount</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_AUTHORIZATIONCODE': $status  .=  "<tr><td class=\"v\">Transaction Authorization Code</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_ERRORCODE': $status  .=  "<tr><td class=\"v\">Transaction Error Code</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_ERRORDESCRIPTION': $status  .=  "<tr><td class=\"v\">Transaction Error Description</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_CHNAME': $status  .=  "<tr><td class=\"v\">Buyer's Name</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_CHEMAIL': $status  .=  "<tr><td class=\"v\">buyer's Email</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_ALERTCODE': $status  .=  "<tr><td class=\"v\">Transaction Alert Code</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_ALERTDESCRIPTION': $status  .=  "<tr><td class=\"v\">Transaction Alert Description</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_CARDNUMBER': $status  .=  "<tr><td class=\"v\">Buyer's Card Number</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_EXPMONTH': $status  .=  "<tr><td class=\"v\">Buyer's Card Expiry Month</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_EXPYEAR': $status  .=  "<tr><td class=\"v\">Buyer's Card Expiry Year</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_COUNTRY': $status  .=  "<tr><td class=\"v\">Buyer's Card Issuing Bank Nationality</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_VBVRISP': $status  .=  "<tr><td class=\"v\">VBVRISP</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_VBV': $status  .=  "<tr><td class=\"v\">VBV</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_IDLANGUAGE': $status  .=  "<tr><td class=\"v\">Language ID</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;

                case 'PAY1_OTP': $status  .=  "<tr><td class=\"v\">One Time Password (OTP)</td>";
                                                $status  .=  "<td class=\"v\">".$value[1]."</td></tr>";
                                                break;
            }
        }
        $status  .=  "</table>";

        return $status;
    }

    public function displayfailurestatus()
    {
        $argv = parse_str($_SERVER['QUERY_STRING']);
        $separator = "*P1*";
        $param_b = explode($separator, $b);
        foreach ($param_b as $trans_param) {
            $trans_param_arr[] = explode("=", $trans_param);
        }
        foreach ($trans_param_arr as $value) {
            switch ($value[0]) {
                case 'PAY1_ERRORCODE': $ErrorCode = $value[1];
                                                break;
                case 'PAY1_ERRORDESCRIPTION': $ErrorDescription = $value[1];
                                                break;
            }
        }
        $this->error_msg = self::MODULE_PAYMENT_GESTPAYGW_ERROR_TITLE;
        $this->error_msg .= "<br/>Error Code :  $ErrorCode";
        $this->error_msg .= "<br/>Error Description : $ErrorDescription";
        $this->error .= "<br /><table border='0.5' width='100%' bgcolor='#160'><tr><td width=100%'>";
        $this->error .= $this->error_msg."</td></tr></table>";
        xarSession::setVar('error_message', $this->error_msg);

        return $this->error;
    }
}
