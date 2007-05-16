<?php
/* -----------------------------------------------------------------------------------------
   $Id: new_products.php,v 1.15 2003/12/30 09:02:31 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(new_products.php,v 1.33 2003/02/12); www.oscommerce.com
   (c) 2003  nextcommerce (new_products.php,v 1.9 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Enable_Disable_Categories 1.3            Autor: Mikel Williams | mikel@ladykatcostumes.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
//$module_smarty= new Smarty;
$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
  require_once(DIR_FS_INC . 'xtc_get_short_description.inc.php');

  if ( (!isset($new_products_category_id)) || ($new_products_category_id == '0') ) {
    $new_products_query = new xenQuery("select distinct p.products_id,
                                         p.products_image,
                                         p.products_tax_class_id,
                                         IF(s.status, s.specials_new_products_price, p.products_price) as products_price
                                         from " . TABLE_PRODUCTS . " p
                                         left join " . TABLE_SPECIALS . " s
                                         on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c,
                                          " . TABLE_CATEGORIES . " c
                                          where c.categories_status='1' and
                                          p.products_id = p2c.products_id and
                                          p2c.categories_id = '0' and
                                          products_status = '1'
                                          order by p.products_date_added
                                          DESC limit " . MAX_DISPLAY_NEW_PRODUCTS);
  } else {
    $new_products_query = new xenQuery("select distinct
                                        p.products_id,
                                        p.products_image,
                                        p.products_tax_class_id,
                                        IF(s.status, s.specials_new_products_price, p.products_price) as
                                        products_price from " . TABLE_PRODUCTS . " p left join
                                        " . TABLE_SPECIALS . " s on p.products_id = s.products_id,
                                        " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c
                                        where c.categories_status='1'
                                        and p.products_id = p2c.products_id
                                        and p2c.categories_id = c.categories_id
                                        and c.parent_id = '" . $new_products_category_id . "'
                                        and p.products_status = '1'
                                        order by p.products_date_added DESC limit " . MAX_DISPLAY_NEW_PRODUCTS);
  }
  $row = 0;
  $module_content = array();
      $q = new xenQuery();
      if(!$q->run()) return;
  while ($new_products = $q->output()) {
    $new_products['products_name'] = xarModAPIFunc('commerce','user','get_products_name',array('id' =>$new_products['products_id']));
    $new_products['products_short_description'] = xtc_get_short_description($new_products['products_id']);
    if ($_SESSION['customers_status']['customers_status_show_price']!='0') {
    $module_content[]=array(
                            'PRODUCTS_NAME' => $new_products['products_name'],
                            'PRODUCTS_DESCRIPTION' => $new_products['products_short_description'],
                            'PRODUCTS_PRICE' => xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$new_products['products_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1)),
                            'PRODUCTS_LINK' => xarModURL('commerce','user','product_info', 'products_id=' . $new_products['products_id']),
                            'PRODUCTS_IMAGE' => xarTplGetImage('product_images/thumbnail_images/' . $new_products['products_image']),
                            'BUTTON_BUY_NOW'=>'<a href="' . xarModURL('commerce','user',(basename($PHP_SELF), xtc_get_all_get_params(array('action')) . 'action=buy_now&BUYproducts_id=' . $new_products['products_id'], 'NONSSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_buy_now.gif'),'alt' => TEXT_BUY . $new_products['products_name'] . TEXT_NOW));
    } else {



    $module_content[]=array(
                            'PRODUCTS_NAME' => $new_products['products_name'],
                            'PRODUCTS_DESCRIPTION' => $new_products['products_short_description'],
                            'PRODUCTS_PRICE' => xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$new_products['products_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1)),
                            'PRODUCTS_LINK' => xarModURL('commerce','user','product_info', 'products_id=' . $new_products['products_id']),
                            'PRODUCTS_IMAGE' => xarTplGetImage('product_images/thumbnail_images/' . $new_products['products_image']));
  }
    $row ++;

  }
   if (sizeof($module_content)>=1)
   {
  $module_smarty->assign('language', $_SESSION['language']);
  $module_smarty->assign('module_content',$module_content);
  // set cache ID
  if (USE_CACHE=='false') {
  $module_smarty->caching = 0;
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/new_products.html');
  } else {
  $module_smarty->caching = 1;
  $module_smarty->cache_lifetime=CACHE_LIFETIME;
  $module_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $GET['cPath'].$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/new_products.html',$cache_id);
  }
  $default_smarty->assign('MODULE_new_products',$module);
  }


?>