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
    if (!xarVarFetch('createdefaultgroup', 'checkbox', $createdefaultgroup, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('createdefaultuser', 'checkbox', $createdefaultuser, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultgroupname', 'str:1:', $defaultgroupname, 'CommerceGroup', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultusername', 'str:1:', $defaultusername, 'CommerceUser', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultuserpass', 'str:1:', $defaultuserpass, 'password', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('createdefaultprivileges', 'checkbox', $createdefaultprivileges, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultdata', 'array', $defaultdata, array(), XARVAR_NOT_REQUIRED)) return;

    $defaultdata[] = 'ice_configuration';
    $defaultdata[] = 'ice_config_groups';

# --------------------------------------------------------
#
# Create database tables
#
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

/* Move to customers module
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
*/
/*  Move to products module
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
*/
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

    /** START ICE MODEL **/
    /*
      Our list of objects
    */

    $ice_objects = $defaultdata;
    /*
    $ice_objects = array(
                         'ice_countries',
//                       'ice_currencies',
//                       'ice_taxclasses',
//                         'ice_taxrates',
//                         'ice_zones',
//                         'ice_taxzones',
//                         'ice_taxzonemapping',
//                         'ice_languages',
                         'ice_addressformats',
                         'ice_configuration', 'ice_config_groups',
                         'ice_roles'
                         );
*/
    // Treat destructive right now
    $existing_objects  = xarModApiFunc('dynamicdata','user','getobjects');
    foreach($existing_objects as $objectid => $objectinfo) {
        if(in_array($objectinfo['name'], $ice_objects)) {
            // KILL
            if(!xarModApiFunc('dynamicdata','admin','deleteobject', array('objectid' => $objectid))) return;
        }
    }

    // Most information will have a DD object presentation, some will be
    // dynamic, others defined with a static datasource.
    // These definitions and data are in the xardata directory in this module.
    // and provide the definition and optionally  the initialisation
    // data in XML files [ice-objectname]-def.xml an [ice-objectname]-data.xml

    // TODO: This will bomb out if the object already exists
    $objects = array();

    foreach($ice_objects as $ice_object) {
        $def_file = 'modules/commerce/xardata/'.$ice_object.'-def.xml';
        $dat_file = 'modules/commerce/xardata/'.$ice_object.'-dat.xml';

        $objectid = xarModAPIFunc('dynamicdata','util','import', array('file' => $def_file, 'keepitemid' => true));
        if (!$objectid) return;
        else $objects[$ice_object] = $objectid;
        // Let data import be allowed to be empty
        if(file_exists($dat_file) && in_array($ice_object, $defaultdata)) {
            // And allow it to fail for now
            xarModAPIFunc('dynamicdata','util','import', array('file' => $dat_file,'keepitemid' => true));
        }
    }

    xarModVars::set('commerce','ice_objects',serialize($objects));

    /** END ICE MODEL **/

/* Move to customers module
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
*/
/*
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
    */
/*
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
*/
/*  Move to customers module
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
*/
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

/*    $query = "DROP TABLE IF EXISTS " . $prefix . "_commerce_sessions";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_commerce_sessions (
      sesskey varchar(32) NOT NULL,
      expiry int(11) unsigned NOT NULL,
      value text NOT NULL,
      PRIMARY KEY (sesskey)
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
*/


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
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (5,0,0,1,'Index','Welcome','{\$greeting}<br><br> Dies ist die Standardinstallation des osCommerce Forking Projektes - XT-Commerce. Alle dargestellten Produkte dienen zur Demonstration der Funktionsweise. Wenn Sie Produkte bestellen, so werden diese weder ausgeliefert, noch in Rechnung gestellt. Alle Informationen zu den verschiedenen Produkten sind erfunden und daher kann kein Anspruch daraus abgeleitet werden.<br><br>Sollten Sie daran interessiert sein das Programm, welches die Grundlage f¸r diesen Shop bildet, einzusetzen, so besuchen Sie bitte die Supportseite von XT-Commerce. Dieser Shop basiert auf der XT-Commerce Version Beta2.<br><br>Der hier dargestellte Text kann in der folgenden Datei einer jeden Sprache ge‰ndert werden: [Pfad zu catalog]/lang/catalog/[language]/index.php.<br><br>Das kann manuell geschehen, oder ¸ber das Administration Tool mit Sprache->[language]->Sprache definieren, oder durch Verwendung des Hilfsprogrammes->Datei Manager.',1,'',0,5,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (6,0,0,2,'Liefer- und Versandkosten','Liefer- und Versandkosten','F¸gen Sie hier Ihre Informationen ¸ber Liefer- und Versandkosten ein.',1,'',1,1,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (7,0,0,2,'Privatsph‰re und Datenschutz','Privatsph‰re und Datenschutz','F¸gen Sie hier Ihre Informationen ¸ber Privatsph‰re und Datenschutz ein.',1,'',1,2,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (8,0,0,2,'Unsere AGB\'s','Allgemeine Gesch‰ftsbedingungen','<strong>Allgemeine Gesch&auml;ftsbedingungen<br></strong><br>F&uuml;gen Sie hier Ihre allgemeinen Gesch&auml;ftsbedingungen ein.<br>1. Geltung<br>2. Angebote<br>3. Preis<br>4. Versand und Gefahr&uuml;bergang<br>5. Lieferung<br>6. Zahlungsbedingungen<br>7. Eigentumsvorbehalt <br>8. M&auml;ngelr&uuml;gen, Gew&auml;hrleistung und Schadenersatz<br>9. Kulanzr&uuml;cknahme / Annahmeverweigerung<br>10. Erf&uuml;llungsort und Gerichtsstand<br>11. Schlussbestimmungen',1,'',1,3,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (9,0,0,2,'Kontakt','Kontakt','F¸gen Sie hier Ihre Informationen ¸ber Kontakt ein.',1,'',1,4,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (10,0,0,2,'Index','Willkommen','{\$greeting}<br><br> Dies ist die Standardinstallation des osCommerce Forking Projektes - XT-Commerce. Alle dargestellten Produkte dienen zur Demonstration der Funktionsweise. Wenn Sie Produkte bestellen, so werden diese weder ausgeliefert, noch in Rechnung gestellt. Alle Informationen zu den verschiedenen Produkten sind erfunden und daher kann kein Anspruch daraus abgeleitet werden.<br><br>Sollten Sie daran interessiert sein das Programm, welches die Grundlage f¸r diesen Shop bildet, einzusetzen, so besuchen Sie bitte die Supportseite von XT-Commerce. Dieser Shop basiert auf der XT-Commerce Version Beta2.<br><br>Der hier dargestellte Text kann in der folgenden Datei einer jeden Sprache ge‰ndert werden: [Pfad zu catalog]/lang/catalog/[language]/index.php.<br><br>Das kann manuell geschehen, oder ¸ber das Administration Tool mit Sprache->[language]->Sprache definieren, oder durch Verwendung des Hilfsprogrammes->Datei Manager.',1,'',0,5,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (11,0,0,3,'Shipping & Returns','Shipping & Returns','Put here your Shipping & Returns information.',1,'',1,1,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (12,0,0,3,'Privacy Notice','Privacy Notice','Put here your Privacy Notice information.',1,'',1,2,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (13,0,0,3,'Conditions of Use','Conditions of Use','Conditions of Use<br />Put here your Conditions of Use information. <br />1. Validity<br />2. Offers<br />3. Price<br />4. Dispatch and passage of the risk<br />5. Delivery<br />6. Terms of payment<br />7. Retention of title<br />8. Notices of defect, guarantee and compensation<br />9. Fair trading cancelling / non-acceptance<br />10. Place of delivery and area of jurisdiction<br />11. Final clauses',1,'',1,3,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (14,0,0,3,'Contact','Contact','Put here your Contact information.',1,'',1,4,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_commerce_content_manager VALUES (15,0,0,3,'Index','Welcome','{\$greeting}<br><br> Dies ist die Standardinstallation des osCommerce Forking Projektes - XT-Commerce. Alle dargestellten Produkte dienen zur Demonstration der Funktionsweise. Wenn Sie Produkte bestellen, so werden diese weder ausgeliefert, noch in Rechnung gestellt. Alle Informationen zu den verschiedenen Produkten sind erfunden und daher kann kein Anspruch daraus abgeleitet werden.<br><br>Sollten Sie daran interessiert sein das Programm, welches die Grundlage f¸r diesen Shop bildet, einzusetzen, so besuchen Sie bitte die Supportseite von XT-Commerce. Dieser Shop basiert auf der XT-Commerce Version Beta2.<br><br>Der hier dargestellte Text kann in der folgenden Datei einer jeden Sprache ge‰ndert werden: [Pfad zu catalog]/lang/catalog/[language]/index.php.<br><br>Das kann manuell geschehen, oder ¸ber das Administration Tool mit Sprache->[language]->Sprache definieren, oder durch Verwendung des Hilfsprogrammes->Datei Manager.',1,'',0,5,0)";
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

/*    $query = "INSERT INTO " . $prefix . "_commerce_customers_info (
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
*/
# --------------------------------------------------------
#
# Register masks
#
    xarRegisterMask('ViewCommerceBlocks','All','commerce','Block','All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadCommerceBlock','All','commerce','Block','All:All:All','ACCESS_READ');
    xarRegisterMask('EditCommerceBlock','All','commerce','Block','All:All:All','ACCESS_EDIT');
    xarRegisterMask('AddCommerceBlock','All','commerce','Block','All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteCommerceBlock','All','commerce','Block','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminCommerceBlock','All','commerce','Block','All:All:All','ACCESS_ADMIN');
    xarRegisterMask('ViewCommerce','All','commerce','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadCommerce','All','commerce','All','All','ACCESS_READ');
    xarRegisterMask('AdminCommerce','All','commerce','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Delete block details for this module (for now)
#
    $blocktypes = xarModAPIfunc(
        'blocks', 'user', 'getallblocktypes',
        array('module' => 'commerce')
    );

    // Delete block types.
    if (is_array($blocktypes) && !empty($blocktypes)) {
        foreach($blocktypes as $blocktype) {
            $result = xarModAPIfunc(
                'blocks', 'admin', 'delete_type', $blocktype
            );
        }
    }

# --------------------------------------------------------
#
# Register block types
#
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

    //if (!xarModAPIFunc('blocks',
    //        'admin',
    //        'register_block_type',
    //        array('modName' => 'commerce',
    //           'blockType' => 'infobox'))) return;

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

    //if (!xarModAPIFunc('blocks',
    //        'admin',
    //        'register_block_type',
    //        array('modName' => 'commerce',
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'commerce',
                'blockType' => 'reviews'))) return;

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

# --------------------------------------------------------
#
# Create modvars we will need
#
    xarModVars::set('commerce', 'itemsperpage', 20);

# --------------------------------------------------------
#
# Register block instances
#
// Put a config menu in the 'left' blockgroup
    $cur = xarModAPIFunc('blocks','user','get',array('name' => 'commerceconfig'));
    if(!isset($cur)) {
        $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'commerce', 'type'=>'configmenu'));
        $leftgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'left'));
        $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                      'name' => 'commerceconfig',
                                                                      'state' => 0,
                                                                      'groups' => array($leftgroup)));
    }
/*// Put an exit menu in the 'left' blockgroup
    $cur = xarModAPIFunc('blocks','user','get',array('name' => 'commerceexit'));
    if(!isset($cur)) {
        $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'base', 'type'=>'menu'));
        $leftgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'left'));
        $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                      'name' => 'commerceexit',
                                                                      'content' => 'a:6:{s:14:"displaymodules";b:0;s:10:"showlogout";b:1;s:10:"displayrss";b:0;s:12:"displayprint";b:0;s:6:"marker";s:3:"[x]";s:7:"content";s:52:"index.php?module=commerce&type=user&func=exit|Exit||";}',
                                                                      'state' => 0,
                                                                      'groups' => array($leftgroup)));
*/
// Put a information block in the 'left' blockgroup
    $cur = xarModAPIFunc('blocks','user','get',array('name' => 'commerceexit'));
    if(!isset($cur)) {
        $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'base', 'type'=>'menu'));
        $leftgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'left'));
        $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                      'name' => 'commerceexit',
                                                                      'content' => 'a:6:{s:14:"displaymodules";b:0;s:10:"showlogout";b:1;s:10:"displayrss";b:0;s:12:"displayprint";b:0;s:6:"marker";s:3:"[x]";s:7:"content";s:52:"index.php?module=commerce&type=user&func=exit|Exit||";}',
                                                                      'state' => 0,
                                                                      'groups' => array($leftgroup)));
    }
