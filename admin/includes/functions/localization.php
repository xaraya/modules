<?php
/* --------------------------------------------------------------
   $Id: localization.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(localization.php,v 1.11 2002/11/22); www.oscommerce.com 
   (c) 2003	 nextcommerce (localization.php,v 1.4 2003/08/14); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  function quote_oanda_currency($code, $base = DEFAULT_CURRENCY) {
    $err_num = '';
    $err_msg = '';
    $s = fsockopen('www.oanda.com', 5011, $err_num, $err_msg, 5);
    if (!$s) {
      $resp = 'na';  // prevent breaking script
    } else {
      fputs($s, "fxp/1.1\r\nbasecurrency: $code\r\nquotecurrency: $base\r\n\r\n");
      $resp = fgets($s, 128);
      if (trim($resp) == "fxp/1.1 200 ok") {
        while ($resp != "\r\n") {
          $resp = fgets($s, 128);
        }
        if (!$resp = fgets($s, 128)) { // timeout? then skip
          $resp = 'na';
        }
      } else {
        $resp = 'na';
      }
      fclose($s);
    }
    if ($resp == 'na') {
      return false;
    }
    return trim($resp);
  }
  
  function quote_xe_currency($to, $from = DEFAULT_CURRENCY) {
    $regex = "/[0-9.]+\s*$from\s*=\s*([0-9.]+)\s*$to/";
    $return = file('http://www.xe.net/ucc/convert.cgi?Amount=1&From=' . $from . '&To=' . $to);
    while (list(, $line) = each($return)) {
      if (preg_match($regex, $line, $match)) {
        break;
      }
    }
    if (!isset($match)) {
      return false;
    }
    return $match[1];
  }
?>