<?php
/* -----------------------------------------------------------------------------------------
   $Id: upcoming_products.php,v 1.4 2003/12/30 09:02:31 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(upcoming_products.php,v 1.23 2003/02/12); www.oscommerce.com
   (c) 2003  nextcommerce (upcoming_products.php,v 1.7 2003/08/22); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
//$module_smarty= new Smarty;
$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
  // include needed functions
   $module_content=array();
  $expected_query = new xenQuery("select p.products_id, pd.products_name, products_date_available as date_expected from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where to_days(products_date_available) >= to_days(now()) and p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' order by " . EXPECTED_PRODUCTS_FIELD . " " . EXPECTED_PRODUCTS_SORT . " limit " . MAX_DISPLAY_UPCOMING_PRODUCTS);
  if ($expected_query->getrows() > 0) {

    $row = 0;
      $q = new xenQuery();
      $q->run();
    while ($expected = $q->output()) {
      $row++;
      $module_content[]=array('PRODUCTS_LINK'=>xarModURL('commerce','user','product_info', 'products_id=' . $expected['products_id']),
                               'PRODUCTS_NAME'=>$expected['products_name'],
                               'PRODUCTS_DATE'=>xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$expected['date_expected'])));

    }


    $module_smarty->assign('language', $_SESSION['language']);
  $module_smarty->assign('module_content',$module_content);
  // set cache ID
  if (USE_CACHE=='false') {
  $module_smarty->caching = 0;
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/upcoming_products.html');
  } else {
  $module_smarty->caching = 1;
  $module_smarty->cache_lifetime=CACHE_LIFETIME;
  $module_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/upcoming_products.html',$cache_id);
  }
  $default_smarty->assign('MODULE_upcoming_products',$module);
  }
?>