<?php
/* -----------------------------------------------------------------------------------------
   $Id: zones.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(zones.php,v 1.3 2002/04/17); www.oscommerce.com 
   (c) 2003	 nextcommerce (zones.php,v 1.4 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

define('MODULE_SHIPPING_ZONES_TEXT_TITLE', 'Versandkosten nach Zonen');
define('MODULE_SHIPPING_ZONES_TEXT_DESCRIPTION', 'Versandkosten Zonenbasierend');
define('MODULE_SHIPPING_ZONES_TEXT_WAY', 'Versand nach:');
define('MODULE_SHIPPING_ZONES_TEXT_UNITS', 'kg');
define('MODULE_SHIPPING_ZONES_INVALID_ZONE', 'Es ist kein Versand in dieses Land m&ouml;glich!');
define('MODULE_SHIPPING_ZONES_UNDEFINED_RATE', 'Die Versandkosten k&ouml;nnen im Moment nicht berechnet werden.');

define('MODULE_SHIPPING_ZONES_STATUS_TITLE' , 'Enable Zones Method');
define('MODULE_SHIPPING_ZONES_STATUS_DESC' , 'Do you want to offer zone rate shipping?');
define('MODULE_SHIPPING_ZONES_ALLOWED_TITLE' , 'Einzelne Versandzonen');
define('MODULE_SHIPPING_ZONES_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, in welche ein Versand möglich sein soll. zb AT,DE');
define('MODULE_SHIPPING_ZONES_TAX_CLASS_TITLE' , 'Tax Class');
define('MODULE_SHIPPING_ZONES_TAX_CLASS_DESC' , 'Use the following tax class on the shipping fee.');
define('MODULE_SHIPPING_ZONES_SORT_ORDER_TITLE' , 'Sort Order');
define('MODULE_SHIPPING_ZONES_SORT_ORDER_DESC' , 'Sort order of display.');
define('MODULE_SHIPPING_ZONES_COUNTRIES_1_TITLE' , 'Zone 1 Countries');
define('MODULE_SHIPPING_ZONES_COUNTRIES_1_DESC' , 'Comma separated list of two character ISO country codes that are part of Zone 1.');
define('MODULE_SHIPPING_ZONES_COST_1_TITLE' , 'Zone 1 Shipping Table');
define('MODULE_SHIPPING_ZONES_COST_1_DESC' , 'Shipping rates to Zone 1 destinations based on a group of maximum order weights. Example: 3:8.50,7:10.50,... Weights less than or equal to 3 would cost 8.50 for Zone 1 destinations.');
define('MODULE_SHIPPING_ZONES_HANDLING_1_TITLE' , 'Zone 1 Handling Fee');
define('MODULE_SHIPPING_ZONES_HANDLING_1_DESC' , 'Handling Fee for this shipping zone');
?>
