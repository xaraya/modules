<?php
/* -----------------------------------------------------------------------------------------
   $Id: counter.php,v 1.1 2003/09/06 22:13:53 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(counter.php,v 1.5 2003/02/10); www.oscommerce.com
   (c) 2003  nextcommerce (counter.php,v 1.6 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  $counter_query = new xenQuery("select startdate, counter from " . TABLE_COUNTER);
  if ($_SESSION['counter_count'] != 1) {
    if (!$counter_query->getrows()) {
      $date_now = date('Ymd');
      new xenQuery("insert into " . TABLE_COUNTER . " (startdate, counter) values ('" . $date_now . "', '1')");
      $counter_startdate = $date_now;
      $counter_now = 1;
      $_SESSION['counter_count'] = 1;
    } else {
      $q = new xenQuery();
      if(!$q->run()) return;
      $counter = $q->output();
      $counter_startdate = $counter['startdate'];
      $counter_now = ($counter['counter'] + 1);
      new xenQuery("update " . TABLE_COUNTER . " set counter = '" . $counter_now . "'");
      $_SESSION['counter_count'] = 1;
    }
    $counter_startdate_formatted = strftime(DATE_FORMAT_LONG, mktime(0, 0, 0, substr($counter_startdate, 4, 2), substr($counter_startdate, -2), substr($counter_startdate, 0, 4)));
  } else {
      $q = new xenQuery();
      if(!$q->run()) return;
    $counter = $q->output();
    $counter_startdate = $counter['startdate'];
    $counter_now = $counter['counter'];
    $counter_startdate_formatted = strftime(DATE_FORMAT_LONG, mktime(0, 0, 0, substr($counter_startdate, 4, 2), substr($counter_startdate, -2), substr($counter_startdate, 0, 4)));
  }
?>