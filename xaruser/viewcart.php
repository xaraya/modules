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
 * View the cart
 */
function shop_user_viewcart() {

    sys::import('modules.dynamicdata.class.objects.master');

	$products = array();
	$total = 0;

	// May want to display cust info with the cart...
	$cust = xarMod::APIFunc('shop','user','customerinfo');
	$data['cust'] = $cust;  

	if (!empty($_SESSION['shop'])) {

		foreach ($_SESSION['shop'] as $pid => $val) {

			// If this post variable is set, we must need to update the quantity
			if (isset($_POST['qty'.$pid])) {

				unset($qty_new); // Have to unset this since we're in a foreach

				if(!xarVarFetch('qty'.$pid, 'isset', $qty_new, NULL, XARVAR_DONT_SET)) {return;}

				if ($qty_new == 0) {
					unset($_SESSION['shop'][$pid]); 
				} else {
					$_SESSION['shop'][$pid]['qty'] = $qty_new;
				}

			} 

			if (isset($_SESSION['shop'][$pid])) { // If the quantity hasn't been set to zero...

				$products[$pid]['qty'] = str_replace(',','',$_SESSION['shop'][$pid]['qty']); 

				// Get the product info
				$object = DataObjectMaster::getObject(array('name' => 'shop_products'));
				$some_id = $object->getItem(array('itemid' => $pid));
				$values = $object->getFieldValues();

				$products[$pid]['title'] = xarVarPrepForDisplay($values['title']);
				$products[$pid]['price'] = $values['price'];
				$subtotal = $values['price'] * $products[$pid]['qty'];
				$subtotals[] = $subtotal;
				if (substr($subtotal, 0, 1) == '.') {
					$subtotal = '0' . $subtotal;
				}
				$products[$pid]['subtotal'] = number_format($subtotal, 2);
				
			}

		}

	$total = array_sum($subtotals);
			if (substr($total, 0, 1) == '.') {
				$total = '0' . $total;
			}
	$total = number_format($total, 2);

	$_SESSION['products'] = $products;
	$data['products'] = $products;
	$_SESSION['total'] = $total;
	$data['total'] = $total;

	}

return $data;

}

?>
