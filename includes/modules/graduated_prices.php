<?php
/* -----------------------------------------------------------------------------------------
   $Id: graduated_prices.php,v 1.10 2003/12/30 09:02:31 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercebased on original files from OSCommerce CVS 2.2 2002/08/28 02:14:35 www.oscommerce.com
   (c) 2003  nextcommerce (graduated_prices.php,v 1.11 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
//$module_smarty= new Smarty;
$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
$module_content=array();
  // include needed functions
  require_once(DIR_FS_INC . 'xtc_format_price_graduated.inc.php');

  $staffel_query = new xenQuery("SELECT
                                     quantity,
                                     personal_offer
                                     FROM
                                     personal_offers_by_customers_status_" . $_SESSION['customers_status']['customers_status_id'] . "
                                     WHERE
                                     products_id = '" . $_GET['products_id'] . "'
                                     ORDER BY quantity ASC");
  $staffel_data = array();
  $staffel=array();
  $i='';
      $q = new xenQuery();
      if(!$q->run()) return;
  while ($staffel_values = $q->output()) {
  $staffel[]=array('stk'=>$staffel_values['quantity'],
                    'price'=>$staffel_values['personal_offer']);
  }

  for ($i=0,$n=sizeof($staffel); $i<$n; $i++) {
  if ($staffel[$i]['stk'] == 1) {
        $quantity= $staffel[$i]['stk'];
        if ($staffel[$i+1]['stk']!='') $quantity= $staffel[$i]['stk'].'-'.($staffel[$i+1]['stk']-1);
      } else {
         $quantity= ' > '.$staffel[$i]['stk'];
         if ($staffel[$i+1]['stk']!='') $quantity= $staffel[$i]['stk'].'-'.($staffel[$i+1]['stk']-1);
      }
  $staffel_data[$i] = array(
    'QUANTITY' => $quantity,
    'PRICE' => xtc_format_price_graduated($staffel[$i]['price'], $price_special=1, $calculate_currencies=true, $tax_class=$product_info['products_tax_class_id']));
  }
if (sizeof($staffel_data)>1) {
  $module_smarty->assign('language', $_SESSION['language']);
  $module_smarty->assign('module_content',$staffel_data);
  // set cache ID
  if (USE_CACHE=='false') {
  $module_smarty->caching = 0;
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/graduated_price.html');
  } else {
  $module_smarty->caching = 1;
  $module_smarty->cache_lifetime=CACHE_LIFETIME;
  $module_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$_GET['products_id'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/graduated_price.html',$cache_id);
  }
  $info_smarty->assign('MODULE_graduated_price',$module);


 };
?>