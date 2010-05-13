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
 *  Complete the order.  If all goes well, we'll submit the transaction to the payment gateway, save our own transaction record, and update customer info
 */
function shop_user_complete() 
{

    // Redirects at the start of the user functions are just a way to make sure someone isn't where they don't need to be
    if (!xarUserIsLoggedIn()) {
        xarController::redirect(xarModURL('shop','user','viewcart'));
        return true;
    }
    $order = xarSession::getVar('order');
    if (empty($order)) {  //Probably a page reload... no reason to be here anymore
        xarController::redirect(xarModURL('shop','user','main'));
        return true;
    }

    $data['order'] = $order['products'];
    $data['ordertid'] = $order['tid'];
    $data['orderdate'] = $order['date'];
    $data['total'] = xarSession::getVar('total');
    xarSession::delVar('order');  // For privacy, order will not be redisplayed if the page is visited later
    xarSession::delVar('total');
    return $data;

}

?>