<?php
/* -----------------------------------------------------------------------------------------
   $Id: column_right.php,v 1.1 2003/09/06 22:13:53 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(column_right.php,v 1.16 2003/02/10); www.oscommerce.com
   (c) 2003  nextcommerce (column_right.php,v 1.13 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
include(DIR_WS_BOXES . 'languages.php');
  if ($_SESSION['customers_status']['customers_status_id'] == 0) include(DIR_WS_BOXES . 'admin.php');
  require(DIR_WS_BOXES . 'infobox.php');
  require(DIR_WS_BOXES . 'loginbox.php');
  if ($_SESSION['customers_status']['customers_status_show_price'] == 1) include(DIR_WS_BOXES . 'shopping_cart.php');
  if (isset($_GET['products_id'])) include(DIR_WS_BOXES . 'manufacturer_info.php');

  if (isset($_SESSION['customer_id'])) include(DIR_WS_BOXES . 'order_history.php');

  if (isset($_GET['products_id'])) {
    if (isset($_SESSION['customer_id'])) {
      $check_query = new xenQuery("select count(*) as count from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . $_SESSION['customer_id'] . "' and global_product_notifications = '1'");
      $q = new xenQuery();
      $q->run();
      $check = $q->output();
      if ($check['count'] > 0) {
        include(DIR_WS_BOXES . 'best_sellers.php');
      } else {
        include(DIR_WS_BOXES . 'product_notifications.php');
      }
    } else {
      include(DIR_WS_BOXES . 'product_notifications.php');
    }
  } else {
    include(DIR_WS_BOXES . 'best_sellers.php');
  }

  if (isset($_GET['products_id'])) {
    if (basename($PHP_SELF) != FILENAME_TELL_A_FRIEND) include(DIR_WS_BOXES . 'tell_a_friend.php');
  } else {
    include(DIR_WS_BOXES . 'specials.php');
  }

  require(DIR_WS_BOXES . 'reviews.php');

  if (substr(basename($PHP_SELF), 0, 8) != 'checkout') {

    include(DIR_WS_BOXES . 'currencies.php');
  }
?>