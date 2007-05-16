<?php
/* -----------------------------------------------------------------------------------------
   $Id: product_reviews.php,v 1.4 2003/12/30 09:02:31 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_reviews.php,v 1.47 2003/02/13); www.oscommerce.com
   (c) 2003  nextcommerce (product_reviews.php,v 1.12 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  // create smarty elements
//  $module_smarty = new Smarty;
  $module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
  // include boxes
  // include needed functions

     $info_smarty->assign('options',$products_options_data);
    $reviews_query = new xenQuery("select count(*) as count from " . TABLE_REVIEWS . " where products_id = '" . $_GET['products_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
    $reviews = $q->output();
    if ($reviews['count'] > 0) {

  $product_info_query = new xenQuery("select pd.products_name from " . TABLE_PRODUCTS_DESCRIPTION . " pd left join " . TABLE_PRODUCTS . " p on pd.products_id = p.products_id where pd.language_id = '" . $_SESSION['languages_id'] . "' and p.products_status = '1' and pd.products_id = '" . (int)$_GET['products_id'] . "'");
  if (!$product_info_query->getrows()) xarRedirectResponse(xarModURL('commerce','user','reviews'));
      $q = new xenQuery();
      if(!$q->run()) return;
  $product_info = $q->output();


  $reviews_query = new xenQuery("select
                                 r.reviews_rating,
                                 r.reviews_id,
                                 r.customers_name,
                                 r.date_added,
                                 r.last_modified,
                                 r.reviews_read,
                                 rd.reviews_text
                                 from " . TABLE_REVIEWS . " r,
                                 ".TABLE_REVIEWS_DESCRIPTION ." rd
                                 where r.products_id = '" . (int)$_GET['products_id'] . "'
                                 and  r.reviews_id=rd.reviews_id
                                 and rd.languages_id = '".$_SESSION['languages_id']."'
                                 order by reviews_id DESC");
  if ($reviews_query)->getrows() {
    $row = 0;
    $data_reviews=array();
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($reviews = $q->output()) {
      $row++;
     $data_reviews[]=array(
                           'AUTHOR'=>$reviews['customers_name'],
                           'DATE'=>xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$reviews['date_added'])),
                           'RATING'=>xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif'), sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])),
                           'TEXT'=>$reviews['reviews_text']);
    if ($row==PRODUCT_REVIEWS_VIEW) break;
    }
  }

  $module_smarty->assign('BUTTON_WRITE','<a href="' . xarModURL('commerce','user','product_reviews'_WRITE, 'products_id=' . $_GET['products_id']) . '">' .
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_write_review.gif'),'alt' => IMAGE_BUTTON_WRITE_REVIEW);
</a>');


  $module_smarty->assign('language', $_SESSION['language']);
  $module_smarty->assign('module_content',$data_reviews);
  $module_smarty->caching = 0;
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/products_reviews.html');


  $info_smarty->assign('MODULE_products_reviews',$module);

}

?>