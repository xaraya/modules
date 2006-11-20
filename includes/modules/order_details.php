<?php
/* -----------------------------------------------------------------------------------------
   $Id: order_details.php,v 1.7 2003/12/30 09:02:31 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(order_details.php,v 1.8 2003/05/03); www.oscommerce.com
   (c) 2003  nextcommerce (order_details.php,v 1.16 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
//$module_smarty=new Smarty;
$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
  // include needed functions
  require_once(DIR_FS_INC . 'xtc_draw_separator.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_input_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_checkbox_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_selection_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_check_stock.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_products_stock.inc.php');
  require_once(DIR_FS_INC . 'xtc_remove_non_numeric.inc.php');


  echo '  <tr>' . "\n";

  $colspan = 3;

  if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
    $colspan++;
    echo '    <td align="center" class="smallText"><b>' . TABLE_HEADING_REMOVE . '</b></td>' . "\n";
  }

  echo '    <td align="center" class="tableHeading">' . TABLE_HEADING_QUANTITY . '</td>' . "\n";

  if ((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
    $colspan++;
    echo '    <td class="tableHeading">' . TABLE_HEADING_MODEL . '</td>' . "\n";
  }

  echo '    <td class="tableHeading">' . TABLE_HEADING_PRODUCTS . '</td>' . "\n";

  if (!strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
    $colspan++;
    echo '    <td align="center" class="tableHeading">' . TABLE_HEADING_TAX . '</td>' . "\n";
  }
  //  echo $customer_id . $customer_status_name . $customer_status_value['customers_status_discount'] . $customer_status_value['customers_status_ot_discount'];
  if ($customer_status_value['customers_status_discount'] != 0) {
    $colspan++;
    echo '<td align="right" class="tableHeading">' . TABLE_HEADING_DISCOUNT . '</td>';
  }
  echo '<td align="right" class="tableHeading">' . TABLE_HEADING_TOTAL . '</td>';
  echo '</tr>';
  echo '<tr>';
  echo '<td colspan="' . $colspan . '">' . xtc_draw_separator() . '</td>';
  echo '</tr>';

  for ($i=0, $n=sizeof($products); $i<$n; $i++) {
    echo '  <tr>' . "\n";

// Delete box only for shopping cart
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '    <td align="center" valign="top">' . xtc_draw_checkbox_field('cart_delete[]', $products[$i]['id']) . '</td>' . "\n";
    }

// Quantity box or information as an input box or text
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '    <td align="center" valign="top">' . xtc_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="4"') . xtc_draw_hidden_field('products_id[]', $products[$i]['id']) . '</td>' . "\n";
    } else {
      echo '    <td align="center" valign="top" class ="main">' . $products[$i]['quantity'] . '</td>' . "\n";
    }

// Model
    if ((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '    <td valign="top" class="main"><a href="' . xarModURL('commerce','user','product_info', 'products_id=' . $products[$i]['id']) . '">' . $products[$i]['model'] . '</a></td>' . "\n";
    }

// Product name, with or without link
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '    <td valign="top" class="main"><a href="' . xarModURL('commerce','user','product_info', 'products_id=' . $products[$i]['id']) . '"><b>' . $products[$i]['name'] . '</b></a>';
    } else {
      echo '    <td valign="top" class="main"><b>' . $products[$i]['name'] . '</b>';
    }

// Display marker if stock quantity insufficient
    if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
      if (STOCK_CHECK == 'true') {
        echo $stock_check = xtc_check_stock($products[$i]['id'], $products[$i]['quantity']);
        if ($stock_check) $any_out_of_stock = 1;
      }
    }

// Product options names
    $attributes_exist = ((isset($products[$i]['attributes'])) ? 1 : 0);

    if ($attributes_exist == 1) {
      reset($products[$i]['attributes']);
      while (list($option, $value) = each($products[$i]['attributes'])) {
        echo '<br><small><i> - ' . $products[$i][$option]['products_options_model'] .'  '.$products[$i][$option]['products_options_name'] . ' ' . $products[$i][$option]['products_options_values_name'] . '</i></small>';

// Display marker if attributes-stock quantity insufficient
    if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
      if (ATTRIBUTE_STOCK_CHECK == 'true') {


        echo $attribute_stock_check = xtc_check_stock_attributes($products[$i][$option]['attributes_stock'], $products[$i]['quantity']);
        if ($attribute_stock_check) $any_out_of_stock = 1;

      }
    }

      }
    }

    echo '</td>' . "\n";

// Tax (not in shopping cart, tax rate may be unknown)
    if (!strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '    <td align="center" valign="top" class="main">' . number_format($products[$i]['tax'], TAX_DECIMAL_PLACES) . '%</td>' . "\n";
    }

// Product price
// elari - changed CS V3.x
  if ($customer_status_value['customers_status_discount'] != 0) {
  $max_product_discount = min($products[$i]['discount_allowed'] , $customer_status_value['customers_status_discount']);
  echo $products[$i]['discount_allowed'] . $products[$i]['discount_allowed'] . $customer_status_value['customers_status_discount'];
  if ($max_product_discount > 0) {
    echo '    <td align="right" valign="top" class="main">-' . $max_product_discount . '%</td>';
  } else {
    echo '    <td align="right" valign="top" class="main">&nbsp</td>';
  }
  }
// elari End CS V3.x
  if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
    echo '    <td align="right" valign="top" class="main"><b>'.xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$products[$i]['id'],'price_special' =>$price_special=1,'quantity' =>$quantity=$products[$i]['quantity'])).'</b>' . "\n";
  } else {
    echo '    <td align="right" valign="top" class="main"><b>'.xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$products[$i]['id'],'price_special' =>$price_special=1,'quantity' =>$quantity=$products[$i]['quantity'])) . '</b>' . "\n";
  }

// Product options prices
    if ($attributes_exist == 1) {
      reset($products[$i]['attributes']);
      while (list($option, $value) = each($products[$i]['attributes'])) {
        if ($products[$i][$option]['options_values_price'] != 0) {
          if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {

            echo '<br><small><i>' . xtc_get_products_attribute_price($products[$i][$option]['options_values_price'], $tax_class=$products[$i]['tax_class_id'],$price_special=1,$quantity=$products[$i]['quantity'],$prefix= $products[$i][$option]['price_prefix']) . '</i></small>';
          } else {
            echo '<br><small><i>' . xtc_get_products_attribute_price($products[$i][$option]['options_values_price'], $tax_class=$products[$i]['tax_class_id'],$price_special=1,$quantity=$products[$i]['quantity'],$prefix= $products[$i][$option]['price_prefix']) . '</i></small>';
          }
        } else {
// Keep price aligned with corresponding option
          echo '<br><small><i>&#160;</i></small>';
        }
      }
    }

    echo '</td>' . "\n" .
         '  </tr>' . "\n";
  }


  $module_smarty->assign('language', $_SESSION['language']);
  //$module_smarty->assign('module_content',$module_content);

  $module_smarty->caching = 0;
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/order_details.html');

  $smarty->assign('MODULE_order_details',$module);

?>
<!-- order_details_eof -->