// Put a information block in the 'left' blockgroup
    $cur = xarModAPIFunc('blocks','user','get',array('name' => 'commerceinformation'));
    if(!isset($cur)) {
        $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'commerce', 'type'=>'information'));
        $leftgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'left'));
        $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                      'name' => 'commerceinformation',
                                                                      'state' => 0,
                                                                      'groups' => array($leftgroup)));
    }
/*
// Put a language block in the 'right' blockgroup
    $cur = xarModAPIFunc('blocks','user','get',array('name' => 'commercelanguage'));
    if(!isset($cur)) {
        $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'roles', 'type'=>'language'));
        $rightgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'right'));
        $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                      'name' => 'commercelanguage',
                                                                      'title' => xarML('Language'),
                                                                      'state' => 0,
                                                                      'groups' => array($rightgroup)));
*/
        // Put a currency block in the 'right' blockgroup
    $cur = xarModAPIFunc('blocks','user','get',array('name' => 'commercecurrencies'));
    if(!isset($cur)) {
        $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'commerce', 'type'=>'currencies'));
        $rightgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'right'));
        $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                      'name' => 'commercecurrencies',
                                                                  'state' => 0,
                                                                  'groups' => array($rightgroup)));
    }
// Put a shopping cart block in the 'right' blockgroup
//    $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'commerce', 'type'=>'shopping_cart'));
//    $rightgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'right'));
//    $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
 //                                                                 'name' => 'commercecart',
