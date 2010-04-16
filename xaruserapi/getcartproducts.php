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
 *  Get the items currently in the cart
 */
function shop_userapi_getcartproducts($args) 
{

    sys::import('modules.dynamicdata.class.objects.master');

    $total = 0;

    if(!isset($_SESSION['shop'])) return;

    foreach ($_SESSION['shop'] as $pid => $val) {

        // if this post variable is set, we must need to update the quantity
        if (isset($_POST['qty'.$pid])) {

            unset($qty_new);

            if(!xarVarFetch('qty'.$pid, 'isset', $qty_new, NULL, XARVAR_DONT_SET)) {return;}

            $_SESSION['shop'][$pid]['qty'] = $qty_new;

        } 

        $products[$pid]['qty'] = $_SESSION['shop'][$pid]['qty'];

        $object = DataObjectMaster::getObject(array('name' => 'shop_products'));
        $some_id = $object->getItem(array('itemid' => $pid));
        $values = $object->getFieldValues();

        $products[$pid]['title'] = xarVarPrepForDisplay($values['title']);

        $price = $values['price'];

        if (substr($price, 0, 1) == '.') {
            $price = '0' . $price;
        }
            
        $products[$pid]['price'] = $price;
        $subtotal = $values['price'] * $products[$pid]['qty'];
        $subtotals[] = $subtotal;
        $products[$pid]['subtotal'] = number_format($subtotal, 2);
    }

    $total = array_sum($subtotals);
    $total = number_format($total, 2);

    if (substr($total, 0, 1) == '.') {
            $total = '0' . $total;
        }

    $productinfo['products'] = $products;
    $productinfo['total'] = $total;
        
    return $productinfo;
}

?>