<?php
/* -----------------------------------------------------------------------------------------
   $Id: table.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(table.php,v 1.6 2003/02/16); www.oscommerce.com 
   (c) 2003	 nextcommerce (table.php,v 1.4 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

define('MODULE_SHIPPING_TABLE_TEXT_TITLE', 'Tabellarische Versandkosten');
define('MODULE_SHIPPING_TABLE_TEXT_DESCRIPTION', 'Tabellarische Versandkosten');
define('MODULE_SHIPPING_TABLE_TEXT_WAY', '');
define('MODULE_SHIPPING_TABLE_TEXT_WEIGHT', 'Gewicht');
define('MODULE_SHIPPING_TABLE_TEXT_AMOUNT', 'Menge');

define('MODULE_SHIPPING_TABLE_STATUS_TITLE' , 'Enable Table Method');
define('MODULE_SHIPPING_TABLE_STATUS_DESC' , 'Do you want to offer table rate shipping?');
define('MODULE_SHIPPING_TABLE_ALLOWED_TITLE' , 'Einzelne Versandzonen');
define('MODULE_SHIPPING_TABLE_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, in welche ein Versand möglich sein soll. zb AT,DE');
define('MODULE_SHIPPING_TABLE_COST_TITLE' , 'Shipping Table');
define('MODULE_SHIPPING_TABLE_COST_DESC' , 'The shipping cost is based on the total cost or weight of items. Example: 25:8.50,50:5.50,etc.. Up to 25 charge 8.50, from there to 50 charge 5.50, etc');
define('MODULE_SHIPPING_TABLE_MODE_TITLE' , 'Table Method');
define('MODULE_SHIPPING_TABLE_MODE_DESC' , 'The shipping cost is based on the order total or the total weight of the items ordered.');
define('MODULE_SHIPPING_TABLE_HANDLING_TITLE' , 'Handling Fee');
define('MODULE_SHIPPING_TABLE_HANDLING_DESC' , 'Handling fee for this shipping method.');
define('MODULE_SHIPPING_TABLE_TAX_CLASS_TITLE' , 'Tax Class');
define('MODULE_SHIPPING_TABLE_TAX_CLASS_DESC' , 'Use the following tax class on the shipping fee.');
define('MODULE_SHIPPING_TABLE_ZONE_TITLE' , 'Shipping Zone');
define('MODULE_SHIPPING_TABLE_ZONE_DESC' , 'If a zone is selected, only enable this shipping method for that zone.');
define('MODULE_SHIPPING_TABLE_SORT_ORDER_TITLE' , 'Sort Order');
define('MODULE_SHIPPING_TABLE_SORT_ORDER_DESC' , 'Sort order of display.');
?>
