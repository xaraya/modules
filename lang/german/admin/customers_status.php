<?php
/* --------------------------------------------------------------
   $Id: customers_status.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(customers.php,v 1.76 2003/05/04); www.oscommerce.com 
   (c) 2003	 nextcommerce (customers_status.php,v 1.12 2003/08/14); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/
   
define('ENTRY_YES','Ja');
define('ENTRY_NO','Nein'); 
define('TEXT_INFO_HEADING_EDIT_CUSTOMERS_STATUS','Gruppendaten bearbeiten');
define('TEXT_INFO_EDIT_INTRO','Ändern Sie hier die Einstellungen dieser Gruppe');
define('TEXT_INFO_CUSTOMERS_STATUS_PUBLIC_INTRO','<b>Gruppe Öffentlich ?</b>');
define('ENTRY_CUSTOMERS_STATUS_PUBLIC','Öffentlich');
define('TEXT_INFO_CUSTOMERS_STATUS_NAME','<b>Gruppenname</b>');
define('HEADING_TITLE','Kundengruppen');
define('TABLE_HEADING_CUSTOMERS_STATUS','Gruppe');
define('TABLE_HEADING_ACTION','Aktion');
define('TEXT_INFO_CUSTOMERS_STATUS_IMAGE','Gruppen Bild');
define('TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_INTRO','<b>Preisanzeige im Shop</b>');
define('ENTRY_CUSTOMERS_STATUS_SHOW_PRICE','Preis');
define('TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_TAX_INTRO','<b>Preise incl. oder excl. Steuer im Shop</b>');
define('ENTRY_CUSTOMERS_STATUS_SHOW_PRICE_TAX','Preise incl. Steuer');
define('TEXT_INFO_CUSTOMERS_STATUS_ADD_TAX_INTRO','<b>Falls Preis incl. Steuer = "Nein"</b>');
define('ENTRY_CUSTOMERS_STATUS_ADD_TAX','UST in Rechnung ausweisen');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE_INTRO','<b>Max. % Rabatt auf ein Produkt</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE','Rabatt');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_OT_XMEMBER_INTRO','<b>Rabatt auf gesamte Bestellung</b>');
define('ENTRY_OT_XMEMBER','Rabatt');
define('ENTRY_CUSTOMERS_STATUS_COD_PERMISSION','Per Nachnahme');
define('ENTRY_CUSTOMERS_STATUS_CC_PERMISSION','Per Kreditkarte');
define('ENTRY_CUSTOMERS_STATUS_BT_PERMISSION','Per Bankeinzug');
define('TEXT_INFO_CUSTOMERS_STATUS_GRADUATED_PRICES_INTRO','<b>Anzeige - Staffelpreise</b>');
define('ENTRY_GRADUATED_PRICES','Staffelpreise');
define('TEXT_INFO_CUSTOMERS_STATUS_PAYMENT_UNALLOWED_INTRO','<b>Nicht erlaubte Zahlungsweisen</b>');
define('ENTRY_CUSTOMERS_STATUS_PAYMENT_UNALLOWED','Geben Sie unerlaubte Zahlungsweisen ein');
define('TABLE_HEADING_CUSTOMERS_UNALLOW','n.a. Zahlungweisen');
define('TABLE_HEADING_CUSTOMERS_GRADUATED','Staffelpreis');
define('TAX_YES','incl');
define('TAX_NO','excl');
define('TABLE_HEADING_TAX_PRICE','Preis / Steuer');
define('TABLE_HEADING_DISCOUNT','Rabatte');
define('YES','ja');
define('NO','nein');
define('HEADING_TITLE', 'Customers Status');
define('TABLE_HEADING_CUSTOMERS_STATUS', 'Kundenstatus');
define('TABLE_HEADING_ACTION', 'Action');
define('TEXT_INFO_EDIT_INTRO', 'Bitte nehmen Sie alle nötigen Einstellungen vor');
define('TEXT_INFO_CUSTOMERS_STATUS_NAME', 'Kundenstatus:');
define('TEXT_INFO_CUSTOMERS_STATUS_IMAGE', 'Kundenstatus Bild:');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE_INTRO', 'Geben Sie eine Discount Rate zwischen 0 und 100% an, die bei jedem angezeigten Produkt verwendet wird.');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE', 'Discount (0 bis 100%):');
define('TEXT_INFO_INSERT_INTRO', 'Bitte erstellen Sie einen neuen Kundenstatus mit den gewünschten Einstellungen');
define('TEXT_INFO_DELETE_INTRO', 'Sind Sie sicher, daß Sie diesen Kundenstatus löschen wollen?');
define('TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_TAX_INTRO', 'Möchten Sie die Preise inklusive oder exklusive Steuer anzeigen?');
define('TEXT_INFO_CUSTOMERS_STATUS_COD_PERMISSION_INTRO', '<b>Möchten Sie erlauben, daß diese Kundengruppe per Nachnahme bezahlen darf?</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_CC_PERMISSION_INTRO', '<b>Möchten Sie erlauben, daß diese Kundengruppe per Kreditkartensystemen bezahlen darf?</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_BT_PERMISSION_INTRO', '<b>Möchten Sie erlauben, daß diese Kundengruppe per Bankeinzug bezahlen darf?</b>');
define('TEXT_INFO_HEADING_NEW_CUSTOMERS_STATUS', 'Neuer Kundenstatus');
define('TEXT_INFO_HEADING_EDIT_CUSTOMERS_STATUS', 'Kundenstatus ändern');
define('TEXT_INFO_HEADING_DELETE_CUSTOMERS_STATUS', 'Kundenstatus löschen');
define('ERROR_REMOVE_DEFAULT_CUSTOMER_STATUS', 'Error: Der Default Kundenstatus kann nicht gelöscht werden. Bitte legen Sie zuerst einen anderen Default Kundenstatus an, und versuchen Sie es erneut.');
define('ERROR_STATUS_USED_IN_CUSTOMERS', 'Error: Dieser Kundenstatus wird zur Zeit bei Kunden verwendet.');
define('ERROR_STATUS_USED_IN_HISTORY', 'Error: Dieser Kundenstatus wird zur Zeit in der Bestellübersicht verwendet.');
define('ENTRY_OT_XMEMBER', 'Kundenrabatt ? :');
define('ENTRY_YES', 'Ja');
define('ENTRY_NO', 'Nein');
define('ENTRY_CUSTOMERS_STATUS_SHOW_PRICE_TAX', 'Preis inclusive Steuer:');
define('ENTRY_GRADUATED_PRICES', 'Staffelpreis:');
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS_STATUS', 'Vorhandene Benutzergruppen:');
define('TABLE_HEADING_CUSTOMERS_UNALLOW_SHIPPING','n.a. Versandarten');
define('TEXT_INFO_CUSTOMERS_STATUS_SHIPPING_UNALLOWED_INTRO','<b>Nicht erlaubte Versandarten</b>');
define('ENTRY_CUSTOMERS_STATUS_SHIPPING_UNALLOWED','Geben Sie unerlaubte Versandarten ein');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO','<b>Rabatt auf Produkt Attribute</b><br>(Max. % Rabatt auf ein Produkt anwenden)');
define('ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES','Rabatt');
?>