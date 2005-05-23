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

    /*
      Our list of objects
    */
    $ice_objects = array('ice_countries', 'ice_currencies', 'ice_taxclasses', 'ice_taxrates', 'ice_languages', 'ice_zones');
    
    // Treat destructive right now
    $existing_objects  = xarModApiFunc('dynamicdata','user','getobjects');
    foreach($existing_objects as $objectid => $objectinfo) {
        if(in_array($objectinfo['name'], $ice_objects)) {
            // KILL
            if(!xarModApiFunc('dynamicdata','admin','deleteobject', array('objectid' => $objectid))) return;
        } 
    }
    
    // The countries are managed through a DD object. 
    // the xardata/ directory provides the definition and the initialisation
    // data in XML files ice-countries-def.xml an ice-countries-data.xml

    // TODO: This will bomb out if the object already exists
    foreach($ice_objects as $ice_object) {
        $def_file = 'modules/commerce/xardata/'.$ice_object.'-def.xml';
        $dat_file = 'modules/commerce/xardata/'.$ice_object.'-data.xml'; 
        
        if(!xarModApiFunc('dynamicdata','util','import', array('file' => $def_file))) return;
        // Let data import be allowed to fail
        xarModApiFunc('dynamicdata','util','import', array('file' => $dat_file));
    }


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

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_personal_offers_by_customers_status_0";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_personal_offers_by_customers_status_0 (
      price_id int NOT NULL auto_increment,
      products_id int NOT NULL,
      quantity int NOT NULL,
      personal_offer decimal(15,4),
      PRIMARY KEY (price_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_personal_offers_by_customers_status_1";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_personal_offers_by_customers_status_1 (
      price_id int NOT NULL auto_increment,
      products_id int NOT NULL,
      quantity int NOT NULL,
      personal_offer decimal(15,4),
      PRIMARY KEY (price_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_personal_offers_by_customers_status_2";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_personal_offers_by_customers_status_2 (
      price_id int NOT NULL auto_increment,
      products_id int NOT NULL,
      quantity int NOT NULL,
      personal_offer decimal(15,4),
      PRIMARY KEY (price_id)
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
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (11,0,0,3,'Shipping & Returns','Shipping & Returns','Put here your Shipping & Returns information.',1,'',1,1,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (12,0,0,3,'Privacy Notice','Privacy Notice','Put here your Privacy Notice information.',1,'',1,2,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (13,0,0,3,'Conditions of Use','Conditions of Use','Conditions of Use<br />Put here your Conditions of Use information. <br />1. Validity<br />2. Offers<br />3. Price<br />4. Dispatch and passage of the risk<br />5. Delivery<br />6. Terms of payment<br />7. Retention of title<br />8. Notices of defect, guarantee and compensation<br />9. Fair trading cancelling / non-acceptance<br />10. Place of delivery and area of jurisdiction<br />11. Final clauses',1,'',1,3,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (14,0,0,3,'Contact','Contact','Put here your Contact information.',1,'',1,4,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (15,0,0,3,'Index','Welcome','{\$greeting}<br><br> Dies ist die Standardinstallation des osCommerce Forking Projektes - XT-Commerce. Alle dargestellten Produkte dienen zur Demonstration der Funktionsweise. Wenn Sie Produkte bestellen, so werden diese weder ausgeliefert, noch in Rechnung gestellt. Alle Informationen zu den verschiedenen Produkten sind erfunden und daher kann kein Anspruch daraus abgeleitet werden.<br><br>Sollten Sie daran interessiert sein das Programm, welches die Grundlage für diesen Shop bildet, einzusetzen, so besuchen Sie bitte die Supportseite von XT-Commerce. Dieser Shop basiert auf der XT-Commerce Version Beta2.<br><br>Der hier dargestellte Text kann in der folgenden Datei einer jeden Sprache geändert werden: [Pfad zu catalog]/lang/catalog/[language]/index.php.<br><br>Das kann manuell geschehen, oder über das Administration Tool mit Sprache->[language]->Sprache definieren, oder durch Verwendung des Hilfsprogrammes->Datei Manager.',1,'',0,5,0)";
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
//    $query = "INSERT INTO " . $prefix . "_commerce_box_align VALUES (8, 'shopping_cart.php', 'right', 1, 1)";
//    if (!$q->run($query)) return;
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
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'STORE_ZONE', '', 1, 7, NULL, '', 'commerce_userapi_get_zone_name', 'commerce_adminapi_pull_down_zone_list(')";
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
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DEFAULT_CUSTOMERS_STATUS_ID_ADMIN', '0',  1, 20, NULL, '', 'commerce_userapi_get_customer_status_name', 'commerce_adminapi_pull_down_customers_status_list(')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DEFAULT_CUSTOMERS_STATUS_ID_GUEST', '1',  1, 21, NULL, '', 'commerce_userapi_get_customer_status_name', 'commerce_adminapi_pull_down_customers_status_list(')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DEFAULT_CUSTOMERS_STATUS_ID', '2',  1, 23, NULL, '', 'commerce_userapi_get_customer_status_name', 'commerce_adminapi_pull_down_customers_status_list(')";
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


    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '1', '1', 'Pending')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '1', '2', 'Offen')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '1', '3', 'Ozhidanie')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '2', '1', 'Processing')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '2', '2', 'In Bearbeitung')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '2', '3', 'Obrabotka')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '3', '1', 'Delivered')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '3', '2', 'Versendet')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_orders_status VALUES ( '3', '3', 'Dostavlen')";
    if (!$q->run($query)) return;


       // Default values for discounts and other stuff
    $status_discount = '0.00';
    $status_ot_discount_flag = '1';
    $status_ot_discount = '0.00';
    $graduated_price = '1';
    $show_price = '1';
    $show_tax = '1';

    $status_discount2 = '0.00';
    $status_ot_discount_flag2 = '1';
    $status_ot_discount2 = '0.00';
    $graduated_price2 = '1';
    $show_price2 = '1';
    $show_tax2 = '1';

    $query = "INSERT INTO " . $prefix . "_commerce_customers_info (
                                        customers_info_id,
                                        customers_info_date_of_last_logon,
                                        customers_info_number_of_logons,
                                        customers_info_date_account_created,
                                        customers_info_date_account_last_modified,
                                        global_product_notifications) VALUES
                                        ('3','','','','','')";
    if (!$q->run($query)) return;

    // status Admin
    $query = "INSERT INTO " . $prefix . "_commerce_customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES ('0', '1', 'Admin', 1, 'admin_status.gif', '0.00', '1', '0.00', '1', '1', '1')";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_commerce_customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES ('0', '2', 'Admin', 1, 'admin_status.gif', '0.00', '1', '0.00', '1', '1', '1')";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_commerce_customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES ('0', '3', 'Admin', 1, 'admin_status.gif', '0.00', '1', '0.00', '1', '1', '1')";
    if (!$q->run($query)) return;

    // status Guest
    $query = "INSERT INTO " . $prefix . "_commerce_customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (1, 1, 'Guest', 1, 'guest_status.gif', '".$status_discount."', '".$status_ot_discount_flag."', '".$status_ot_discount."', '".$graduated_price."', '".$show_price."', '".$show_tax."')";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_commerce_customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (1, 2, 'Gast', 1, 'guest_status.gif', '".$status_discount."', '".$status_ot_discount_flag."', '".$status_ot_discount."', '".$graduated_price."', '".$show_price."', '".$show_tax."')";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_commerce_customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (1, 3, 'Guest', 1, 'guest_status.gif', '".$status_discount."', '".$status_ot_discount_flag."', '".$status_ot_discount."', '".$graduated_price."', '".$show_price."', '".$show_tax."')";
    if (!$q->run($query)) return;

    // status New customer
    $query = "INSERT INTO " . $prefix . "_commerce_customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (2, 1, 'New customer', 1, 'customer_status.gif', '".$status_discount2."', '".$status_ot_discount_flag2."', '".$status_ot_discount2."', '".$graduated_price2."', '".$show_price2."', '".$show_tax2."')";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_commerce_customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (2, 2, 'Neuer Kunde', 1, 'customer_status.gif', '".$status_discount2."', '".$status_ot_discount_flag2."', '".$status_ot_discount2."', '".$graduated_price2."', '".$show_price2."', '".$show_tax2."')";
    if (!$q->run($query)) return;

    $query = "INSERT INTO " . $prefix . "_commerce_customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (2, 3, 'New customer', 1, 'customer_status.gif', '".$status_discount2."', '".$status_ot_discount_flag2."', '".$status_ot_discount2."', '".$graduated_price2."', '".$show_price2."', '".$show_tax2."')";
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

