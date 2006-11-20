<?php
/* --------------------------------------------------------------
   $Id: english.php,v 1.2 2003/12/31 14:08:16 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(german.php,v 1.99 2003/05/28); www.oscommerce.com
   (c) 2003  nextcommerce (german.php,v 1.24 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:
   Customers Status v3.x (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

// look in your $PATH_LOCALE/locale directory for available locales..
// on RedHat6.0 I used 'en_US'
// on FreeBSD 4.0 I use 'en_US.ISO_8859-1'
// this may not work under win32 environments..
setlocale(LC_TIME, 'en_US.ISO_8859-1');
define('DATE_FORMAT_SHORT', '%m/%d/%Y');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%A %d %B, %Y'); // this is used for strftime()
define('DATE_FORMAT', 'm/d/Y');  // this is used for strftime()
define('PHP_DATE_TIME_FORMAT', 'm/d/Y H:i:s'); // this is used for date()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');

////
// Return date in raw format
// $date should be in format mm/dd/yyyy
// raw date is in format YYYYMMDD, or DDMMYYYY
function xtc_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 3, 2) . substr($date, 0, 2) . substr($date, 6, 4);
  } else {
    return substr($date, 6, 4) . substr($date, 0, 2) . substr($date, 3, 2);
  }
}

// Global entries for the <html> tag
define('HTML_PARAMS','dir="ltr" lang="en"');


// page title
define('TITLE', 'XT-Commerce');

// header text in includes/header.php
define('HEADER_TITLE_TOP', 'Administration');
define('HEADER_TITLE_SUPPORT_SITE', 'Support Site');
define('HEADER_TITLE_ONLINE_CATALOG', 'Online Catalog');
define('HEADER_TITLE_ADMINISTRATION', 'Administration');

// text for gender
define('MALE', 'Male');
define('FEMALE', 'Female');

// text for date of birth example
define('DOB_FORMAT_STRING', 'mm/dd/yyyy');

// configuration box text in includes/boxes/configuration.php

define('BOX_HEADING_CONFIGURATION','Configuration');
define('BOX_HEADING_MODULES','Modules');
define('BOX_HEADING_ZONE','Zone / Tax');
define('BOX_HEADING_CUSTOMERS','Customers');
define('BOX_HEADING_PRODUCTS','Catalog');
define('BOX_HEADING_STATISTICS','Statistics');
define('BOX_HEADING_TOOLS','Tools');

define('BOX_CONTENT','Content Manager');
define('TEXT_ALLOWED', 'Permission');
define('TEXT_ACCESS', 'Usable Area');
define('BOX_CONFIGURATION', 'General Options');
define('BOX_CONFIGURATION_1', 'My Shop');
define('BOX_CONFIGURATION_2', 'Minimum Values');
define('BOX_CONFIGURATION_3', 'Maximum Values');
define('BOX_CONFIGURATION_4', 'Image Options');
define('BOX_CONFIGURATION_5', 'Customer Details');
define('BOX_CONFIGURATION_6', 'Module Options');
define('BOX_CONFIGURATION_7', 'Shipping Options');
define('BOX_CONFIGURATION_8', 'Product Listing Options');
define('BOX_CONFIGURATION_9', 'Stock Options');
define('BOX_CONFIGURATION_10', 'Logging Options');
define('BOX_CONFIGURATION_11', 'Cache Options');
define('BOX_CONFIGURATION_12', 'E-mail Options');
define('BOX_CONFIGURATION_13', 'Download Options');
define('BOX_CONFIGURATION_14', 'Gzip Compression');
define('BOX_CONFIGURATION_15', 'Sessions');
define('BOX_CONFIGURATION_16', 'Meta-Tags/Searchengines');
define('BOX_MODULES', 'Payment-/Shipping-/Billing-Modules');
define('BOX_PAYMENT', 'Payment Systems');
define('BOX_SHIPPING', 'Shipping Methods');
define('BOX_ORDER_TOTAL', 'Order Total');
define('BOX_CATEGORIES', 'Categories / Products');
define('BOX_PRODUCTS_ATTRIBUTES', 'Product Options');
define('BOX_MANUFACTURERS', 'Manufacturers');
define('BOX_REVIEWS', 'Product Reviews');
define('BOX_XSELL_PRODUCTS', 'Cross Marketing');
define('BOX_SPECIALS', 'Special Pricing');
define('BOX_PRODUCTS_EXPECTED', 'Expected Offers');
define('BOX_CUSTOMERS', 'Customers');
define('BOX_ACCOUNTING', 'Admin Permissions');
define('BOX_CUSTOMERS_STATUS','Customer Groups');
define('BOX_ORDERS', 'Orders');
define('BOX_COUNTRIES', 'Countries');
define('BOX_ZONES', 'Zones');
define('BOX_GEO_ZONES', 'Tax Zones');
define('BOX_TAX_CLASSES', 'Tax Classes');
define('BOX_TAX_RATES', 'Tax Rates');
define('BOX_HEADING_REPORTS', 'Reports');
define('BOX_PRODUCTS_VIEWED', 'Viewed Products');
define('BOX_STOCK_WARNING','Stock Info');
define('BOX_PRODUCTS_PURCHASED', 'Sold Products');
define('BOX_STATS_CUSTOMERS', 'Purchasing Statistics');
define('BOX_BACKUP', 'Database Manager');
define('BOX_BANNER_MANAGER', 'Banner Manager');
define('BOX_CACHE', 'Cache Control');
define('BOX_DEFINE_LANGUAGE', 'Language Definitions');
define('BOX_FILE_MANAGER', 'File-Manager');
define('BOX_MAIL', 'eMail Center');
define('BOX_NEWSLETTERS', 'Notification Manager');
define('BOX_SERVER_INFO', 'Server Info');
define('BOX_WHOS_ONLINE', 'Who is Online');
define('BOX_TPL_BOXES','Boxes Sort Order');
define('BOX_CURRENCIES', 'Currencies');
define('BOX_LANGUAGES', 'Languages');
define('BOX_ORDERS_STATUS', 'Order Status');
define('BOX_ATTRIBUTES_MANAGER','Attribute Manager');
define('BOX_PRODUCTS_ATTRIBUTES','Option-Groups');
define('BOX_MODULE_NEWSLETTER','Newsletter');

//Dividers text for menu

define('BOX_HEADING_MODULES', 'Modules');
define('BOX_HEADING_LOCALIZATION', 'Languages/Currencies');
define('BOX_HEADING_TEMPLATES','Templates');
define('BOX_HEADING_TOOLS', 'Tools');
define('BOX_HEADING_LOCATION_AND_TAXES', 'Location / Tax');
define('BOX_HEADING_CUSTOMERS', 'Customers');
define('BOX_HEADING_CATALOG', 'Catalog');

// javascript messages
define('JS_ERROR', 'Error have occured during the process of your form!\nPlease make the following corrections:\n\n');

define('JS_OPTIONS_VALUE_PRICE', '* The new product attribute needs a price value\n');
define('JS_OPTIONS_VALUE_PRICE_PREFIX', '* The new product attribute needs a price prefix (+/-)\n');

define('JS_PRODUCTS_NAME', '* The new product needs a name\n');
define('JS_PRODUCTS_DESCRIPTION', '* The new product needs a description\n');
define('JS_PRODUCTS_PRICE', '* The new product needs a price value\n');
define('JS_PRODUCTS_WEIGHT', '* The new product needs a weight value\n');
define('JS_PRODUCTS_QUANTITY', '* The new product needs a quantity value\n');
define('JS_PRODUCTS_MODEL', '* The new product needs a model value\n');
define('JS_PRODUCTS_IMAGE', '* The new product needs an image value\n');

define('JS_SPECIALS_PRODUCTS_PRICE', '* A new price for this product needs to be set\n');

define('JS_GENDER', '* The \'Gender\' value must be chosen.\n');
define('JS_FIRST_NAME', '* The \'First Name\' entry must have at least ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' characters.\n');
define('JS_LAST_NAME', '* The \'Last Name\' entry must have at least ' . ENTRY_LAST_NAME_MIN_LENGTH . ' characters.\n');
define('JS_DOB', '* The \'Date of Birth\' entry must be in the format: xx/xx/xxxx (month/date/year).\n');
define('JS_EMAIL_ADDRESS', '* The \'eMail-Adress\' entry must have at least ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' characters.\n');
define('JS_ADDRESS', '* The \'Street Adress\' entry must have at least ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' characters.\n');
define('JS_POST_CODE', '* The \'Post Code\' entry must have at least ' . ENTRY_POSTCODE_MIN_LENGTH . ' characters.\n');
define('JS_CITY', '* The \'City\' entry must have at least ' . ENTRY_CITY_MIN_LENGTH . ' characters.\n');
define('JS_STATE', '* The \'State\' entry must be selected.\n');
define('JS_STATE_SELECT', '-- Select above --');
define('JS_ZONE', '* The \'State\' entry must be selected from the list for this country.');
define('JS_COUNTRY', '* The \'Country\' value must be chosen.\n');
define('JS_TELEPHONE', '* The \'Telephone Number\' entry must have at least ' . ENTRY_TELEPHONE_MIN_LENGTH . ' characters.\n');
define('JS_PASSWORD', '* The \'Password\' and \'Confirmation\' entries must match and have at least ' . ENTRY_PASSWORD_MIN_LENGTH . ' characters.\n');

define('JS_ORDER_DOES_NOT_EXIST', 'Order Number %s does not exist!');

define('CATEGORY_PERSONAL', 'Personal');
define('CATEGORY_ADDRESS', 'Adress');
define('CATEGORY_CONTACT', 'Contact');
define('CATEGORY_COMPANY', 'Company');
define('CATEGORY_OPTIONS', 'More Options');

define('ENTRY_GENDER', 'Gender:');
define('ENTRY_GENDER_ERROR', '&#160;<span class="errorText">required</span>');
define('ENTRY_FIRST_NAME', 'First Name:');
define('ENTRY_FIRST_NAME_ERROR', '&#160;<span class="errorText">min. ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' chars</span>');
define('ENTRY_LAST_NAME', 'Last Name:');
define('ENTRY_LAST_NAME_ERROR', '&#160;<span class="errorText">min. ' . ENTRY_LAST_NAME_MIN_LENGTH . ' chars</span>');
define('ENTRY_DATE_OF_BIRTH', 'Date of Birth:');
define('ENTRY_DATE_OF_BIRTH_ERROR', '&#160;<span class="errorText">(e.g. 05/21/1970)</span>');
define('ENTRY_EMAIL_ADDRESS', 'eMail Adress:');
define('ENTRY_EMAIL_ADDRESS_ERROR', '&#160;<span class="errorText">min. ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' chars</span>');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', '&#160;<span class="errorText">Invalid eMail-Adress!</span>');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', '&#160;<span class="errorText">This eMail-Adress already exists!</span>');
define('ENTRY_COMPANY', 'Company name:');
define('ENTRY_STREET_ADDRESS', 'Street Adress:');
define('ENTRY_STREET_ADDRESS_ERROR', '&#160;<span class="errorText">min. ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' Chars</span>');
define('ENTRY_SUBURB', 'Suburb:');
define('ENTRY_POST_CODE', 'Post Code:');
define('ENTRY_POST_CODE_ERROR', '&#160;<span class="errorText">min. ' . ENTRY_POSTCODE_MIN_LENGTH . ' chars</span>');
define('ENTRY_CITY', 'City:');
define('ENTRY_CITY_ERROR', '&#160;<span class="errorText">min. ' . ENTRY_CITY_MIN_LENGTH . ' chars</span>');
define('ENTRY_STATE', 'State:');
define('ENTRY_STATE_ERROR', '&#160;<span class="errorText">required</font></small>');
define('ENTRY_COUNTRY', 'County:');
define('ENTRY_TELEPHONE_NUMBER', 'Telephone Number:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', '&#160;<span class="errorText">min. ' . ENTRY_TELEPHONE_MIN_LENGTH . ' chars</span>');
define('ENTRY_FAX_NUMBER', 'Fax Number:');
define('ENTRY_NEWSLETTER', 'Newsletter:');
define('ENTRY_CUSTOMERS_STATUS', 'Customers status:');
define('ENTRY_NEWSLETTER_YES', 'Subscribed');
define('ENTRY_NEWSLETTER_NO', 'Unsubscribed');
define('ENTRY_MAIL_ERROR','&#160;<span class="errorText">Please choose an option</span>');
define('ENTRY_PASSWORD','Password (generated)');
define('ENTRY_PASSWORD_ERROR','&#160;<span class="errorText">min. ' . ENTRY_PASSWORD_MIN_LENGTH . ' chars</span>');
define('ENTRY_MAIL_COMMENTS','Mailtext:');

define('ENTRY_MAIL','Send eMail with password to customer?');
define('YES','yes');
define('NO','no');


// images
define('IMAGE_ANI_SEND_EMAIL', 'Sending E-Mail');
define('IMAGE_BACK', 'Back');
define('IMAGE_BACKUP', 'Backup');
define('IMAGE_CANCEL', 'Cancel');
define('IMAGE_CONFIRM', 'Confirm');
define('IMAGE_COPY', 'Copy');
define('IMAGE_COPY_TO', 'Copy to');
define('IMAGE_DETAILS', 'Details');
define('IMAGE_DELETE', 'Delete');
define('IMAGE_EDIT', 'Edit');
define('IMAGE_EMAIL', 'eMail');
define('IMAGE_FILE_MANAGER', 'File-Manager');
define('IMAGE_ICON_STATUS_GREEN', 'Active');
define('IMAGE_ICON_STATUS_GREEN_LIGHT', 'Set active');
define('IMAGE_ICON_STATUS_RED', 'Inactiv');
define('IMAGE_ICON_STATUS_RED_LIGHT', 'Set inactive');
define('IMAGE_ICON_INFO', 'Info');
define('IMAGE_INSERT', 'Insert');
define('IMAGE_LOCK', 'Lock');
define('IMAGE_MODULE_INSTALL', 'Install Module');
define('IMAGE_MODULE_REMOVE', 'Remove Module');
define('IMAGE_MOVE', 'Move');
define('IMAGE_NEW_BANNER', 'New Banner');
define('IMAGE_NEW_CATEGORY', 'New Category');
define('IMAGE_NEW_COUNTRY', 'New Country');
define('IMAGE_NEW_CURRENCY', 'New Currency');
define('IMAGE_NEW_FILE', 'New File');
define('IMAGE_NEW_FOLDER', 'New Folder');
define('IMAGE_NEW_LANGUAGE', 'New Language');
define('IMAGE_NEW_NEWSLETTER', 'New Newsletter');
define('IMAGE_NEW_PRODUCT', 'New Product');
define('IMAGE_NEW_TAX_CLASS', 'New Tax Class');
define('IMAGE_NEW_TAX_RATE', 'New Tax Rate');
define('IMAGE_NEW_TAX_ZONE', 'New Tax Zone');
define('IMAGE_NEW_ZONE', 'New Zone');
define('IMAGE_ORDERS', 'Orders');
define('IMAGE_ORDERS_INVOICE', 'Invoice');
define('IMAGE_ORDERS_PACKINGSLIP', 'Packing Slip');
define('IMAGE_PREVIEW', 'Preview');
define('IMAGE_RESET', 'Reset');
define('IMAGE_RESTORE', 'Restore');
define('IMAGE_SAVE', 'Save');
define('IMAGE_SEARCH', 'Search');
define('IMAGE_SELECT', 'Select');
define('IMAGE_SEND', 'Send');
define('IMAGE_SEND_EMAIL', 'Send eMail');
define('IMAGE_UNLOCK', 'Unlock');
define('IMAGE_UPDATE', 'Update');
define('IMAGE_UPDATE_CURRENCIES', 'Update Currencies');
define('IMAGE_UPLOAD', 'Upload');
define('IMAGE_ACCOUNTING','Accounting');
define('IMAGE_STATUS','Customer status');
define('IMAGE_IPLOG','IP-Log');
define('CREATE_ACCOUNT','Create Account');

define('ICON_CROSS', 'False');
define('ICON_CURRENT_FOLDER', 'Current Folder');
define('ICON_DELETE', 'Delete');
define('ICON_ERROR', 'Error');
define('ICON_FILE', 'File');
define('ICON_FILE_DOWNLOAD', 'Download');
define('ICON_FOLDER', 'Folder');
define('ICON_LOCKED', 'Locked');
define('ICON_PREVIOUS_LEVEL', 'Previous Level');
define('ICON_PREVIEW', 'Preview');
define('ICON_STATISTICS', 'Statistics');
define('ICON_SUCCESS', 'Success');
define('ICON_TICK', 'True');
define('ICON_UNLOCKED', 'Unlocked');
define('ICON_WARNING', 'Warning');

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', 'Page %s of %d');
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Banners)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRIES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Countries)');
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Customers)');
define('TEXT_DISPLAY_NUMBER_OF_CURRENCIES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Currencies)');
define('TEXT_DISPLAY_NUMBER_OF_LANGUAGES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Languages)');
define('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Manufacturers)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Newsletters)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Orders)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Orders Status)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Products)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> products expected)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Reviews)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> products on special)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Tax Classes)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_ZONES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Tax Zones)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_RATES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Tax Rates)');
define('TEXT_DISPLAY_NUMBER_OF_ZONES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> zones)');

define('PREVNEXT_BUTTON_PREV', '&lt;&lt;');
define('PREVNEXT_BUTTON_NEXT', '&gt;&gt;');

define('TEXT_DEFAULT', 'default');
define('TEXT_SET_DEFAULT', 'Set as default');
define('TEXT_FIELD_REQUIRED', '&#160;<span class="fieldRequired">* Required</span>');

define('ERROR_NO_DEFAULT_CURRENCY_DEFINED', 'Error: There is currently no default currency set. Please set one at: Administration Tool -> Localization -> Currencies');

define('TEXT_CACHE_CATEGORIES', 'Categories Box');
define('TEXT_CACHE_MANUFACTURERS', 'Manufacturers Box');
define('TEXT_CACHE_ALSO_PURCHASED', 'Also Purchased Modul');

define('TEXT_NONE', '--none--');
define('TEXT_TOP', 'Top');

define('ERROR_DESTINATION_DOES_NOT_EXIST', 'Error: Destination does not exist.');
define('ERROR_DESTINATION_NOT_WRITEABLE', 'Error: Destination is not writeable.');
define('ERROR_FILE_NOT_SAVED', 'Error: File upload not saved.');
define('ERROR_FILETYPE_NOT_ALLOWED', 'Error: File upload type not allowed.');
define('SUCCESS_FILE_SAVED_SUCCESSFULLY', 'Success: File upload saved successfully.');
define('WARNING_NO_FILE_UPLOADED', 'Warnung: No file uploaded.');

define('DELETE_ENTRY','Delete entry?');
define('TEXT_PAYMENT_ERROR','<b>WARNING:</b><br>Please activate a Payment Module!');
define('TEXT_SHIPPING_ERROR','<b>WARNING:</b><br>Please activate a Shipping Module!');

define('TEXT_NETTO','no tax: ');
?>