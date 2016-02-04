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
//Psspl:Added the code for Paypal standard gateway support.

sys::import('modules.payments.class.basicpayment');

define('MODULE_PAYMENT_PAYPAL_STANDARD_ID' , 'PAYPAL_STANDARD_CC_TRANSACTION_ID');
define('MODULE_PAYMENT_PAYPAL_STANDARD_DEBUG_EMAIL' , 'PAYPAL_STANDARD_CC_TRANSACTION_DEBUG_EMAIL');
define('MODULE_PAYMENT_PAYPAL_TRANSACTION_METHOD' , 'PAYPAL_STANDARD_CC_TRANSACTION_METHOD');
define('MODULE_PAYMENT_CC_TRANSACTION_GATEWAY_SERVER' , 'PAYPAL_STANDARD_CC_TRANSACTION_GATEWAY_SERVER');

define('UPLOAD' , '1');
define('ITEM_NAME', "product_name");
define('ORDER_ID', 'product_name');

class paypalstandard extends BasicPayment
{
    var $enabled, $gateway_url;
    var $authnet_values = array();

    // class constructor
    public function __construct()
    {
        $this->enabled = true;
    }

    function getParams(Array $args=array())
    {
        $object = DataObjectMaster::getObjectList(array('name' => 'payments_gateways_config'));
        $object->getProperties();
        $items = $object->getItems(array('where' => 'configuration_group_id eq 6'));
        $aryParams = array();
        
        foreach ($items as $key => $val)
        {
            switch ($val['configuration_key'])
            {
                case MODULE_PAYMENT_PAYPAL_STANDARD_ID:
                    $aryParams['business'] = isset($args['business']) ? urlencode($args['business']) : urlencode($val['configuration_value']);
                    break;
                case MODULE_PAYMENT_PAYPAL_STANDARD_DEBUG_EMAIL:
                    $aryParams['receiver_email'] = isset($args['receiver_email']) ? urlencode($args['receiver_email']) : urlencode($val['configuration_value']);
                    break;
                case MODULE_PAYMENT_PAYPAL_TRANSACTION_METHOD:
                    $aryParams['cmd'] = isset($args['cmd']) ? urlencode($args['cmd']) : urlencode($val['configuration_value']);
                    break;
                case MODULE_PAYMENT_CC_TRANSACTION_GATEWAY_SERVER:
                    if($val['configuration_value'] == 'sandbox') {
                        $this->gateway_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
                    }
                    if($val['configuration_value'] == 'live') {
                        $this->gateway_url = 'https://www.paypal.com/cgi-bin/webscr';
                    }
                    break;
                default:
                    break;
            }
        }
        
        $fields = unserialize(xarSession::getVar('orderfields'));
        if(is_array($fields))
        {
            //Psspl:Added the input curracy type.
            $aryParams["currency_code"] = $fields['currency'];
            $aryParams["amount"] = round($fields['amount']);
        }
        
        $aryParams['upload'] = UPLOAD;
        $aryParams['item_name'] = ITEM_NAME;
        $aryParams['order_id'] = ORDER_ID;
        $aryParams['invoice'] = xarSession::getVar('AUTHID');
        
        //Psspl: modified the code for allowEdit_payment.
        if(!xarVarFetch('allowEdit_Payment', 'int', $allowEdit_Payment,   null,    XARVAR_DONT_SET)) {return;}
        
        $aryParams['return'] = xarModURL('payments','user','phase3');
        $aryParams["return"] = str_replace('&amp;','%26',$aryParams["return"]);
         
        $aryParams['notify_url'] = xarModURL('payments','user','phase3');
        $aryParams["notify_url"] = str_replace('&amp;','%26',$aryParams["notify_url"]);
        
        $aryParams['cancel_return'] = xarModURL('payments','user','amount',array('MakeChanges' => 1, 'allowEdit_Payment' => $allowEdit_Payment));
        $aryParams["cancel_return"] = str_replace('&amp;','%26',$aryParams["cancel_return"]);

        return $aryParams;
    }

    function update_status(Array $args=array())
    {
        $this->authnet_values = $this->getParams($args);
        
        $status = false;
        
        if ($this->enabled == true) 
        {
            $status = $this->sendTransactionToGateway();
        }
        
        xarSession::setVar('PAYPAL_FLAG','ACTIVE');
        
        return true;
    }

    function sendTransactionToGateway()
    {
        $queryParameter=$this->getQueryParameter();
        header("Location:$this->gateway_url?$queryParameter");
        ?>
        <!--<form name='form1' action = "<?php //echo $this->gateway_url; ?>" method = 'post' target = '_self'>-->
        <?php //echo"$queryParameter";?>    
        <!--</form>
        <script>SubmitPaypal();</script>
        --><?php
        return true;
        ?>
        
        <?php
    }

    function getQueryParameter()
    {
        $queryParameter = "";
        foreach ($this->authnet_values as $key => $val)
        {
            //$queryParameter .= "<input type=\"hidden\" name = \"$key\" value = \"$val\"/>";
            $queryParameter .= $key."=".$val."&";
        }
        
        return $queryParameter;
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
  }
?>
<!--<script language = "javascript">
function SubmitPaypal()
{
    document.form1.submit();
}
</script>
-->