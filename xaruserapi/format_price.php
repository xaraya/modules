<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 Mario Zanier for XTcommerce
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------
// include needed functions
require_once(DIR_FS_INC . 'xtc_precision.inc.php');

function commerce_userapi_format_price ($price_string,$price_special,$calculate_currencies,$show_currencies=1)
{
// calculate currencies

$currencies_query = new xenQuery("SELECT symbol_left,
          symbol_right,
          decimal_places,
          value
          FROM ". TABLE_CURRENCIES ." WHERE
          code = '".$_SESSION['currency'] ."'");
      $q = new xenQuery();
      $q->run();
$currencies_value=$q->output();
$currencies_data=array();
$currencies_data=array(
      'SYMBOL_LEFT'=>$currencies_value['symbol_left'] ,
      'SYMBOL_RIGHT'=>$currencies_value['symbol_right'] ,
      'DECIMAL_PLACES'=>$currencies_value['decimal_places'] ,
      'VALUE'=> $currencies_value['value']);
if ($calculate_currencies=='true') {
$price_string=$price_string * $currencies_data['VALUE'];
}
// round price
$price_string=xtc_precision($price_string,$currencies_data['DECIMAL_PLACES']);


if ($price_special=='1') {
$currencies_query = new xenQuery("SELECT symbol_left,
          decimal_point,
          thousands_point,
          value
          FROM ". TABLE_CURRENCIES ." WHERE
          code = '".$_SESSION['currency'] ."'");
      $q = new xenQuery();
      $q->run();
$currencies_value=$q->output();
$price_string=number_format($price_string,$currencies_data['DECIMAL_PLACES'], $currencies_value['decimal_point'], $currencies_value['thousands_point']);
  if ($show_currencies == 1) {
    $price_string = $currencies_data['SYMBOL_LEFT']. ' '.$price_string.' '.$currencies_data['SYMBOL_RIGHT'];
  }
}
return $price_string;
}
?>
