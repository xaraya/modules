<?php
/* -----------------------------------------------------------------------------------------
   $Id: boxes.php,v 1.5 2003/12/30 09:01:38 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  include(DIR_WS_BOXES . 'categories.php');
  include(DIR_WS_BOXES . 'manufacturers.php');
  if ($_SESSION['customers_status']['customers_status_show_price']!='0') {
  require(DIR_WS_BOXES . 'add_a_quickie.php');
  }
  require(DIR_WS_BOXES . 'whats_new.php');
  require(DIR_WS_BOXES . 'search.php');
  require(DIR_WS_BOXES . 'content.php');
  require(DIR_WS_BOXES . 'information.php');
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
      if(!$q->run()) return;
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

$data['tpl_path'] = 'templates/'.CURRENT_TEMPLATE.'/';
?>