//                                                                  'state' => 0,
//                                                                  'groups' => array($rightgroup)));

// Put a admin info block in the 'right' blockgroup
    $cur = xarModAPIFunc('blocks','user','get',array('name' => 'commerceadmininfo'));
    if(!isset($cur)) {
        $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'commerce', 'type'=>'admin'));
        $rightgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'right'));
        $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                      'name' => 'commerceadmininfo',
                                                                      'state' => 0,
                                                                      'groups' => array($rightgroup)));
    }
# --------------------------------------------------------
#
# Create a parent category for commerce
#
    $cid = xarModAPIFunc('categories', 'admin', 'create',
                         array('name' => 'ICE Category',
                               'description' => 'Commerce Base Category',
                               'parent_id' => 0));
    // save the id for later
    xarModVars::set('commerce', 'ice_basecategory', $cid);

# --------------------------------------------------------
#
# Set a parent group with privileges and a user for commerce
#

    if ($createdefaultgroup) {
        $role = xarFindRole($defaultgroupname);
        if (empty($role)) {
            $everybody = xarFindRole('Everybody');
            $new = array('name' => $defaultgroupname,
                         'itemtype' => ROLES_GROUPTYPE,
                         'parentid' => $everybody->getID(),
                        );
            $uid = xarModAPIFunc('roles','admin','create',$new);
        } else {
            $uid = $role->getID();
        }
        if ($createdefaultuser) {
            $role = xarFindRole($defaultgroupname);
            if (empty($role)) {
                $new = array('name' => $defaultusername,
                             'uname' => strtolower($defaultusername),
                             'email' => 'none@none.com',
                             'pass' => $defaultuserpass,
                             'state' => ROLES_STATE_ACTIVE,
                             'itemtype' => ROLES_USERTYPE,
                             'parentid' => $uid,
                            );
                $uid = xarModAPIFunc('roles','admin','create',$new);
            }
        }
    }

