<?php
/**
 * the main IPN Function
 *
 * @author  John Cox <niceguyeddie@xaraya.com>
 * @access  public
 * @param   no parameters
 * @return  true on success or void on falure
 * @throws  XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
*/
function paypalipn_user_main()
{

    // Add to the string with cmd var
    $req = 'cmd=_notify-validate';

    // Post Vars must exactly match.  It would be nice to use the varBatch for this as well
    // For now just using the $_Post global.  TODO clean this up.
    foreach ($_POST as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
    }
    
    // Config Option to test the post vars
    if (!xarModGetVar('paypalipn', 'testmode')){
        $domain = 'www.paypal.com';
    } else {
        $domain = 'www.eliteweaver.co.uk';
    }

    // post back to PayPal system to validate
    $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "User-Agent: PHP/".phpversion()."\r\n";
	$header .= "Referer: " . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "\r\n";
    $header .= "Host: " . $domain . ":80\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    $fp = fsockopen ($domain, 80, $errno, $errstr, 30);
    
    if (!$fp) return;

    // assign posted variables to local variables
    $data['item_name']          = $_POST['item_name'];
    $data['item_number']        = $_POST['item_number'];
    $data['payment_status']     = $_POST['payment_status'];
    $data['payment_amount']     = $_POST['mc_gross'];
    $data['payment_currency']   = $_POST['mc_currency'];
    $data['txn_id']             = $_POST['txn_id'];
    $data['receiver_email']     = $_POST['receiver_email'];
    $data['payer_email']        = $_POST['payer_email'];
    // We also want a copy of all the vars serialized for later use
    $data['var_dump']           = serialize($_POST);

    fputs ($fp, $header . $req);
    while (!feof($fp)) {
        $res = fgets ($fp, 1024);
        if (strcmp ($res, "VERIFIED") == 0) {
            // No processing of the payment is needed here.
            // All we want to do is log and mail (if wanted) the item transaction

            if (xarModGetVar('paypalipn', 'email')){
                $mail['var_dump_formatted'] = var_export($_POST, true);
                $email                      = xarModGetVar('mail', 'adminmail');
                $name                       = xarModGetVar('mail', 'adminname');
                $subject                    = xarML('Paypal IPN Successful');
                $message = xarML('A successful Paypal IPN event has been recognized, below is a transaction of the event');
                $message .= "\n\n";
                $message .= $mail['var_dump_formatted'];

                if (!xarModAPIFunc('mail',
                        'admin',
                        'sendmail',
                        array('info' => $email,
                            'name' => $name,
                            'subject' => $subject,
                            'message' => $message))) return;
            }

            // Let's log the entire event as well.
            if (!xarModAPIFunc('paypalipn',
                    'admin',
                    'create',
                    array('args' => $data))) return;

        }   else if (strcmp ($res, "INVALID") == 0) {
            // log for manual investigation
            // let's just save the last transaction for now
            // if needed we can save more I suppose
            xarModSetVar('paypalipn', 'lastbadtransaction', $data['var_dump']);

            // And let's see if we want the email about the event.
            if (xarModGetVar('paypalipn', 'email')){
                $data['var_dump_formatted'] = var_export($_POST, true);
                $email                      = xarModGetVar('mail', 'adminmail');
                $name                       = xarModGetVar('mail', 'adminname');
                $subject                    = xarML('Paypal IPN Failed');
                $message = xarML('A failed Paypal IPN event has been recognized, below is a transaction of the event');
                $message .= "\n\n";
                $message .= $data['var_dump_formatted'];

                if (!xarModAPIFunc('mail',
                        'admin',
                        'sendmail',
                        array('info' => $email,
                            'name' => $name,
                            'subject' => $subject,
                            'message' => $message))) return;
            }
        }
    }
    fclose ($fp);
    return true;
} 
?>