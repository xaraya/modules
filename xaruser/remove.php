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
 *  Remove an item from the cart
 */
function shop_user_remove($args) 
{

    if(!xarVarFetch('id', 'isset', $pid, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('returnurl', 'isset', $returnurl, NULL, XARVAR_DONT_SET)) {return;}

    $shop = xarSession::getVar('shop');
    unset($shop[$pid]); 
    xarSession::setVar('shop',$shop);

    // Return the template variables defined in this function

    xarResponse::redirect($returnurl);

    return true;

}

?>