# --------------------------------------------------------
#
# Register privileges
#
    if ($createdefaultprivileges) {
        xarRegisterPrivilege('ViewCommerce','All','commerce','All','All','ACCESS_OVERVIEW');
        xarRegisterPrivilege('ReadCommerce','All','commerce','All','All','ACCESS_READ');
        xarRegisterPrivilege('EditCommerce','All','commerce','All','All','ACCESS_EDIT');
        xarRegisterPrivilege('AddCommerce','All','commerce','All','All','ACCESS_ADD');
        xarRegisterPrivilege('DeleteCommerce','All','commerce','All','All','ACCESS_DELETE');
        xarRegisterPrivilege('AdminCommerce','All','commerce','All','All','ACCESS_ADMIN');
        $role = xarFindRole($defaultgroupname);
        if (!empty($role)) {
            xarAssignPrivilege('ViewCommerce',$defaultgroupname);
        }
    }

# --------------------------------------------------------
#
# Add this module to the list of installed commerce suite modules
#
    $info = xarModGetInfo(xarModGetIDFromName('commerce'));
    $modules[$info['name']] = $info['regid'];
    $result = xarModVars::set('commerce', 'ice_modules', serialize($modules));

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
# --------------------------------------------------------
#
# Purge all the roles created by this module
#
    $role = xarFindRole('CommerceGroup');
    if (!empty($role)) {
        $descendants = $role->getDescendants();
        foreach ($descendants as $item)
            if (!$item->purge()) return;
        if (!$role->purge()) return;
    }

    // Remove from the list of commerce modules
    $modules = unserialize(xarModVars::get('commerce', 'ice_modules'));
    unset($modules['commerce']);
    $result = xarModVars::set('commerce', 'ice_modules', serialize($modules));

# --------------------------------------------------------
#
# Remove blocks
#
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commercelanguage'));
    if ($blockinfo) {
        if(!xarModAPIFunc('blocks', 'admin', 'delete_instance', array('bid' => $blockinfo['bid']))) return;
    }
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commerceexit'));
    if ($blockinfo) {
        if(!xarModAPIFunc('blocks', 'admin', 'delete_instance', array('bid' => $blockinfo['bid']))) return;
    }

    // The modules module will take care of all the other blocks

    return xarModAPIFunc('xen','admin','deinstall',array('module' => 'commerce'));
}

?>