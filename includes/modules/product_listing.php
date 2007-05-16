<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------
////$module_smarty= new Smarty;
//$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
  // include needed functions
//  require_once(DIR_FS_INC . 'xtc_get_all_get_params.inc.php');

  $listing_split = new splitPageResults($listing_sql, $_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, 'p.products_id');
  $module_content=array();
  if ($listing_split->number_of_rows > 0) {
    $rows = 0;
    $listing_query = new xenQuery($listing_split->sql_query);
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($listing = $q->output()) {
      $rows++;
      if ($_SESSION['customers_status']['customers_status_show_price'] != '0') {
        $price=xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$listing['products_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1));
        $buy_now='<a href="' . xarModURL('commerce','user',(basename($PHP_SELF), xtc_get_all_get_params(array('action')) . 'action=buy_now&BUYproducts_id=' . $listing['products_id'], 'NONSSL') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_buy_now.gif'),'alt' => TEXT_BUY . $listing['products_name'] . TEXT_NOW);;
      }
      $image='';
      if ($listing['products_image']!='') {
      $image= xarTplGetImage('product_images/thumbnail_images/' . $listing['products_image']);
      }
      $module_content[]=array(
                    'PRODUCTS_NAME'=>$listing['products_name'],
                    'PRODUCTS_SHORT_DESCRIPTION'=>$listing['products_short_description'],
                    'PRODUCTS_IMAGE'=>$image,
                    'PRODUCTS_PRICE'=>$price,
                    'PRODUCTS_LINK' =>xarModURL('commerce','user','product_info', 'products_id=' . $listing['products_id']),
                    'BUTTON_BUY_NOW'=>$buy_now,
                    'PRODUCTS_ID'=>$listing['products_id']);
    }
  }

  if  ($listing_split->number_of_rows > 0) {

        $navigation='
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText">'.$listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS).'</td>
            <td class="smallText" align="right">'.TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array('page', 'info', 'x', 'y'))).'</td>
          </tr>
        </table>';

} else {
    $module_smarty->assign('result','false');
}
  $module_smarty->assign('MANUFACTURER_DROPDOWN',$manufacturer_dropdown);
  $module_smarty->assign('language', $_SESSION['language']);
  $module_smarty->assign('module_content',$module_content);
  $module_smarty->assign('NAVIGATION',$navigation);
  // set cache ID
  if (USE_CACHE=='false') {
  $module_smarty->caching = 0;
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/product_listing/product_listing_v1.html');
  } else {
  $module_smarty->caching = 1;
  $module_smarty->cache_lifetime=CACHE_LIFETIME;
  $module_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_GET['cPath'].$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'].$_GET['manufacturers_id'].$_GET['filter_id'];
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/product_listing/product_listing_v1.html',$cache_id);
  }
  $smarty->assign('main_content',$module);

?>