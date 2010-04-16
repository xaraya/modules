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
 *   
 */
function shop_adminapi_gateways() 
{

    $array = array(
        1 => 'Demo Mode (no payment gateway)', // don't remove this from the array
        2 => 'Authorize.net',
        3 => 'Paypal',
        4 => 'Your payment gateway',
        5 => 'Etcetera'
    );

    return $array;

}

?>