<?php
/* -----------------------------------------------------------------------------------------
   $Id: reviews.php,v 1.4 2003/12/15 19:50:45 gwinger Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(reviews.php,v 1.48 2003/05/27); www.oscommerce.com
   (c) 2003  nextcommerce (reviews.php,v 1.12 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  include( 'includes/application_top.php');
//      $smarty = new Smarty;
  // include boxes
  require(DIR_WS_INCLUDES.'boxes.php');
  // include needed functions
  require_once(DIR_FS_INC . 'xtc_word_count.inc.php');


  $breadcrumb->add(NAVBAR_TITLE_REVIEWS, xarModURL('commerce','admin','reviews'));

 require(DIR_WS_INCLUDES . 'header.php');

  $reviews_query_raw = "select r.reviews_id, left(rd.reviews_text, 250) as reviews_text, r.reviews_rating, r.date_added, p.products_id, pd.products_name, p.products_image, r.customers_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = r.products_id and r.reviews_id = rd.reviews_id and p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and rd.languages_id = '" . $_SESSION['languages_id'] . "' order by r.reviews_id DESC";
  $reviews_split = new splitPageResults($reviews_query_raw, $_GET['page'], MAX_DISPLAY_NEW_REVIEWS);

  if (($reviews_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {

  $data['NAVBAR'] = '

   <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText">'. $reviews_split->display_count(TEXT_DISPLAY_NUMBER_OF_REVIEWS).'</td>
            <td align="right" class="smallText">'.TEXT_RESULT_PAGE . ' ' . $reviews_split->display_links(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array('page', 'info', 'x', 'y'))).'</td>
          </tr>
        </table>';

  }

$module_data=array();
  if ($reviews_split->number_of_rows > 0) {
    $reviews_query = new xenQuery($reviews_split->sql_query);
      $q = new xenQuery();
      $q->run();
    while ($reviews = $q->output()) {
    $module_data[]=array(
                         'PRODUCTS_IMAGE' => xarTplGetImage('product_images/thumbnail_images/' . $reviews['products_image']), $reviews['products_name'],
                         'PRODUCTS_LINK' => xarModURL('commerce','admin','product_reviews'_INFO, 'products_id=' . $reviews['products_id'] . '&reviews_id=' . $reviews['reviews_id']),
                         'PRODUCTS_NAME' => $reviews['products_name'],
                         'AUTHOR' => $reviews['customers_name'],
                         'TEXT' => sprintf(TEXT_REVIEW_WORD_COUNT, xtc_word_count($reviews['reviews_text'], ' ')) . ')<br>' . htmlspecialchars($reviews['reviews_text']) . '..',
                         'RATING' => xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif'), sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])));


    }
    $data['module_content'] = $module_data;
  }

  $data['language'] = $_SESSION['language'];

  // set cache ID
  if (USE_CACHE=='false') {
  $smarty->caching = 0;
  return data;
  } else {
  $smarty->caching = 1;
  $smarty->cache_lifetime=CACHE_LIFETIME;
  $smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'];
  return data;
  }
  ?>