// Register some block types
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'commerce',
                             'blockType'=> 'configmenu'))) return;

/*     $adminBlockType = xarModAPIFunc('blocks', 'user', 'getblocktype',
                                    array('module'  => 'commerce',
                                          'type'    => 'configmenu'));

    $adminBlockTypeId = $adminBlockType['tid'];

   if (!xarModAPIFunc('blocks', 'admin', 'create_instance',
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
                'blockType' => 'order_history'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'reviews'))) return;

    //if (!xarModAPIFunc('blocks',
    //        'admin',
    //        'register_block_type',
    //        array('modName' => 'commerce',
    //            'blockType' => 'shopping_cart'))) return;

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

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'admin'))) return;

    xarModSetVar('commerce', 'itemsperpage', 20);

// Create some block instances

// Put a config menu in the 'left' blockgroup
    $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'commerce', 'type'=>'configmenu'));
    $leftgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'left'));
    $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                  'name' => 'commerceconfig',
                                                                  'state' => 0,
                                                                  'groups' => array($leftgroup)));
// Put an exit menu in the 'left' blockgroup
    $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'base', 'type'=>'menu'));
    $leftgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'left'));
    $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                  'name' => 'commerceexit',
                                                                  'content' => 'a:6:{s:14:"displaymodules";b:0;s:10:"showlogout";b:1;s:10:"displayrss";b:0;s:12:"displayprint";b:0;s:6:"marker";s:3:"[x]";s:7:"content";s:52:"index.php?module=commerce&type=user&func=exit|Exit||";}',
                                                                  'state' => 0,
                                                                  'groups' => array($leftgroup)));
// Put a information block in the 'left' blockgroup
    $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'commerce', 'type'=>'information'));
    $leftgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'left'));
    $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                  'name' => 'commerceinformation',
                                                                  'state' => 0,
                                                                  'groups' => array($leftgroup)));
// Put a language block in the 'right' blockgroup
    $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'roles', 'type'=>'language'));
    $rightgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'right'));
    $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                  'name' => 'commercelanguage',
                                                                  'title' => xarML('Language'),
                                                                  'state' => 0,
                                                                  'groups' => array($rightgroup)));
// Put a currency block in the 'right' blockgroup
    $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'commerce', 'type'=>'currencies'));
    $rightgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'right'));
    $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                  'name' => 'commercecurrencies',
                                                                  'state' => 0,
                                                                  'groups' => array($rightgroup)));
// Put a shopping cart block in the 'right' blockgroup
//    $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'commerce', 'type'=>'shopping_cart'));
//    $rightgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'right'));
//    $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
 //                                                                 'name' => 'commercecart',
//                                                                  'state' => 0,
//                                                                  'groups' => array($rightgroup)));

// Put a admin info block in the 'right' blockgroup
    $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'commerce', 'type'=>'admin'));
    $rightgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'right'));
    $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                  'name' => 'commerceadmininfo',
                                                                  'state' => 0,
                                                                  'groups' => array($rightgroup)));

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

    // Remove the language block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commercelanguage'));
    if ($blockinfo) {
        if(!xarModAPIFunc('blocks', 'admin', 'delete_instance', array('bid' => $blockinfo['bid']))) return;
    }
    // Remove the exit menu
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commerceexit'));
    if ($blockinfo) {
        if(!xarModAPIFunc('blocks', 'admin', 'delete_instance', array('bid' => $blockinfo['bid']))) return;
    }

    // The modules module will take care of all the other blocks


// Delete successful

return true;
}
# --------------------------------------------------------

?>
