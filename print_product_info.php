<?php
/* -----------------------------------------------------------------------------------------
   $Id: print_product_info.php,v 1.3 2003/12/20 08:42:28 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_info.php,v 1.94 2003/05/04); www.oscommerce.com
   (c) 2003  nextcommerce (print_product_info.php,v 1.16 2003/08/25); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  include( 'includes/application_top.php');

  // include needed functions

//  $smarty = new Smarty;

  $product_info_query = new xenQuery("select p.products_id, pd.products_name, pd.products_description, p.products_model, p.products_quantity, p.products_image, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
  $product_info = $q->output();
  $products_price = xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$product_info['products_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1));

  $products_attributes_query = new xenQuery("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$_GET['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
  $products_attributes = $q->output();
  if ($products_attributes['total'] > 0) {
    $products_options_name_query = new xenQuery("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$_GET['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . $_SESSION['languages_id'] . "' order by popt.products_options_name");
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($products_options_name = $q->output()) {
      $selected = 0;

      $products_options_query = new xenQuery("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix,pa.attributes_stock, pa.attributes_model from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . (int)$_GET['products_id'] . "' and pa.options_id = '" . $products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($products_options = $q->output()) {
        $module_content[] = array(
          'GROUP'=>$products_options_name['products_options_name'],
          'NAME'=>$products_options['products_options_values_name']);

        if ($products_options['options_values_price'] != '0') {

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
        $tax_rate=xtc_get_tax_rate($product_info['products_tax_class_id']);
        $products_options['options_values_price']=xarModAPIFunc('commerce','user','add_tax',array('price' =>$products_options['options_values_price'],'tax' =>xtc_get_tax_rate($product_info['products_tax_class_id'])));
        }
          $module_content[sizeof($module_content)-1]['NAME'] .= ' (' . $products_options['price_prefix'] . xtc_format_price($products_options['options_values_price'], $price_special=1, $calculate_currencies=true) .')';
        }
      }
    }
  }

  // assign language to template for caching
  $data['language'] = $_SESSION['language'];

  $data['PRODUCTS_NAME'] = $product_info['products_name'];
  $data['PRODUCTS_MODEL'] = $product_info['products_model'];
  $data['PRODUCTS_DESCRIPTION'] = $product_info['products_description'];
  $data['PRODUCTS_IMAGE'] = xarTplGetImage('product_images/thumbnail_images/' . $product_info['products_image']);
  $data['PRODUCTS_PRICE'] = $products_price;
  $data['module_content'] = $module_content;

  // set cache ID
  if (USE_CACHE=='false') {
  $smarty->caching = 0;
  } else {
  $smarty->caching = 1;
  $smarty->cache_lifetime=CACHE_LIFETIME;
  $smarty->cache_modified_check=CACHE_CHECK;
  }
  $cache_id = $_SESSION['language'] . '_' . $product_info['products_id'];


  $smarty->display(CURRENT_TEMPLATE . '/module/print_product_info.html', $cache_id);
?>