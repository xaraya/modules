<?php
/* -----------------------------------------------------------------------------------------
   $Id: ot_cod_fee.php,v 1.1 2003/10/01 18:17:10 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  define('MODULE_ORDER_TOTAL_COD_TITLE', 'Nachnahmegeb&uuml;hr');
  define('MODULE_ORDER_TOTAL_COD_DESCRIPTION', 'Berechnung der Nachnahmegeb&uuml;hr');

  define('MODULE_ORDER_TOTAL_COD_STATUS_TITLE','Nachnahmegeb&uuml;hr');
  define('MODULE_ORDER_TOTAL_COD_STATUS_DESC','Berechnung der Nachnahmegeb&uuml;hr');

  define('MODULE_ORDER_TOTAL_COD_SORT_ORDER_TITLE','Reihenfolge');
  define('MODULE_ORDER_TOTAL_COD_SORT_ORDER_DESC','Reihenfolge der Anzeige');

  define('MODULE_ORDER_TOTAL_COD_FEE_FLAT_TITLE','Pauschale Versandkosten');
  define('MODULE_ORDER_TOTAL_COD_FEE_FLAT_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
  00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
  00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
  kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
  (nicht m&ouml;glich).');

  define('MODULE_ORDER_TOTAL_COD_FEE_ITEM_TITLE','Versandkosten pro St&uuml;ck');
  define('MODULE_ORDER_TOTAL_COD_FEE_ITEM_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
  00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
  00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
  kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
  (nicht m&ouml;glich).');

  define('MODULE_ORDER_TOTAL_COD_FEE_TABLE_TITLE','Tabellarische Versandkosten');
  define('MODULE_ORDER_TOTAL_COD_FEE_TABLE_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
  00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
  00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
  kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
  (nicht m&ouml;glich).');

  define('MODULE_ORDER_TOTAL_COD_FEE_ZONES_TITLE','Versandkosten nach Zonen');
  define('MODULE_ORDER_TOTAL_COD_FEE_ZONES_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
  00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
  00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
  kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
  (nicht m&ouml;glich).');

  define('MODULE_ORDER_TOTAL_COD_FEE_AP_TITLE','&Ouml;sterreichische Post AG');
  define('MODULE_ORDER_TOTAL_COD_FEE_AP_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
  00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
  00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
  kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
  (nicht m&ouml;glich).');

  define('MODULE_ORDER_TOTAL_COD_FEE_DP_TITLE','Deutsche Post AG');
  define('MODULE_ORDER_TOTAL_COD_FEE_DP_DESC','&lt;ISO2-Code&gt;:&lt;Preis&gt;, ....<br>
  00 als ISO2-Code erm&ouml;glicht den Nachnahmeversand in alle L&auml;nder. Wenn 
  00 verwendet wird, muss dieses als letztes Argument eingetragen werden. Wenn 
  kein 00:9.99 eingetragen ist, wird der Nachnahmeversand ins Ausland nicht berechnet 
  (nicht m&ouml;glich).');

  define('MODULE_ORDER_TOTAL_COD_TAX_CLASS_TITLE','Steuerklasse');
  define('MODULE_ORDER_TOTAL_COD_TAX_CLASS_DESC','W&auml;hlen Sie eine Steuerklasse.');
?>
