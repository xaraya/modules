<?php
/* --------------------------------------------------------------
   $Id: languages.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(languages.php,v 1.5 2002/11/22); www.oscommerce.com
   (c) 2003  nextcommerce (languages.php,v 1.6 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

function commerce_adminapi_date_short($args) {
    extract($args);
  ////
  // Output a raw date string in the selected locale date format
  // $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
  // NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
      return xarLocaleGetFormattedDate('short', mktime($hour, $minute, $second, $month, $day, $year));
    } else {
      return ereg_replace('2037' . '$', $year, xarLocaleGetFormattedDate('short', mktime($hour, $minute, $second, $month, $day, 2037)));
    }

  }
}
?>