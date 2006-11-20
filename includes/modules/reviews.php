<?php
/* -----------------------------------------------------------------------------------------
   $Id: reviews.php,v 1.2 2003/11/09 11:46:55 gwinger Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(reviews.php,v 1.9 2003/02/12); www.oscommerce.com
   (c) 2003  nextcommerce (reviews.php,v 1.5 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
?>
<table border="0" cellspacing="0" cellpadding="2">
<?php
  if (sizeof($reviews_array) < 1) {
?>
  <tr>
    <td class="main"><?php echo TEXT_NO_REVIEWS; ?></td>
  </tr>
<?php
  } else {
    for($i = 0, $n = sizeof($reviews_array); $i < $n; $i++) {
?>
  <tr>
    <td valign="top" class="main"><a href="<?php echo xarModURL('commerce','user','product_reviews'_INFO, 'products_id=' . $reviews_array[$i]['products_id'] . '&reviews_id=' . $reviews_array[$i]['reviews_id']) . '">' . xtc_image(xarTplGetImage('product_images/thumbnail_images/' . $reviews_array[$i]['products_image']), $reviews_array[$i]['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT); ?></a></td>
    <td valign="top" class="main"><a href="<?php echo xarModURL('commerce','user','product_reviews'_INFO, 'products_id=' . $reviews_array[$i]['products_id'] . '&reviews_id=' . $reviews_array[$i]['reviews_id']) . '"><b><u>' . $reviews_array[$i]['products_name'] . '</u></b></a> (' . sprintf(TEXT_REVIEW_BY, $reviews_array[$i]['authors_name']) . ', ' . sprintf(TEXT_REVIEW_WORD_COUNT, $reviews_array[$i]['word_count']) . ')<br>' . $reviews_array[$i]['review'] . '<br><br><i>' . sprintf(TEXT_REVIEW_RATING, xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'stars_' . $reviews_array[$i]['rating'] . '.gif'), sprintf(TEXT_OF_5_STARS, $reviews_array[$i]['rating'])), sprintf(TEXT_OF_5_STARS, $reviews_array[$i]['rating'])) . '<br>' . sprintf(TEXT_REVIEW_DATE_ADDED, $reviews_array[$i]['date_added']) . '</i>'; ?></td>
  </tr>
<?php
      if (($i+1) != $n) {
?>
  <tr>
    <td colspan="2" class="main">&#160;</td>
  </tr>
<?php
      }
    }
  }
?>
</table>
