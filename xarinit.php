<?php
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2003 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Modified by: Nuncanada
// Modified by: marcinmilan
// Purpose of file:  Initialisation functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

include_once 'modules/xen/xarclasses/xenquery.php';
//Load Table Maintainance API
xarDBLoadTableMaintenanceAPI();

/**
 * initialise the commerce module
 */
function commerce_init()
{
    $q = new xenQuery();
    $prefix = xarDBGetSiteTablePrefix();


    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_address_book";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_address_book (
       address_book_id int NOT NULL auto_increment,
       customers_id int NOT NULL,
       entry_gender char(1) NOT NULL,
       entry_company varchar(32),
       entry_firstname varchar(32) NOT NULL,
       entry_lastname varchar(32) NOT NULL,
       entry_street_address varchar(64) NOT NULL,
       entry_suburb varchar(32),
       entry_postcode varchar(10) NOT NULL,
       entry_city varchar(32) NOT NULL,
       entry_state varchar(32),
       entry_country_id int DEFAULT '0' NOT NULL,
       entry_zone_id int DEFAULT '0' NOT NULL,
       PRIMARY KEY (address_book_id),
       KEY idx_address_book_customers_id (customers_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_customers_memo";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_customers_memo (
      memo_id int(11) NOT NULL auto_increment,
      customers_id int(11) NOT NULL default '0',
      memo_date date NOT NULL default '0000-00-00',
      memo_title text NOT NULL,
      memo_text text NOT NULL,
      poster_id int(11) NOT NULL default '0',
      PRIMARY KEY  (memo_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_products_xsell";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_products_xsell (
    ID int(10) NOT NULL auto_increment,
    products_id int(10) unsigned NOT NULL default '1',
    xsell_id int(10) unsigned NOT NULL default '1',
    sort_order int(10) unsigned NOT NULL default '1',
    PRIMARY KEY  (ID)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_address_format";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_address_format (
      address_format_id int NOT NULL auto_increment,
      address_format varchar(128) NOT NULL,
      address_summary varchar(48) NOT NULL,
      PRIMARY KEY (address_format_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_admin_access";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_admin_access (
      customers_id int(11) NOT NULL default '0',
      configuration int(1) NOT NULL default '0',
      modules int(1) NOT NULL default '0',
      countries int(1) NOT NULL default '0',
      currencies int(1) NOT NULL default '0',
      zones int(1) NOT NULL default '0',
      geo_zones int(1) NOT NULL default '0',
      tax_classes int(1) NOT NULL default '0',
      tax_rates int(1) NOT NULL default '0',
      customers int(1) NOT NULL default '0',
      create_account int(1) NOT NULL default '0',
      accounting int(1) NOT NULL default '0',
      customers_status int(1) NOT NULL default '0',
      orders int(1) NOT NULL default '0',
      categories int(1) NOT NULL default '0',
      new_attributes int(1) NOT NULL default '0',
      products_attributes int(1) NOT NULL default '0',
      manufacturers int(1) NOT NULL default '0',
      reviews int(1) NOT NULL default '0',
      xsell_products int(1) NOT NULL default '0',
      specials int(1) NOT NULL default '0',
      stats_products_expected int(1) NOT NULL default '0',
      stats_products_viewed int(1) NOT NULL default '0',
      stats_products_purchased int(1) NOT NULL default '0',
      stats_customers int(1) NOT NULL default '0',
      backup int(1) NOT NULL default '0',
      banner_manager int(1) NOT NULL default '0',
      cache int(1) NOT NULL default '0',
      define_language int(1) NOT NULL default '0',
      file_manager int(1) NOT NULL default '0',
      mail int(1) NOT NULL default '0',
      newsletters int(1) NOT NULL default '0',
      server_info int(1) NOT NULL default '0',
      whos_online int(1) NOT NULL default '0',
      templates_boxes int(1) NOT NULL default '0',
      invoice int(1) NOT NULL default '0',
      packingslip int(1) NOT NULL default '0',
      languages int(1) NOT NULL default '0',
      start int(1) NOT NULL default '1',
      print_order int(1) NOT NULL default '1',
      content_manager int(1) NOT NULL default '0',
      content_preview int(1) NOT NULL default '1',
      credits int(1) NOT NULL default '1',
      print_packingslip int(1) NOT NULL default '1',
      popup_image int(1) NOT NULL default '1',
      banner_statistics int(1) NOT NULL default '1',
      module_newsletter int(1) NOT NULL default '1',
      PRIMARY KEY  (customers_id)
    )";
    if (!$q->run($query)) return;

    # only for bugfix with non installed banktransfer
    # if calling orders.php at the admin-tool
    # ---------- should be deleted asap

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_banktransfer";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_banktransfer (
      orders_id int(11) NOT NULL default '0',
      banktransfer_owner varchar(64) default NULL,
      banktransfer_number varchar(24) default NULL,
      banktransfer_bankname varchar(255) default NULL,
      banktransfer_blz varchar(8) default NULL,
      banktransfer_status int(11) default NULL,
      banktransfer_prz char(2) default NULL,
      banktransfer_fax char(2) default NULL,
      KEY orders_id(orders_id)
    )";
    if (!$q->run($query)) return;

    # --------------------------------------------------

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_banners";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_banners (
      banners_id int NOT NULL auto_increment,
      banners_title varchar(64) NOT NULL,
      banners_url varchar(255) NOT NULL,
      banners_image varchar(64) NOT NULL,
      banners_group varchar(10) NOT NULL,
      banners_html_text text,
      expires_impressions int(7) DEFAULT '0',
      expires_date datetime DEFAULT NULL,
      date_scheduled datetime DEFAULT NULL,
      date_added datetime NOT NULL,
      date_status_change datetime DEFAULT NULL,
      status int(1) DEFAULT '1' NOT NULL,
      PRIMARY KEY  (banners_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_banners_history";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_banners_history (
      banners_history_id int NOT NULL auto_increment,
      banners_id int NOT NULL,
      banners_shown int(5) NOT NULL DEFAULT '0',
      banners_clicked int(5) NOT NULL DEFAULT '0',
      banners_history_date datetime NOT NULL,
      PRIMARY KEY  (banners_history_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_categories";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_categories (
      categories_id int NOT NULL auto_increment,
      categories_image varchar(64),
      parent_id int DEFAULT '0' NOT NULL,
      categories_status TINYint (1)  UNSIGNED DEFAULT '1' NOT NULL,
      sort_order int(3),
      date_added datetime,
      last_modified datetime,
      PRIMARY KEY (categories_id),
      KEY idx_categories_parent_id (parent_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_categories_description";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_categories_description (
      categories_id int DEFAULT '0' NOT NULL,
      language_id int DEFAULT '1' NOT NULL,
      categories_name varchar(32) NOT NULL,
      categories_heading_title varchar(255) NOT NULL,
      categories_description varchar(255) NOT NULL,
      categories_meta_title varchar(100) NOT NULL,
      categories_meta_description varchar(255) NOT NULL,
      categories_meta_keywords varchar(255) NOT NULL,
      PRIMARY KEY (categories_id, language_id),
      KEY idx_categories_name (categories_name)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_configuration";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_configuration (
      configuration_id int NOT NULL auto_increment,
      configuration_key varchar(64) NOT NULL,
      configuration_value varchar(255) NOT NULL,
      configuration_group_id int NOT NULL,
      sort_order int(5) NULL,
      last_modified datetime NULL,
      date_added datetime NOT NULL,
      use_function varchar(255) NULL,
      set_function varchar(255) NULL,
      PRIMARY KEY (configuration_id),
      KEY idx_configuration_group_id (configuration_group_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_configuration_group";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_configuration_group (
      configuration_group_id int NOT NULL auto_increment,
      configuration_group_title varchar(64) NOT NULL,
      configuration_group_description varchar(255) NOT NULL,
      sort_order int(5) NULL,
      visible int(1) DEFAULT '1' NULL,
      PRIMARY KEY (configuration_group_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_counter";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_counter (
      startdate char(8),
      counter int(12)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_counter_history";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_counter_history (
      month char(8),
      counter int(12)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_countries";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_countries (
      countries_id int NOT NULL auto_increment,
      countries_name varchar(64) NOT NULL,
      countries_iso_code_2 char(2) NOT NULL,
      countries_iso_code_3 char(3) NOT NULL,
      address_format_id int NOT NULL,
      PRIMARY KEY (countries_id),
      KEY IDX_COUNTRIES_NAME (countries_name)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_currencies";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_currencies (
      currencies_id int NOT NULL auto_increment,
      title varchar(32) NOT NULL,
      code char(3) NOT NULL,
      symbol_left varchar(12),
      symbol_right varchar(12),
      decimal_point char(1),
      thousands_point char(1),
      decimal_places char(1),
      value float(13,8),
      last_updated datetime NULL,
      PRIMARY KEY (currencies_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_customers";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_customers (
       customers_id int NOT NULL auto_increment,
       customers_status int(5) DEFAULT '1' NOT NULL,
       customers_gender char(1) NOT NULL,
       customers_firstname varchar(32) NOT NULL,
       customers_lastname varchar(32) NOT NULL,
       customers_dob datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
       customers_email_address varchar(96) NOT NULL,
       customers_default_address_id int NOT NULL,
       customers_telephone varchar(32) NOT NULL,
       customers_fax varchar(32),
       customers_password varchar(40) NOT NULL,
       customers_newsletter char(1),
       customers_newsletter_mode char( 1 ) DEFAULT '0' NOT NULL,
       member_flag char(1) DEFAULT '0' NOT NULL,
       delete_user char(1) DEFAULT '1' NOT NULL,
       account_type int(1) NOT NULL default '0',
       PRIMARY KEY (customers_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_customers_basket";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_customers_basket (
      customers_basket_id int NOT NULL auto_increment,
      customers_id int NOT NULL,
      products_id tinytext NOT NULL,
      customers_basket_quantity int(2) NOT NULL,
      final_price decimal(15,4) NOT NULL,
      customers_basket_date_added char(8),
      PRIMARY KEY (customers_basket_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_customers_basket_attributes";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_customers_basket_attributes (
      customers_basket_attributes_id int NOT NULL auto_increment,
      customers_id int NOT NULL,
      products_id tinytext NOT NULL,
      products_options_id int NOT NULL,
      products_options_value_id int NOT NULL,
      PRIMARY KEY (customers_basket_attributes_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_customers_info";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_customers_info (
      customers_info_id int NOT NULL,
      customers_info_date_of_last_logon datetime,
      customers_info_number_of_logons int(5),
      customers_info_date_account_created datetime,
      customers_info_date_account_last_modified datetime,
      global_product_notifications int(1) DEFAULT '0',
      PRIMARY KEY (customers_info_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_customers_ip";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_customers_ip (
      customers_ip_id int(11) NOT NULL auto_increment,
      customers_id int(11) NOT NULL default '0',
      customers_ip varchar(15) NOT NULL default '',
      customers_ip_date datetime NOT NULL default '0000-00-00 00:00:00',
      customers_host varchar(255) NOT NULL default '',
      customers_advertiser varchar(30) default NULL,
      customers_referer_url varchar(255) default NULL,
      PRIMARY KEY  (customers_ip_id),
      KEY customers_id (customers_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_customers_status";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_customers_status (
      customers_status_id int(11) NOT NULL default '0',
      language_id int(11) NOT NULL DEFAULT '1',
      customers_status_name VARCHAR(32) NOT NULL DEFAULT '',
      customers_status_public int(1) NOT NULL DEFAULT '1',
      customers_status_image varchar(64) DEFAULT NULL,
      customers_status_discount decimal(4,2) DEFAULT '0',
      customers_status_ot_discount_flag char(1) NOT NULL DEFAULT '0',
      customers_status_ot_discount decimal(4,2) DEFAULT '0',
      customers_status_graduated_prices varchar(1) NOT NULL DEFAULT '0',
      customers_status_show_price int(1) NOT NULL DEFAULT '1',
      customers_status_show_price_tax int(1) NOT NULL DEFAULT '1',
      customers_status_add_tax_ot  int(1) NOT NULL DEFAULT '0',
      customers_status_payment_unallowed varchar(255) NOT NULL,
      customers_status_shipping_unallowed varchar(255) NOT NULL,
      customers_status_discount_attributes  int(1) NOT NULL DEFAULT '0',
      PRIMARY KEY  (customers_status_id,language_id),
      KEY idx_orders_status_name (customers_status_name)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_customers_status_history";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_customers_status_history (
      customers_status_history_id int(11) NOT NULL auto_increment,
      customers_id int(11) NOT NULL default '0',
      new_value int(5) NOT NULL default '0',
      old_value int(5) default NULL,
      date_added datetime NOT NULL default '0000-00-00 00:00:00',
      customer_notified int(1) default '0',
      PRIMARY KEY  (customers_status_history_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_languages";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_languages (
      languages_id int NOT NULL auto_increment,
      name varchar(32)  NOT NULL,
      code char(2) NOT NULL,
      image varchar(64),
      directory varchar(32),
      sort_order int(3),
      language_charset text NOT NULL,
      PRIMARY KEY (languages_id),
      KEY IDX_LANGUAGES_NAME (name)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_manufacturers";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_manufacturers (
      manufacturers_id int NOT NULL auto_increment,
      manufacturers_name varchar(32) NOT NULL,
      manufacturers_image varchar(64),
      date_added datetime NULL,
      last_modified datetime NULL,
      PRIMARY KEY (manufacturers_id),
      KEY IDX_MANUFACTURERS_NAME (manufacturers_name)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_manufacturers_info";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_manufacturers_info (
      manufacturers_id int NOT NULL,
      languages_id int NOT NULL,
      manufacturers_meta_title varchar(100) NOT NULL,
      manufacturers_meta_description varchar(255) NOT NULL,
      manufacturers_meta_keywords varchar(255) NOT NULL,
      manufacturers_url varchar(255) NOT NULL,
      url_clicked int(5) NOT NULL default '0',
      date_last_click datetime NULL,
      PRIMARY KEY (manufacturers_id, languages_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_newsletters";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_newsletters (
      newsletters_id int NOT NULL auto_increment,
      title varchar(255) NOT NULL,
      content text NOT NULL,
      module varchar(255) NOT NULL,
      date_added datetime NOT NULL,
      date_sent datetime,
      status int(1),
      locked int(1) DEFAULT '0',
      PRIMARY KEY (newsletters_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_newsletters_history";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_newsletters_history (
      news_hist_id int(11) NOT NULL default '0',
      news_hist_cs int(11) NOT NULL default '0',
      news_hist_cs_date_sent date default NULL,
      PRIMARY KEY  (news_hist_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_orders";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_orders (
      orders_id int NOT NULL auto_increment,
      customers_id int NOT NULL,
      customers_status int(11),
      customers_status_name varchar(32) NOT NULL,
      customers_status_image varchar (64),
      customers_status_discount decimal (4,2),
      customers_name varchar(64) NOT NULL,
      customers_company varchar(32),
      customers_street_address varchar(64) NOT NULL,
      customers_suburb varchar(32),
      customers_city varchar(32) NOT NULL,
      customers_postcode varchar(10) NOT NULL,
      customers_state varchar(32),
      customers_country varchar(32) NOT NULL,
      customers_telephone varchar(32) NOT NULL,
      customers_email_address varchar(96) NOT NULL,
      customers_address_format_id int(5) NOT NULL,
      delivery_name varchar(64) NOT NULL,
      delivery_company varchar(32),
      delivery_street_address varchar(64) NOT NULL,
      delivery_suburb varchar(32),
      delivery_city varchar(32) NOT NULL,
      delivery_postcode varchar(10) NOT NULL,
      delivery_state varchar(32),
      delivery_country varchar(32) NOT NULL,
      delivery_address_format_id int(5) NOT NULL,
      billing_name varchar(64) NOT NULL,
      billing_company varchar(32),
      billing_street_address varchar(64) NOT NULL,
      billing_suburb varchar(32),
      billing_city varchar(32) NOT NULL,
      billing_postcode varchar(10) NOT NULL,
      billing_state varchar(32),
      billing_country varchar(32) NOT NULL,
      billing_address_format_id int(5) NOT NULL,
      payment_method varchar(32) NOT NULL,
      cc_type varchar(20),
      cc_owner varchar(64),
      cc_number varchar(32),
      cc_expires varchar(4),
      comments varchar (255),
      last_modified datetime,
      date_purchased datetime,
      orders_status int(5) NOT NULL,
      orders_date_finished datetime,
      currency char(3),
      currency_value decimal(14,6),
      account_type int(1) DEFAULT '0' NOT NULL,
      payment_class VARCHAR(32) NOT NULL,
      shipping_method VARCHAR(32) NOT NULL,
      shipping_class VARCHAR(32) NOT NULL,
      PRIMARY KEY (orders_id)
    )";
    if (!$q->run($query)) return;


    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_orders_products";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_orders_products (
      orders_products_id int NOT NULL auto_increment,
      orders_id int NOT NULL,
      products_id int NOT NULL,
      products_model varchar(12),
      products_name varchar(64) NOT NULL,
      products_price decimal(15,4) NOT NULL,
      products_discount_made decimal(4,2) DEFAULT NULL,
      final_price decimal(15,4) NOT NULL,
      products_tax decimal(7,4) NOT NULL,
      products_quantity int(2) NOT NULL,
      allow_tax int(1) NOT NULL,
      PRIMARY KEY (orders_products_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_shipping_status";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_shipping_status (
        shipping_status_id int DEFAULT '0' NOT NULL,
        language_id int DEFAULT '1' NOT NULL,
        shipping_status_name varchar(32) NOT NULL,
        shipping_status_image varchar(32) NOT NULL,
        PRIMARY KEY (shipping_status_id, language_id),
        KEY idx_shipping_status_name (shipping_status_name)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_orders_status";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_orders_status (
       orders_status_id int DEFAULT '0' NOT NULL,
       language_id int DEFAULT '1' NOT NULL,
       orders_status_name varchar(32) NOT NULL,
       PRIMARY KEY (orders_status_id, language_id),
       KEY idx_orders_status_name (orders_status_name)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_orders_status_history";
    if (!$q->run($query)) return;
     $query = "CREATE TABLE " . $prefix . "_commerce_orders_status_history (
      orders_status_history_id int NOT NULL auto_increment,
       orders_id int NOT NULL,
       orders_status_id int(5) NOT NULL,
       date_added datetime NOT NULL,
       customer_notified int(1) DEFAULT '0',
       comments text,
       PRIMARY KEY (orders_status_history_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_orders_products_attributes";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_orders_products_attributes (
      orders_products_attributes_id int NOT NULL auto_increment,
      orders_id int NOT NULL,
      orders_products_id int NOT NULL,
      products_options varchar(32) NOT NULL,
      products_options_values varchar(32) NOT NULL,
      options_values_price decimal(15,4) NOT NULL,
      price_prefix char(1) NOT NULL,
      PRIMARY KEY (orders_products_attributes_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_orders_products_download";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_orders_products_download (
      orders_products_download_id int NOT NULL auto_increment,
      orders_id int NOT NULL default '0',
      orders_products_id int NOT NULL default '0',
      orders_products_filename varchar(255) NOT NULL default '',
      download_maxdays int(2) NOT NULL default '0',
      download_count int(2) NOT NULL default '0',
      PRIMARY KEY  (orders_products_download_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_orders_total";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_orders_total (
      orders_total_id int unsigned NOT NULL auto_increment,
      orders_id int NOT NULL,
      title varchar(255) NOT NULL,
      text varchar(255) NOT NULL,
      value decimal(15,4) NOT NULL,
      class varchar(32) NOT NULL,
      sort_order int NOT NULL,
      PRIMARY KEY (orders_total_id),
      KEY idx_orders_total_orders_id (orders_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_products";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_products (
      products_id int NOT NULL auto_increment,
      products_quantity int(4) NOT NULL,
      products_model varchar(12),
      products_image varchar(64),
      products_price decimal(15,4) NOT NULL,
      products_discount_allowed decimal(3,2) DEFAULT '0' NOT NULL,
      products_date_added datetime NOT NULL,
      products_last_modified datetime,
      products_date_available datetime,
      products_weight decimal(5,2) NOT NULL,
      products_status tinyint(1) NOT NULL,
      products_tax_class_id int NOT NULL,
      product_template varchar (64),
      options_template varchar (64),
      manufacturers_id int NULL,
      products_ordered int NOT NULL default '0',
      PRIMARY KEY (products_id),
      KEY idx_products_date_added (products_date_added)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_products_attributes";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_products_attributes (
      products_attributes_id int NOT NULL auto_increment,
      products_id int NOT NULL,
      options_id int NOT NULL,
      options_values_id int NOT NULL,
      options_values_price decimal(15,4) NOT NULL,
      price_prefix char(1) NOT NULL,
      attributes_model varchar(12) NULL,
      attributes_stock int(4) NULL,
      options_values_weight decimal(15,4) NOT NULL,
      weight_prefix char(1) NOT NULL,
      PRIMARY KEY (products_attributes_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_products_attributes_download";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_products_attributes_download (
      products_attributes_id int NOT NULL,
      products_attributes_filename varchar(255) NOT NULL default '',
      products_attributes_maxdays int(2) default '0',
      products_attributes_maxcount int(2) default '0',
      PRIMARY KEY  (products_attributes_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_products_description";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_products_description (
      products_id int NOT NULL auto_increment,
      language_id int NOT NULL default '1',
      products_name varchar(64) NOT NULL default '',
      products_description text,
      products_short_description text,
      products_meta_title text NOT NULL,
      products_meta_description text NOT NULL,
      products_meta_keywords text NOT NULL,
      products_url varchar(255) default NULL,
      products_viewed int(5) default '0',
      PRIMARY KEY  (products_id,language_id),
      KEY products_name (products_name)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_products_notifications";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_products_notifications (
      products_id int NOT NULL,
      customers_id int NOT NULL,
      date_added datetime NOT NULL,
      PRIMARY KEY (products_id, customers_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_products_options";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_products_options (
      products_options_id int NOT NULL default '0',
      language_id int NOT NULL default '1',
      products_options_name varchar(32) NOT NULL default '',
      PRIMARY KEY  (products_options_id,language_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_products_options_values";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_products_options_values (
      products_options_values_id int NOT NULL default '0',
      language_id int NOT NULL default '1',
      products_options_values_name varchar(64) NOT NULL default '',
      PRIMARY KEY  (products_options_values_id,language_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_products_options_values_to_products_options";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_products_options_values_to_products_options (
      products_options_values_to_products_options_id int NOT NULL auto_increment,
      products_options_id int NOT NULL,
      products_options_values_id int NOT NULL,
      PRIMARY KEY (products_options_values_to_products_options_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_products_graduated_prices";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_products_graduated_prices (
      products_id int(11) NOT NULL default '0',
      quantity int(11) NOT NULL default '0',
      unitprice decimal(15,4) NOT NULL default '0.0000',
      KEY products_id (products_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_products_to_categories";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_products_to_categories (
      products_id int NOT NULL,
      categories_id int NOT NULL,
      PRIMARY KEY (products_id,categories_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_reviews";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_reviews (
      reviews_id int NOT NULL auto_increment,
      products_id int NOT NULL,
      customers_id int,
      customers_name varchar(64) NOT NULL,
      reviews_rating int(1),
      date_added datetime,
      last_modified datetime,
      reviews_read int(5) NOT NULL default '0',
      PRIMARY KEY (reviews_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_reviews_description";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_reviews_description (
      reviews_id int NOT NULL,
      languages_id int NOT NULL,
      reviews_text text NOT NULL,
      PRIMARY KEY (reviews_id, languages_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_sessions";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_sessions (
      sesskey varchar(32) NOT NULL,
      expiry int(11) unsigned NOT NULL,
      value text NOT NULL,
      PRIMARY KEY (sesskey)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_specials";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_specials (
      specials_id int NOT NULL auto_increment,
      products_id int NOT NULL,
      specials_new_products_price decimal(15,4) NOT NULL,
      specials_date_added datetime,
      specials_last_modified datetime,
      expires_date datetime,
      date_status_change datetime,
      status int(1) NOT NULL DEFAULT '1',
      PRIMARY KEY (specials_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_tax_class";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_tax_class (
      tax_class_id int NOT NULL auto_increment,
      tax_class_title varchar(32) NOT NULL,
      tax_class_description varchar(255) NOT NULL,
      last_modified datetime NULL,
      date_added datetime NOT NULL,
      PRIMARY KEY (tax_class_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_tax_rates";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_tax_rates (
      tax_rates_id int NOT NULL auto_increment,
      tax_zone_id int NOT NULL,
      tax_class_id int NOT NULL,
      tax_priority int(5) DEFAULT 1,
      tax_rate decimal(7,4) NOT NULL,
      tax_description varchar(255) NOT NULL,
      last_modified datetime NULL,
      date_added datetime NOT NULL,
      PRIMARY KEY (tax_rates_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_geo_zones";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_geo_zones (
      geo_zone_id int NOT NULL auto_increment,
      geo_zone_name varchar(32) NOT NULL,
      geo_zone_description varchar(255) NOT NULL,
      last_modified datetime NULL,
      date_added datetime NOT NULL,
      PRIMARY KEY (geo_zone_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_whos_online";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_whos_online (
      customer_id int,
      full_name varchar(64) NOT NULL,
      session_id varchar(128) NOT NULL,
      ip_address varchar(15) NOT NULL,
      time_entry varchar(14) NOT NULL,
      time_last_click varchar(14) NOT NULL,
      last_page_url varchar(64) NOT NULL
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_zones";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_zones (
      zone_id int NOT NULL auto_increment,
      zone_country_id int NOT NULL,
      zone_code varchar(32) NOT NULL,
      zone_name varchar(32) NOT NULL,
      PRIMARY KEY (zone_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_zones_to_geo_zones";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_zones_to_geo_zones (
       association_id int NOT NULL auto_increment,
       zone_country_id int NOT NULL,
       zone_id int NULL,
       geo_zone_id int NULL,
       last_modified datetime NULL,
       date_added datetime NOT NULL,
       PRIMARY KEY (association_id)
    )";
    if (!$q->run($query)) return;


    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_box_align";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_box_align (
      box_id int(2) NOT NULL auto_increment,
      box_name text NOT NULL,
      box_align text NOT NULL,
      box_visible int(2) NOT NULL default '0',
      box_order int(2) NOT NULL default '0',
      PRIMARY KEY  (box_id)
    )";
    if (!$q->run($query)) return;


    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_content_manager";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_content_manager (
      content_id int(11) NOT NULL auto_increment,
      categories_id int(11) NOT NULL default '0',
      parent_id int(11) NOT NULL default '0',
      languages_id int(11) NOT NULL default '0',
      content_title varchar(32) NOT NULL default '',
      content_heading varchar(32) NOT NULL default '',
      content_text text NOT NULL,
      file_flag int(1) NOT NULL default '0',
      content_file varchar(64) NOT NULL default '',
      content_status int(1) NOT NULL default '0',
      content_group int(11) NOT NULL,
      content_delete int(1) NOT NULL default '1',
      PRIMARY KEY  (content_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_media_content";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_media_content (
      file_id int(11) NOT NULL auto_increment,
      old_filename text NOT NULL,
      new_filename text NOT NULL,
      file_comment text NOT NULL,
      PRIMARY KEY  (file_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_products_content";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_products_content (
      content_id int(11) NOT NULL auto_increment,
      products_id int(11) NOT NULL default '0',
      content_name varchar(32) NOT NULL default '',
      content_file varchar(64) NOT NULL,
      content_link text NOT NULL,
      languages_id int(11) NOT NULL default '0',
      content_read int(11) NOT NULL default '0',
      file_comment text NOT NULL,
      PRIMARY KEY  (content_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_module_newsletter";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_module_newsletter (
      newsletter_id int(11) NOT NULL auto_increment,
      title text NOT NULL,
      bc text NOT NULL,
      cc text NOT NULL,
      date datetime default NULL,
      status int(1) NOT NULL default '0',
      body text NOT NULL,
      PRIMARY KEY  (newsletter_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_cm_file_flags";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_cm_file_flags (
        file_flag int(11) NOT NULL,
        file_flag_name varchar(32) NOT NULL,
        PRIMARY KEY (file_flag)
    )";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_commerce_cm_file_flags (file_flag, file_flag_name) VALUES ('0', 'information')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_cm_file_flags (file_flag, file_flag_name) VALUES ('1', 'content')";
    if (!$q->run($query)) return;


    # data

    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (1,0,0,1,'Shipping & Returns','Shipping & Returns','Put here your Shipping & Returns information.',1,'',1,1,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (2,0,0,1,'Privacy Notice','Privacy Notice','Put here your Privacy Notice information.',1,'',1,2,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (3,0,0,1,'Conditions of Use','Conditions of Use','Conditions of Use<br />Put here your Conditions of Use information. <br />1. Validity<br />2. Offers<br />3. Price<br />4. Dispatch and passage of the risk<br />5. Delivery<br />6. Terms of payment<br />7. Retention of title<br />8. Notices of defect, guarantee and compensation<br />9. Fair trading cancelling / non-acceptance<br />10. Place of delivery and area of jurisdiction<br />11. Final clauses',1,'',1,3,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (4,0,0,1,'Contact','Contact','Put here your Contact information.',1,'',1,4,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (5,0,0,1,'Index','Welcome','{\$greeting}<br><br> Dies ist die Standardinstallation des osCommerce Forking Projektes - XT-Commerce. Alle dargestellten Produkte dienen zur Demonstration der Funktionsweise. Wenn Sie Produkte bestellen, so werden diese weder ausgeliefert, noch in Rechnung gestellt. Alle Informationen zu den verschiedenen Produkten sind erfunden und daher kann kein Anspruch daraus abgeleitet werden.<br><br>Sollten Sie daran interessiert sein das Programm, welches die Grundlage für diesen Shop bildet, einzusetzen, so besuchen Sie bitte die Supportseite von XT-Commerce. Dieser Shop basiert auf der XT-Commerce Version Beta2.<br><br>Der hier dargestellte Text kann in der folgenden Datei einer jeden Sprache geändert werden: [Pfad zu catalog]/lang/catalog/[language]/index.php.<br><br>Das kann manuell geschehen, oder über das Administration Tool mit Sprache->[language]->Sprache definieren, oder durch Verwendung des Hilfsprogrammes->Datei Manager.',1,'',0,5,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (6,0,0,2,'Liefer- und Versandkosten','Liefer- und Versandkosten','Fügen Sie hier Ihre Informationen über Liefer- und Versandkosten ein.',1,'',1,1,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (7,0,0,2,'Privatsphäre und Datenschutz','Privatsphäre und Datenschutz','Fügen Sie hier Ihre Informationen über Privatsphäre und Datenschutz ein.',1,'',1,2,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (8,0,0,2,'Unsere AGB\'s','Allgemeine Geschäftsbedingungen','<strong>Allgemeine Gesch&auml;ftsbedingungen<br></strong><br>F&uuml;gen Sie hier Ihre allgemeinen Gesch&auml;ftsbedingungen ein.<br>1. Geltung<br>2. Angebote<br>3. Preis<br>4. Versand und Gefahr&uuml;bergang<br>5. Lieferung<br>6. Zahlungsbedingungen<br>7. Eigentumsvorbehalt <br>8. M&auml;ngelr&uuml;gen, Gew&auml;hrleistung und Schadenersatz<br>9. Kulanzr&uuml;cknahme / Annahmeverweigerung<br>10. Erf&uuml;llungsort und Gerichtsstand<br>11. Schlussbestimmungen',1,'',1,3,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (9,0,0,2,'Kontakt','Kontakt','Fügen Sie hier Ihre Informationen über Kontakt ein.',1,'',1,4,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (10,0,0,2,'Index','Willkommen','{\$greeting}<br><br> Dies ist die Standardinstallation des osCommerce Forking Projektes - XT-Commerce. Alle dargestellten Produkte dienen zur Demonstration der Funktionsweise. Wenn Sie Produkte bestellen, so werden diese weder ausgeliefert, noch in Rechnung gestellt. Alle Informationen zu den verschiedenen Produkten sind erfunden und daher kann kein Anspruch daraus abgeleitet werden.<br><br>Sollten Sie daran interessiert sein das Programm, welches die Grundlage für diesen Shop bildet, einzusetzen, so besuchen Sie bitte die Supportseite von XT-Commerce. Dieser Shop basiert auf der XT-Commerce Version Beta2.<br><br>Der hier dargestellte Text kann in der folgenden Datei einer jeden Sprache geändert werden: [Pfad zu catalog]/lang/catalog/[language]/index.php.<br><br>Das kann manuell geschehen, oder über das Administration Tool mit Sprache->[language]->Sprache definieren, oder durch Verwendung des Hilfsprogrammes->Datei Manager.',1,'',0,5,0)";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (1, 'loginbox.php', 'left', 1, 6)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (2, 'customers_status.php', 'left', 0, 7)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (3, 'categories.php', 'left', 1, 2)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (4, 'manufacturers.php', 'left', 0, 1)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (5, 'add_a_quickie.php', 'left', 1, 4)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (6, 'search.php', 'left', 1, 8)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (7, 'information.php', 'left', 1, 3)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (8, 'shopping_cart.php', 'right', 1, 1)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (9, 'manufacturer_info.php', 'right', 1, 6)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (10, 'order_history.php', 'right', 1, 5)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (11, 'best_sellers.php', 'right', 1, 3)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (12, 'product_notifications.php', 'right', 1, 2)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (15, 'tell_a_friend.php', 'right', 0, 4)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (16, 'specials.php', 'right', 1, 7)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (17, 'reviews.php', 'right', 1, 9)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (18, 'languages.php', 'left', 1, 5)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (19, 'currencies.php', 'right', 1, 8)";
    if (!$q->run($query)) return;


    # 1 - Default, 2 - USA, 3 - Spain, 4 - Singapore, 5 - Germany
    $query = "INSERT INTO " . $prefix . "_commerce_address_format VALUES (1, '\$firstname \$lastname\$cr\$streets\$cr\$city, \$postcode\$cr\$statecomma\$country','\$city / \$country')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_address_format VALUES (2, '\$firstname \$lastname\$cr\$streets\$cr\$city, \$state    \$postcode\$cr\$country','\$city, \$state / \$country')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_address_format VALUES (3, '\$firstname \$lastname\$cr\$streets\$cr\$city\$cr\$postcode - \$statecomma\$country','\$state / \$country')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_address_format VALUES (4, '\$firstname \$lastname\$cr\$streets\$cr\$city (\$postcode)\$cr\$country', '\$postcode / \$country')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_address_format VALUES (5, '\$firstname \$lastname\$cr\$streets\$cr\$postcode \$city\$cr\$country','\$city / \$country')";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_commerce_admin_access VALUES (1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1)";
    if (!$q->run($query)) return;




    # configuration_group_id 1
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STORE_NAME', 'XT-Commerce',  1, 1, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STORE_OWNER', 'XT-Commerce', 1, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STORE_OWNER_EMAIL_ADDRESS', 'owner@your-shop.com', 1, 3, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_FROM', 'XT-Commerce <owner@your-shop.com>',  1, 4, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STORE_COUNTRY', '81',  1, 6, NULL, '', 'commerce_userapi_get_country_name', 'commerce_adminapi_pull_down_country_list(')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STORE_ZONE', '', 1, 7, NULL, '', 'commerce_adminapi_get_zone_name', 'commerce_adminapi_pull_down_zone_list(')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EXPECTED_PRODUCTS_SORT', 'desc',  1, 8, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'asc\', \'desc\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EXPECTED_PRODUCTS_FIELD', 'date_expected',  1, 9, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'products_name\', \'date_expected\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'USE_DEFAULT_LANGUAGE_CURRENCY', 'false', 1, 10, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SEARCH_ENGINE_FRIENDLY_URLS', 'false',  16, 12, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DISPLAY_CART', 'true',  1, 13, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ALLOW_GUEST_TO_TELL_A_FRIEND', 'false', 1, 14, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ADVANCED_SEARCH_DEFAULT_OPERATOR', 'and', 1, 15, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'and\', \'or\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STORE_NAME_ADDRESS', 'Store Name\nAddress\nCountry\nPhone',  1, 16, NULL, '', NULL, 'commerce_adminapi_textarea(')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SHOW_COUNTS', 'true',  1, 17, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DEFAULT_CUSTOMERS_STATUS_ID_ADMIN', '0',  1, 20, NULL, '', 'commerce_userapi_get_customers_status_name', 'commerce_adminapi_pull_down_customers_status_list(')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DEFAULT_CUSTOMERS_STATUS_ID_GUEST', '1',  1, 21, NULL, '', 'commerce_userapi_get_customers_status_name', 'commerce_adminapi_pull_down_customers_status_list(')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DEFAULT_CUSTOMERS_STATUS_ID', '2',  1, 23, NULL, '', 'commerce_userapi_get_customers_status_name', 'commerce_adminapi_pull_down_customers_status_list(')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ALLOW_ADD_TO_CART', 'false',  1, 24, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ALLOW_CATEGORY_DESCRIPTIONS', 'true', 1, 25, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CURRENT_TEMPLATE', 'xtc', 1, 26, NULL, '', NULL, 'commerce_adminapi_pull_down_template_sets(')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'PRICE_IS_BRUTTO', 'false', 1, 27, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'PRICE_PRECISION', '2', 1, 28, NULL, '', NULL, '')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'USE_SPAW', 'true', 1, 29, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;

    # configuration_group_id 2
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ENTRY_FIRST_NAME_MIN_LENGTH', '2',  2, 1, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ENTRY_LAST_NAME_MIN_LENGTH', '2',  2, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ENTRY_DOB_MIN_LENGTH', '10',  2, 3, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ENTRY_EMAIL_ADDRESS_MIN_LENGTH', '6',  2, 4, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ENTRY_STREET_ADDRESS_MIN_LENGTH', '5',  2, 5, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ENTRY_COMPANY_MIN_LENGTH', '2',  2, 6, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ENTRY_POSTCODE_MIN_LENGTH', '4',  2, 7, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ENTRY_CITY_MIN_LENGTH', '3',  2, 8, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ENTRY_STATE_MIN_LENGTH', '2', 2, 9, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ENTRY_TELEPHONE_MIN_LENGTH', '3',  2, 10, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ENTRY_PASSWORD_MIN_LENGTH', '5',  2, 11, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CC_OWNER_MIN_LENGTH', '3',  2, 12, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CC_NUMBER_MIN_LENGTH', '10',  2, 13, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'REVIEW_TEXT_MIN_LENGTH', '50',  2, 14, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MIN_DISPLAY_BESTSELLERS', '1',  2, 15, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MIN_DISPLAY_ALSO_PURCHASED', '1', 2, 16, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;

    # configuration_group_id 3
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_ADDRESS_BOOK_ENTRIES', '5',  3, 1, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_SEARCH_RESULTS', '20',  3, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_PAGE_LINKS', '5',  3, 3, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_SPECIAL_PRODUCTS', '9', 3, 4, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_NEW_PRODUCTS', '9',  3, 5, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_UPCOMING_PRODUCTS', '10',  3, 6, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_MANUFACTURERS_IN_A_LIST', '0', 3, 7, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_MANUFACTURERS_LIST', '1',  3, 7, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_MANUFACTURER_NAME_LEN', '15',  3, 8, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_NEW_REVIEWS', '6', 3, 9, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_RANDOM_SELECT_REVIEWS', '10',  3, 10, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_RANDOM_SELECT_NEW', '10',  3, 11, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_RANDOM_SELECT_SPECIALS', '10',  3, 12, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_CATEGORIES_PER_ROW', '3',  3, 13, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_PRODUCTS_NEW', '10',  3, 14, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_BESTSELLERS', '10',  3, 15, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_ALSO_PURCHASED', '6',  3, 16, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX', '6',  3, 17, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MAX_DISPLAY_ORDER_HISTORY', '10',  3, 18, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'PRODUCT_REVIEWS_VIEW', '5',  3, 19, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;

    # configuration_group_id 4
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'CONFIG_CALCULATE_IMAGE_SIZE', 'true', 4, 1, NULL, '0000-00-00 00:00:00', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'IMAGE_REQUIRED', 'true', 4, 2, NULL, '0000-00-00 00:00:00', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'HEADING_IMAGE_WIDTH', '70', 4, 3, NULL, '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'HEADING_IMAGE_HEIGHT', '50', 4, 4, NULL, '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'SUBCATEGORY_IMAGE_WIDTH', '100', 4, 5, NULL, '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'SUBCATEGORY_IMAGE_HEIGHT', '57', 4, 6, NULL, '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_THUMBNAIL_WIDTH', '120', 4, 7, '2003-12-15 12:10:45', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_THUMBNAIL_HEIGHT', '80', 4, 8, NULL, '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_INFO_WIDTH', '200', 4, 9, NULL, '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_INFO_HEIGHT', '160', 4, 10, NULL, '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_POPUP_WIDTH', '300', 4, 11, '2003-12-15 12:11:00', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_POPUP_HEIGHT', '240', 4, 12, '2003-12-15 12:11:09', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_THUMBNAIL_BEVEL', '', 4, 13, '2003-12-15 13:14:39', '0000-00-00 00:00:00', '', '')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_THUMBNAIL_GREYSCALE', '', 4, 14, '2003-12-15 13:13:37', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_THUMBNAIL_ELLIPSE', '', 4, 15, '2003-12-15 13:14:57', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_THUMBNAIL_ROUND_EDGES', '', 4, 16, '2003-12-15 13:19:45', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_THUMBNAIL_MERGE', '', 4, 17, '2003-12-15 12:01:43', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_THUMBNAIL_FRAME', '(FFFFFF,000000,3,EEEEEE)', 4, 18, '2003-12-15 13:19:37', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_THUMBNAIL_DROP_SHADDOW', '', 4, 19, '2003-12-15 13:15:14', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_THUMBNAIL_MOTION_BLUR', '(4,FFFFFF)', 4, 20, '2003-12-15 12:02:19', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_INFO_BEVEL', '', 4, 21, '2003-12-15 13:42:09', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_INFO_GREYSCALE', '', 4, 22, '2003-12-15 13:18:00', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_INFO_ELLIPSE', '', 4, 23, '2003-12-15 13:41:53', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_INFO_ROUND_EDGES', '', 4, 24, '2003-12-15 13:21:55', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_INFO_MERGE', '(overlay.gif,10,-50,60,FF0000)', 4, 25, NULL, '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_INFO_FRAME', '(FFFFFF,000000,3,EEEEEE)', 4, 26, NULL, '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_INFO_DROP_SHADDOW', '(3,333333,FFFFFF)', 4, 27, NULL, '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_INFO_MOTION_BLUR', '', 4, 28, '2003-12-15 13:21:18', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_POPUP_BEVEL', '(8,FFCCCC,330000)', 4, 29, NULL, '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_POPUP_GREYSCALE', '', 4, 30, '2003-12-15 13:22:58', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_POPUP_ELLIPSE', '', 4, 31, '2003-12-15 13:22:51', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_POPUP_ROUND_EDGES', '', 4, 32, '2003-12-15 13:23:17', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_POPUP_MERGE', '(overlay.gif,10,-50,60,FF0000)', 4, 33, NULL, '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_POPUP_FRAME', '', 4, 34, '2003-12-15 13:22:43', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_POPUP_DROP_SHADDOW', '', 4, 35, '2003-12-15 13:22:26', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_POPUP_MOTION_BLUR', '', 4, 36, '2003-12-15 13:22:32', '0000-00-00 00:00:00', NULL, NULL)";
    if (!$q->run($query)) return;

    # configuration_group_id 5
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ACCOUNT_GENDER', 'true',  5, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ACCOUNT_DOB', 'true',  5, 2, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ACCOUNT_COMPANY', 'true',  5, 3, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ACCOUNT_SUBURB', 'true', 5, 4, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ACCOUNT_STATE', 'true',  5, 5, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ACCOUNT_OPTIONS', 'account',  5, 6, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'account\', \'guest\', \'both\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DELETE_GUEST_ACCOUNT', 'true',  5, 7, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;

    # configuration_group_id 6
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_PAYMENT_INSTALLED', '', 6, 0, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_INSTALLED', 'ot_subtotal.php;ot_shipping.php;ot_tax.php;ot_total.php', 6, 0, '2003-07-18 03:31:55', '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_SHIPPING_INSTALLED', '',  6, 0, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DEFAULT_CURRENCY', 'EUR',  6, 0, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DEFAULT_LANGUAGE', 'de',  6, 0, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DEFAULT_ORDERS_STATUS_ID', '1',  6, 0, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'true',  6, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER', '3',  6, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING', 'false', 6, 3, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER', '50',  6, 4, NULL, '', 'currencies->format', NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_SHIPPING_DESTINATION', 'national', 6, 5, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'national\', \'international\', \'both\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_SUBTOTAL_STATUS', 'true',  6, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER', '1',  6, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_TAX_STATUS', 'true',  6, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_TAX_SORT_ORDER', '5',  6, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_TOTAL_STATUS', 'true',  6, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER', '6',  6, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_DISCOUNT_STATUS', 'true',  6, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER', '2', 6, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS', 'true',  6, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_SORT_ORDER','4',  6, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;


    # configuration_group_id 7
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SHIPPING_ORIGIN_COUNTRY', '81',  7, 1, NULL, '', 'commerce_userapi_get_country_name', 'commerce_adminapi_pull_down_country_list(')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SHIPPING_ORIGIN_ZIP', '',  7, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SHIPPING_MAX_WEIGHT', '50',  7, 3, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SHIPPING_BOX_WEIGHT', '3',  7, 4, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SHIPPING_BOX_PADDING', '10',  7, 5, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;

    # configuration_group_id 8
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'PRODUCT_LIST_FILTER', '1', 8, 1, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;

    # configuration_group_id 9
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STOCK_CHECK', 'true',  9, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ATTRIBUTE_STOCK_CHECK', 'true',  9, 2, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STOCK_LIMITED', 'true', 9, 3, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STOCK_ALLOW_CHECKOUT', 'true',  9, 4, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STOCK_MARK_PRODUCT_OUT_OF_STOCK', '***',  9, 5, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STOCK_REORDER_LEVEL', '5',  9, 6, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;

    # configuration_group_id 10
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STORE_PAGE_PARSE_TIME', 'false',  10, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STORE_PAGE_PARSE_TIME_LOG', '/var/log/www/tep/page_parse_time.log',  10, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STORE_PARSE_DATE_TIME_FORMAT', '%d/%m/%Y %H:%M:%S', 10, 3, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DISPLAY_PAGE_PARSE_TIME', 'true',  10, 4, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STORE_DB_TRANSACTIONS', 'false',  10, 5, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;

    # configuration_group_id 11
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'USE_CACHE', 'false',  11, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DIR_FS_CACHE', 'cache',  11, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CACHE_LIFETIME', '3600',  11, 3, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CACHE_CHECK', 'true',  11, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;

    # configuration_group_id 12
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_TRANSPORT', 'sendmail',  12, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'sendmail\', \'smtp\', \'mail\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SENDMAIL_PATH', '/usr/sbin/sendmail', 12, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SMTP_MAIN_SERVER', 'localhost', 12, 3, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SMTP_Backup_Server', 'localhost', 12, 4, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SMTP_PORT', '25', 12, 5, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SMTP_USERNAME', 'Please Enter', 12, 6, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SMTP_PASSWORD', 'Please Enter', 12, 7, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SMTP_AUTH', 'false', 12, 8, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_LINEFEED', 'LF',  12, 9, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'LF\', \'CRLF\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_USE_HTML', 'false',  12, 10, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ENTRY_EMAIL_ADDRESS_CHECK', 'false',  12, 11, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SEND_EMAILS', 'true',  12, 12, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;

    # Constants for contact_us
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CONTACT_US_EMAIL_ADDRESS', 'contact@your-shop.com', 12, 20, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CONTACT_US_NAME', 'Mail send by Contact_us Form',  12, 21, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CONTACT_US_REPLY_ADDRESS',  '', 12, 22, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CONTACT_US_REPLY_ADDRESS_NAME',  '', 12, 23, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CONTACT_US_EMAIL_SUBJECT',  '', 12, 24, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CONTACT_US_FORWARDING_STRING',  '', 12, 25, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;

    # Constants for support system
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_SUPPORT_ADDRESS', 'support@your-shop.com', 12, 26, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_SUPPORT_NAME', 'Mail send by support systems',  12, 27, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_SUPPORT_REPLY_ADDRESS',  '', 12, 28, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_SUPPORT_REPLY_ADDRESS_NAME',  '', 12, 29, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_SUPPORT_SUBJECT',  '', 12, 30, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_SUPPORT_FORWARDING_STRING',  '', 12, 31, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;

    # Constants for billing system
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_BILLING_ADDRESS', 'billing@your-shop.com', 12, 32, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_BILLING_NAME', 'Mail send by billing systems',  12, 33, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_BILLING_REPLY_ADDRESS',  '', 12, 34, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_BILLING_REPLY_ADDRESS_NAME',  '', 12, 35, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_BILLING_SUBJECT',  '', 12, 36, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_BILLING_FORWARDING_STRING',  '', 12, 37, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'EMAIL_BILLING_SUBJECT_ORDER',  'Your order Nr:{\$nr} / {\$date}', 12, 38, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;

    # configuration_group_id 13
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DOWNLOAD_ENABLED', 'false',  13, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DOWNLOAD_BY_REDIRECT', 'false',  13, 2, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DOWNLOAD_MAX_DAYS', '7',  13, 3, NULL, '', NULL, '')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DOWNLOAD_MAX_COUNT', '5',  13, 4, NULL, '', NULL, '')";
    if (!$q->run($query)) return;

    # configuration_group_id 14
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'GZIP_COMPRESSION', 'false',  14, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'GZIP_LEVEL', '5',  14, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;

    # configuration_group_id 15
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SESSION_WRITE_DIRECTORY', '/tmp',  15, 1, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SESSION_FORCE_COOKIE_USE', 'False',  15, 2, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'True\', \'False\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SESSION_CHECK_SSL_SESSION_ID', 'False',  15, 3, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'True\', \'False\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SESSION_CHECK_USER_AGENT', 'False',  15, 4, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'True\', \'False\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SESSION_CHECK_IP_ADDRESS', 'False',  15, 5, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'True\', \'False\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SESSION_BLOCK_SPIDERS', 'False',  15, 6, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'True\', \'False\'),')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SESSION_RECREATE', 'False',  15, 7, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'True\', \'False\'),')";
    if (!$q->run($query)) return;

    # configuration_group_id 16
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DISPLAY_CONDITIONS_ON_CHECKOUT', 'true',16, 1, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'META_MIN_KEYWORD_LENGTH', '6', 16, 2, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'META_KEYWORDS_NUMBER', '5',  16, 3, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'META_AUTHOR', '',  16, 4, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'META_PUBLISHER', '',  16, 5, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'META_COMPANY', '',  16, 6, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'META_TOPIC', 'shopping',  16, 7, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'META_REPLY_TO', 'xx@xx.com',  16, 8, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'META_REVISIT_AFTER', '14',  16, 9, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'META_ROBOTS', 'index,follow',  16, 10, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'META_DESCRIPTION', '',  16, 11, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'META_KEYWORDS', '',  16, 12, NULL, '', NULL, NULL)";
    if (!$q->run($query)) return;

    # configuration_group_id 17
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'USE_SPAW', 'true', 17, 1, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ACTIVATE_GIFT_SYSTEM', 'false', 17, 2, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SECURITY_CODE_LENGTH', '10', 17, 3, NULL, '2003-12-05 05:01:41', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'NEW_SIGNUP_GIFT_VOUCHER_AMOUNT', '0', 17, 4, NULL, '2003-12-05 05:01:41', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'NEW_SIGNUP_DISCOUNT_COUPON', '', 17, 5, NULL, '2003-12-05 05:01:41', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ACTIVATE_SHIPPING_STATUS', 'true', 17, 6, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DISPLAY_CONDITIONS_ON_CHECKOUT', 'true',17, 7, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CHECK_CLIENT_AGENT', 'false',17, 7, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SHOW_IP_LOG', 'false',17, 8, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'GROUP_CHECK', 'false',  17, 9, NULL, '', NULL, 'commerce_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('1', 'My Store', 'General information about my store', '1', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('2', 'Minimum Values', 'The minimum values for functions / data', '2', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('3', 'Maximum Values', 'The maximum values for functions / data', '3', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('4', 'Images', 'Image parameters', '4', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('5', 'Customer Details', 'Customer account configuration', '5', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('6', 'Module Options', 'Hidden from configuration', '6', '0')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('7', 'Shipping/Packaging', 'Shipping options available at my store', '7', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('8', 'Product Listing', 'Product Listing    configuration options', '8', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('9', 'Stock', 'Stock configuration options', '9', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('10', 'Logging', 'Logging configuration options', '10', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('11', 'Cache', 'Caching configuration options', '11', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('12', 'E-Mail Options', 'General setting for E-Mail transport and HTML E-Mails', '12', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('13', 'Download', 'Downloadable products options', '13', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('14', 'GZip Compression', 'GZip compression options', '14', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('15', 'Sessions', 'Session options', '15', '1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration_group VALUES ('16', 'Meta-Tags/Search engines', 'Meta-tags/Search engines', '16', '1')";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (1,'Afghanistan','AF','AFG','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (2,'Albania','AL','ALB','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (3,'Algeria','DZ','DZA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (4,'American Samoa','AS','ASM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (5,'Andorra','AD','AND','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (6,'Angola','AO','AGO','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (7,'Anguilla','AI','AIA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (8,'Antarctica','AQ','ATA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (9,'Antigua and Barbuda','AG','ATG','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (10,'Argentina','AR','ARG','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (11,'Armenia','AM','ARM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (12,'Aruba','AW','ABW','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (13,'Australia','AU','AUS','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (14,'Austria','AT','AUT','5')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (15,'Azerbaijan','AZ','AZE','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (16,'Bahamas','BS','BHS','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (17,'Bahrain','BH','BHR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (18,'Bangladesh','BD','BGD','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (19,'Barbados','BB','BRB','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (20,'Belarus','BY','BLR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (21,'Belgium','BE','BEL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (22,'Belize','BZ','BLZ','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (23,'Benin','BJ','BEN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (24,'Bermuda','BM','BMU','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (25,'Bhutan','BT','BTN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (26,'Bolivia','BO','BOL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (27,'Bosnia and Herzegowina','BA','BIH','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (28,'Botswana','BW','BWA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (29,'Bouvet Island','BV','BVT','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (30,'Brazil','BR','BRA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (31,'British Indian Ocean Territory','IO','IOT','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (32,'Brunei Darussalam','BN','BRN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (33,'Bulgaria','BG','BGR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (34,'Burkina Faso','BF','BFA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (35,'Burundi','BI','BDI','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (36,'Cambodia','KH','KHM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (37,'Cameroon','CM','CMR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (38,'Canada','CA','CAN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (39,'Cape Verde','CV','CPV','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (40,'Cayman Islands','KY','CYM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (41,'Central African Republic','CF','CAF','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (42,'Chad','TD','TCD','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (43,'Chile','CL','CHL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (44,'China','CN','CHN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (45,'Christmas Island','CX','CXR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (46,'Cocos (Keeling) Islands','CC','CCK','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (47,'Colombia','CO','COL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (48,'Comoros','KM','COM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (49,'Congo','CG','COG','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (50,'Cook Islands','CK','COK','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (51,'Costa Rica','CR','CRI','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (52,'Cote D\'Ivoire','CI','CIV','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (53,'Croatia','HR','HRV','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (54,'Cuba','CU','CUB','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (55,'Cyprus','CY','CYP','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (56,'Czech Republic','CZ','CZE','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (57,'Denmark','DK','DNK','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (58,'Djibouti','DJ','DJI','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (59,'Dominica','DM','DMA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (60,'Dominican Republic','DO','DOM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (61,'East Timor','TP','TMP','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (62,'Ecuador','EC','ECU','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (63,'Egypt','EG','EGY','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (64,'El Salvador','SV','SLV','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (65,'Equatorial Guinea','GQ','GNQ','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (66,'Eritrea','ER','ERI','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (67,'Estonia','EE','EST','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (68,'Ethiopia','ET','ETH','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (69,'Falkland Islands (Malvinas)','FK','FLK','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (70,'Faroe Islands','FO','FRO','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (71,'Fiji','FJ','FJI','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (72,'Finland','FI','FIN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (73,'France','FR','FRA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (74,'France, Metropolitan','FX','FXX','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (75,'French Guiana','GF','GUF','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (76,'French Polynesia','PF','PYF','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (77,'French Southern Territories','TF','ATF','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (78,'Gabon','GA','GAB','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (79,'Gambia','GM','GMB','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (80,'Georgia','GE','GEO','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (81,'Germany','DE','DEU','5')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (82,'Ghana','GH','GHA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (83,'Gibraltar','GI','GIB','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (84,'Greece','GR','GRC','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (85,'Greenland','GL','GRL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (86,'Grenada','GD','GRD','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (87,'Guadeloupe','GP','GLP','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (88,'Guam','GU','GUM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (89,'Guatemala','GT','GTM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (90,'Guinea','GN','GIN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (91,'Guinea-bissau','GW','GNB','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (92,'Guyana','GY','GUY','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (93,'Haiti','HT','HTI','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (94,'Heard and Mc Donald Islands','HM','HMD','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (95,'Honduras','HN','HND','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (96,'Hong Kong','HK','HKG','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (97,'Hungary','HU','HUN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (98,'Iceland','IS','ISL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (99,'India','IN','IND','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (100,'Indonesia','ID','IDN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (101,'Iran (Islamic Republic of)','IR','IRN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (102,'Iraq','IQ','IRQ','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (103,'Ireland','IE','IRL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (104,'Israel','IL','ISR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (105,'Italy','IT','ITA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (106,'Jamaica','JM','JAM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (107,'Japan','JP','JPN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (108,'Jordan','JO','JOR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (109,'Kazakhstan','KZ','KAZ','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (110,'Kenya','KE','KEN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (111,'Kiribati','KI','KIR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (112,'Korea, Democratic People\'s Republic of','KP','PRK','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (113,'Korea, Republic of','KR','KOR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (114,'Kuwait','KW','KWT','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (115,'Kyrgyzstan','KG','KGZ','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (116,'Lao People\'s Democratic Republic','LA','LAO','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (117,'Latvia','LV','LVA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (118,'Lebanon','LB','LBN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (119,'Lesotho','LS','LSO','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (120,'Liberia','LR','LBR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (121,'Libyan Arab Jamahiriya','LY','LBY','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (122,'Liechtenstein','LI','LIE','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (123,'Lithuania','LT','LTU','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (124,'Luxembourg','LU','LUX','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (125,'Macau','MO','MAC','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (126,'Macedonia, The Former Yugoslav Republic of','MK','MKD','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (127,'Madagascar','MG','MDG','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (128,'Malawi','MW','MWI','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (129,'Malaysia','MY','MYS','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (130,'Maldives','MV','MDV','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (131,'Mali','ML','MLI','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (132,'Malta','MT','MLT','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (133,'Marshall Islands','MH','MHL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (134,'Martinique','MQ','MTQ','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (135,'Mauritania','MR','MRT','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (136,'Mauritius','MU','MUS','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (137,'Mayotte','YT','MYT','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (138,'Mexico','MX','MEX','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (139,'Micronesia, Federated States of','FM','FSM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (140,'Moldova, Republic of','MD','MDA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (141,'Monaco','MC','MCO','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (142,'Mongolia','MN','MNG','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (143,'Montserrat','MS','MSR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (144,'Morocco','MA','MAR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (145,'Mozambique','MZ','MOZ','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (146,'Myanmar','MM','MMR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (147,'Namibia','NA','NAM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (148,'Nauru','NR','NRU','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (149,'Nepal','NP','NPL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (150,'Netherlands','NL','NLD','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (151,'Netherlands Antilles','AN','ANT','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (152,'New Caledonia','NC','NCL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (153,'New Zealand','NZ','NZL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (154,'Nicaragua','NI','NIC','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (155,'Niger','NE','NER','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (156,'Nigeria','NG','NGA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (157,'Niue','NU','NIU','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (158,'Norfolk Island','NF','NFK','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (159,'Northern Mariana Islands','MP','MNP','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (160,'Norway','NO','NOR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (161,'Oman','OM','OMN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (162,'Pakistan','PK','PAK','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (163,'Palau','PW','PLW','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (164,'Panama','PA','PAN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (165,'Papua New Guinea','PG','PNG','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (166,'Paraguay','PY','PRY','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (167,'Peru','PE','PER','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (168,'Philippines','PH','PHL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (169,'Pitcairn','PN','PCN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (170,'Poland','PL','POL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (171,'Portugal','PT','PRT','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (172,'Puerto Rico','PR','PRI','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (173,'Qatar','QA','QAT','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (174,'Reunion','RE','REU','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (175,'Romania','RO','ROM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (176,'Russian Federation','RU','RUS','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (177,'Rwanda','RW','RWA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (178,'Saint Kitts and Nevis','KN','KNA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (179,'Saint Lucia','LC','LCA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (180,'Saint Vincent and the Grenadines','VC','VCT','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (181,'Samoa','WS','WSM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (182,'San Marino','SM','SMR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (183,'Sao Tome and Principe','ST','STP','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (184,'Saudi Arabia','SA','SAU','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (185,'Senegal','SN','SEN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (186,'Seychelles','SC','SYC','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (187,'Sierra Leone','SL','SLE','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (188,'Singapore','SG','SGP', '4')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (189,'Slovakia (Slovak Republic)','SK','SVK','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (190,'Slovenia','SI','SVN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (191,'Solomon Islands','SB','SLB','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (192,'Somalia','SO','SOM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (193,'South Africa','ZA','ZAF','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (194,'South Georgia and the South Sandwich Islands','GS','SGS','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (195,'Spain','ES','ESP','3')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (196,'Sri Lanka','LK','LKA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (197,'St. Helena','SH','SHN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (198,'St. Pierre and Miquelon','PM','SPM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (199,'Sudan','SD','SDN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (200,'Suriname','SR','SUR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (201,'Svalbard and Jan Mayen Islands','SJ','SJM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (202,'Swaziland','SZ','SWZ','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (203,'Sweden','SE','SWE','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (204,'Switzerland','CH','CHE','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (205,'Syrian Arab Republic','SY','SYR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (206,'Taiwan','TW','TWN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (207,'Tajikistan','TJ','TJK','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (208,'Tanzania, United Republic of','TZ','TZA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (209,'Thailand','TH','THA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (210,'Togo','TG','TGO','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (211,'Tokelau','TK','TKL','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (212,'Tonga','TO','TON','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (213,'Trinidad and Tobago','TT','TTO','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (214,'Tunisia','TN','TUN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (215,'Turkey','TR','TUR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (216,'Turkmenistan','TM','TKM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (217,'Turks and Caicos Islands','TC','TCA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (218,'Tuvalu','TV','TUV','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (219,'Uganda','UG','UGA','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (220,'Ukraine','UA','UKR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (221,'United Arab Emirates','AE','ARE','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (222,'United Kingdom','GB','GBR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (223,'United States','US','USA', '2')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (224,'United States Minor Outlying Islands','UM','UMI','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (225,'Uruguay','UY','URY','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (226,'Uzbekistan','UZ','UZB','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (227,'Vanuatu','VU','VUT','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (228,'Vatican City State (Holy See)','VA','VAT','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (229,'Venezuela','VE','VEN','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (230,'Viet Nam','VN','VNM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (231,'Virgin Islands (British)','VG','VGB','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (232,'Virgin Islands (U.S.)','VI','VIR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (233,'Wallis and Futuna Islands','WF','WLF','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (234,'Western Sahara','EH','ESH','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (235,'Yemen','YE','YEM','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (236,'Yugoslavia','YU','YUG','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (237,'Zaire','ZR','ZAR','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (238,'Zambia','ZM','ZMB','1')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_countries VALUES (239,'Zimbabwe','ZW','ZWE','1')";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_commerce_currencies VALUES (1,'Euro','EUR','','EUR','.',',','2','1.0000', now())";
    if (!$q->run($query)) return;


    $query = "INSERT INTO " . $prefix . "_commerce_languages VALUES (1,'English','en','icon.gif','en_US',1,'iso-8859-15')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_languages VALUES (2,'Deutsch','de','icon.gif','de_DE',2,'iso-8859-15')";
    if (!$q->run($query)) return;


    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '1', '1', 'Pending')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '1', '2', 'Offen')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '2', '1', 'Processing')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '2', '2', 'In Bearbeitung')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '3', '1', 'Delivered')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '3', '2', 'Versendet')";
    if (!$q->run($query)) return;


    # USA
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (1,223,'AL','Alabama')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (2,223,'AK','Alaska')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (3,223,'AS','American Samoa')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (4,223,'AZ','Arizona')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (5,223,'AR','Arkansas')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (6,223,'AF','Armed Forces Africa')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (7,223,'AA','Armed Forces Americas')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (8,223,'AC','Armed Forces Canada')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (9,223,'AE','Armed Forces Europe')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (10,223,'AM','Armed Forces Middle East')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (11,223,'AP','Armed Forces Pacific')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (12,223,'CA','California')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (13,223,'CO','Colorado')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (14,223,'CT','Connecticut')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (15,223,'DE','Delaware')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (16,223,'DC','District of Columbia')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (17,223,'FM','Federated States Of Micronesia')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (18,223,'FL','Florida')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (19,223,'GA','Georgia')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (20,223,'GU','Guam')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (21,223,'HI','Hawaii')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (22,223,'ID','Idaho')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (23,223,'IL','Illinois')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (24,223,'IN','Indiana')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (25,223,'IA','Iowa')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (26,223,'KS','Kansas')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (27,223,'KY','Kentucky')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (28,223,'LA','Louisiana')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (29,223,'ME','Maine')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (30,223,'MH','Marshall Islands')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (31,223,'MD','Maryland')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (32,223,'MA','Massachusetts')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (33,223,'MI','Michigan')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (34,223,'MN','Minnesota')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (35,223,'MS','Mississippi')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (36,223,'MO','Missouri')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (37,223,'MT','Montana')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (38,223,'NE','Nebraska')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (39,223,'NV','Nevada')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (40,223,'NH','New Hampshire')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (41,223,'NJ','New Jersey')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (42,223,'NM','New Mexico')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (43,223,'NY','New York')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (44,223,'NC','North Carolina')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (45,223,'ND','North Dakota')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (46,223,'MP','Northern Mariana Islands')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (47,223,'OH','Ohio')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (48,223,'OK','Oklahoma')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (49,223,'OR','Oregon')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (50,223,'PW','Palau')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (51,223,'PA','Pennsylvania')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (52,223,'PR','Puerto Rico')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (53,223,'RI','Rhode Island')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (54,223,'SC','South Carolina')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (55,223,'SD','South Dakota')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (56,223,'TN','Tennessee')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (57,223,'TX','Texas')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (58,223,'UT','Utah')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (59,223,'VT','Vermont')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (60,223,'VI','Virgin Islands')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (61,223,'VA','Virginia')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (62,223,'WA','Washington')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (63,223,'WV','West Virginia')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (64,223,'WI','Wisconsin')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (65,223,'WY','Wyoming')";
    if (!$q->run($query)) return;

    # Canada
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (66,38,'AB','Alberta')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (67,38,'BC','British Columbia')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (68,38,'MB','Manitoba')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (69,38,'NF','Newfoundland')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (70,38,'NB','New Brunswick')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (71,38,'NS','Nova Scotia')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (72,38,'NT','Northwest Territories')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (73,38,'NU','Nunavut')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (74,38,'ON','Ontario')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (75,38,'PE','Prince Edward Island')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (76,38,'QC','Quebec')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (77,38,'SK','Saskatchewan')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (78,38,'YT','Yukon Territory')";
    if (!$q->run($query)) return;

    # Germany
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (79,81,'NDS','Niedersachsen')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (80,81,'BAW','Baden-Württemberg')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (81,81,'BAY','Bayern')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (82,81,'BER','Berlin')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (83,81,'BRG','Brandenburg')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (84,81,'BRE','Bremen')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (85,81,'HAM','Hamburg')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (86,81,'HES','Hessen')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (87,81,'MEC','Mecklenburg-Vorpommern')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (88,81,'NRW','Nordrhein-Westfalen')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (89,81,'RHE','Rheinland-Pfalz')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (90,81,'SAR','Saarland')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (91,81,'SAS','Sachsen')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (92,81,'SAC','Sachsen-Anhalt')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (93,81,'SCN','Schleswig-Holstein')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (94,81,'THE','Thüringen')";
    if (!$q->run($query)) return;

    # Austria
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (95,14,'WI','Wien')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (96,14,'NO','Niederösterreich')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (97,14,'OO','Oberösterreich')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (98,14,'SB','Salzburg')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (99,14,'KN','Kärnten')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (100,14,'ST','Steiermark')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (101,14,'TI','Tirol')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (102,14,'BL','Burgenland')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (103,14,'VB','Voralberg')";
    if (!$q->run($query)) return;

    # Swizterland
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (104,204,'AG','Aargau')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (105,204,'AI','Appenzell Innerrhoden')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (106,204,'AR','Appenzell Ausserrhoden')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (107,204,'BE','Bern')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (108,204,'BL','Basel-Landschaft')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (109,204,'BS','Basel-Stadt')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (110,204,'FR','Freiburg')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (111,204,'GE','Genf')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (112,204,'GL','Glarus')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (113,204,'JU','Graubünden')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (114,204,'JU','Jura')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (115,204,'LU','Luzern')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (116,204,'NE','Neuenburg')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (117,204,'NW','Nidwalden')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (118,204,'OW','Obwalden')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (119,204,'SG','St. Gallen')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (120,204,'SH','Schaffhausen')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (121,204,'SO','Solothurn')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (122,204,'SZ','Schwyz')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (123,204,'TG','Thurgau')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (124,204,'TI','Tessin')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (125,204,'UR','Uri')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (126,204,'VD','Waadt')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (127,204,'VS','Wallis')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (128,204,'ZG','Zug')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones VALUES (129,204,'ZH','Zürich')";
    if (!$q->run($query)) return;

    # Spain
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'A Coruña','A Coruña')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Alava','Alava')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Albacete','Albacete')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Alicante','Alicante')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Almeria','Almeria')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Asturias','Asturias')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Avila','Avila')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Badajoz','Badajoz')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Baleares','Baleares')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Barcelona','Barcelona')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Burgos','Burgos')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Caceres','Caceres')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Cadiz','Cadiz')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Cantabria','Cantabria')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Castellon','Castellon')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Ceuta','Ceuta')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Ciudad Real','Ciudad Real')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Cordoba','Cordoba')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Cuenca','Cuenca')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Girona','Girona')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Granada','Granada')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Guadalajara','Guadalajara')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Guipuzcoa','Guipuzcoa')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Huelva','Huelva')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Huesca','Huesca')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Jaen','Jaen')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'La Rioja','La Rioja')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Las Palmas','Las Palmas')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Leon','Leon')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Lleida','Lleida')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Lugo','Lugo')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Madrid','Madrid')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Malaga','Malaga')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Melilla','Melilla')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Murcia','Murcia')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Navarra','Navarra')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Ourense','Ourense')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Palencia','Palencia')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Pontevedra','Pontevedra')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Salamanca','Salamanca')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Santa Cruz de Tenerife','Santa Cruz de Tenerife')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Segovia','Segovia')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Sevilla','Sevilla')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Soria','Soria')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Tarragona','Tarragona')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Teruel','Teruel')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Toledo','Toledo')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Valencia','Valencia')";
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Valladolid','Valladolid')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Vizcaya','Vizcaya')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Zamora','Zamora')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Zaragoza','Zaragoza')";
    if (!$q->run($query)) return;

    // Register masks
    xarRegisterMask('ViewCommerceBlocks','All','commerce','Block','All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadCommerceBlock','All','commerce','Block','All:All:All','ACCESS_READ');
    xarRegisterMask('EditCommerceBlock','All','commerce','Block','All:All:All','ACCESS_EDIT');
    xarRegisterMask('AddCommerceBlock','All','commerce','Block','All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteCommerceBlock','All','commerce','Block','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminCommerceBlock','All','commerce','Block','All:All:All','ACCESS_ADMIN');
    xarRegisterMask('ViewCommerce','All','commerce','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadCommerce','All','commerce','All','All','ACCESS_READ');
    xarRegisterMask('AdminCommerce','All','commerce','All','All','ACCESS_ADMIN');

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'commerce',
                             'blockType'=> 'configmenu'))) return;

    $adminBlockType = xarModAPIFunc('blocks', 'user', 'getblocktype',
                                    array('module'  => 'commerce',
                                          'type'    => 'configmenu'));

    $adminBlockTypeId = $adminBlockType['tid'];

/*    if (!xarModAPIFunc('blocks', 'admin', 'create_instance',
                       array('title'    => 'Admin',
                             'name'     => 'adminpanel',
                             'type'     => $adminBlockTypeId,
                             'groups'   => array(array('gid'      => 1,
                                                       'template' => '')),
                             'template' => '',
                             'state'    =>  2))) {
        return;
    }
*/
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'best_sellers'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'currencies'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'customers_status'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'infobox'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'information'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'languages'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'loginbox'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'manufacturer_info'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'manufacturers'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'order_history'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'product_notifications'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'reviews'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'search'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'shopping_cart'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'specials'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'tell_a_friend'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'whats_new'))) return;

    xarModSetVar('commerce', 'itemsperpage', 20);

// Initialisation successful
    return true;
}

function commerce_activate()
{
    return true;
}

/**
 * upgrade the commerce module from an old version
 */
function commerce_upgrade($oldversion)
{
    switch($oldversion){
        case '0.3.0.1':

    }
// Upgrade successful
    return true;
}

/**
 * delete the commerce module
 */
function commerce_delete()
{
    $tablenameprefix = xarDBGetSiteTablePrefix() . '_commerce_';
    $tables = xarDBGetTables();
    $q = new xenQuery();
        foreach ($tables as $table) {
        if (strpos($table,$tablenameprefix) === 0) {
            $query = "DROP TABLE IF EXISTS " . $table;
            if (!$q->run($query)) return;
        }
    }

    xarModDelAllVars('commerce');
    xarRemoveMasks('commerce');
// Delete successful

return true;
}
# --------------------------------------------------------

?>