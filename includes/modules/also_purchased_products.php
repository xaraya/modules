<?php
/* -----------------------------------------------------------------------------------------
   $Id: also_purchased_products.php,v 1.8 2003/12/30 09:02:31 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(also_purchased_products.php,v 1.21 2003/02/12); www.oscommerce.com
   (c) 2003  nextcommerce (also_purchased_products.php,v 1.9 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

//$module_smarty= new Smarty;
$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
  // include needed files
  require_once(DIR_FS_INC .  'xtc_get_short_description.inc.php');

  if (isset($_GET['products_id'])) {
    $orders_query = new xenQuery("select p.products_id, p.products_image from " . TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " . TABLE_ORDERS . " o, " . TABLE_PRODUCTS . " p where opa.products_id = '" . (int)$_GET['products_id'] . "' and opa.orders_id = opb.orders_id and opb.products_id != '" . (int)$_GET['products_id'] . "' and opb.products_id = p.products_id and opb.orders_id = o.orders_id and p.products_status = '1' group by p.products_id order by o.date_purchased desc limit " . MAX_DISPLAY_ALSO_PURCHASED);
    $num_products_ordered = $orders_query->getrows();
    if ($num_products_ordered >= MIN_DISPLAY_ALSO_PURCHASED) {

      $row = 0;
      $module_content = array();
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($orders = $q->output()) {
        $orders['products_name'] = xarModAPIFunc('commerce','user','get_products_name',array('id' =>$orders['products_id']));
        $orders['products_short_description'] = xtc_get_short_description($orders['products_id']);

    if ($_SESSION['customers_status']['customers_status_show_price']!='0') {
    $module_content[]=array(
                            'PRODUCTS_NAME' => $orders['products_name'],
                            'PRODUCTS_DESCRIPTION' => $orders['products_short_description'],
                            'PRODUCTS_PRICE' => xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$orders['products_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1)),
                            'PRODUCTS_LINK' => xarModURL('commerce','user','product_info', 'products_id=' . $orders['products_id']),
                            'PRODUCTS_IMAGE' => xarTplGetImage('product_images/thumbnail_images/' . $orders['products_image']),
                            'BUTTON_BUY_NOW'=>'<a href="' . xarModURL('commerce','user',(basename($PHP_SELF), xtc_get_all_get_params(array('action')) . 'action=buy_now&BUYproducts_id=' . $orders['products_id'], 'NONSSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_buy_now.gif'),'alt' => TEXT_BUY . $orders['products_name'] . TEXT_NOW)                            );
  } else {
    $module_content[]=array(
                            'PRODUCTS_NAME' => $orders['products_name'],
                            'PRODUCTS_DESCRIPTION' => $orders['products_short_description'],
                            'PRODUCTS_PRICE' => xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$orders['products_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1)),
                            'PRODUCTS_LINK' => xarModURL('commerce','user','product_info', 'products_id=' . $orders['products_id']),
                            'PRODUCTS_IMAGE' => xarTplGetImage('product_images/thumbnail_images/' . $orders['products_image']));

  }
    $row ++;
      }

  $module_smarty->assign('language', $_SESSION['language']);
  $module_smarty->assign('module_content',$module_content);
  // set cache ID
  if (USE_CACHE=='false') {
  $module_smarty->caching = 0;
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/also_purchased.html');
  } else {
  $module_smarty->caching = 1;
  $module_smarty->cache_lifetime=CACHE_LIFETIME;
  $module_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$_GET['products_id'].$_SESSION['customers_status']['customers_status_name'];
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/also_purchased.html',$cache_id);
  }
  $info_smarty->assign('MODULE_also_purchased',$module);

    }
  }
?>