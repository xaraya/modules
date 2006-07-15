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
 * initialise the products module
 */
function products_init()
{
    $q = new xenQuery();
    $prefix = xarDBGetSiteTablePrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_categories";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_categories (
      categories_id int NOT NULL auto_increment,
      categories_image varchar(64),
      parent_id int DEFAULT '0' NOT NULL,
      categories_status TINYint (1)  UNSIGNED DEFAULT '1' NOT NULL,
      categories_template varchar(64),
      group_ids TEXT,
      listing_template varchar(64),
      sort_order int(3),
      product_sorting varchar(32),
      product_sorting2 varchar(32),
      date_added int(10) UNSIGNED,
      last_modified int(10) UNSIGNED,
      PRIMARY KEY (categories_id),
      KEY idx_categories_parent_id (parent_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_categories_description";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_categories_description (
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

    $query = "DROP TABLE IF EXISTS " . $prefix . "_product_configuration";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_product_configuration (
      configuration_id int NOT NULL auto_increment,
      configuration_key varchar(64) NOT NULL,
      configuration_value varchar(255) NOT NULL,
      configuration_group_id int NOT NULL,
      sort_order int(5) NULL,
      last_modified int(10) UNSIGNED NULL,
      date_added int(10) UNSIGNED NOT NULL,
      use_function varchar(255) NULL,
      set_function varchar(255) NULL,
      PRIMARY KEY (configuration_id),
      KEY idx_configuration_group_id (configuration_group_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_configuration_group";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_configuration_group (
      configuration_group_id int NOT NULL auto_increment,
      configuration_group_title varchar(64) NOT NULL,
      configuration_group_description varchar(255) NOT NULL,
      sort_order int(5) NULL,
      visible int(1) DEFAULT '1' NULL,
      PRIMARY KEY (configuration_group_id)
    )";
    if (!$q->run($query)) return;


    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_product";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_product (
      product_id int NOT NULL auto_increment,
      product_quantity int(4) NOT NULL,
      product_shippingtime int(4) NOT NULL,
      product_model varchar(12),
      group_ids TEXT,
      product_sort int(4),
      product_price decimal(15,4) NOT NULL,
      product_discount_allowed decimal(3,2) DEFAULT '0' NOT NULL,
      product_date_added int(10) UNSIGNED NOT NULL,
      product_last_modified int(10) UNSIGNED,
      product_date_available int(10) UNSIGNED,
      product_weight decimal(5,2) NOT NULL,
      product_status tinyint(1) NOT NULL,
      product_tax_class_id int NOT NULL,
      manufacturers_id int NULL,
      product_ordered int NOT NULL default '0',
      product_fsk18 int(1) NOT NULL DEFAULT '0',
      product_image varchar(255),
      xar_modid int(11) NOT NULL,
      xar_itemid int(11) NOT NULL,
      xar_itemtype int(11) NULL,
      PRIMARY KEY (product_id),
      KEY idx_product_date_added (product_date_added)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_product_attributes";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_product_attributes (
      product_attributes_id int NOT NULL auto_increment,
      product_id int NOT NULL,
      options_id int NOT NULL,
      options_values_id int NOT NULL,
      options_values_price decimal(15,4) NOT NULL,
      price_prefix char(1) NOT NULL,
      attributes_model varchar(12) NULL,
      attributes_stock int(4) NULL,
      options_values_weight decimal(15,4) NOT NULL,
      weight_prefix char(1) NOT NULL,
      PRIMARY KEY (product_attributes_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_product_attributes_download";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_product_attributes_download (
      product_attributes_id int NOT NULL,
      product_attributes_filename varchar(255) NOT NULL default '',
      product_attributes_maxdays int(2) default '0',
      product_attributes_maxcount int(2) default '0',
      PRIMARY KEY  (product_attributes_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_product_description";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_product_description (
      product_id int NOT NULL auto_increment,
      language_id int NOT NULL default '1',
      product_name varchar(64) NOT NULL default '',
      product_description text,
      product_short_description text,
      product_meta_title text NOT NULL,
      product_meta_description text NOT NULL,
      product_meta_keywords text NOT NULL,
      product_url varchar(255) default NULL,
      product_viewed int(5) default '0',
      PRIMARY KEY  (product_id,language_id),
      KEY product_name (product_name)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_product_notifications";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_product_notifications (
      product_id int NOT NULL,
      customers_id int NOT NULL,
      date_added int(10) UNSIGNED NOT NULL,
      PRIMARY KEY (product_id, customers_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_product_options";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_product_options (
      product_options_id int NOT NULL default '0',
      language_id int NOT NULL default '1',
      product_options_name varchar(32) NOT NULL default '',
      PRIMARY KEY  (product_options_id,language_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_product_options_values";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_product_options_values (
      product_options_values_id int NOT NULL default '0',
      language_id int NOT NULL default '1',
      product_options_values_name varchar(64) NOT NULL default '',
      PRIMARY KEY  (product_options_values_id,language_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_product_options_values_to_product_options";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_product_options_values_to_product_options (
      product_options_values_to_product_options_id int NOT NULL auto_increment,
      product_options_id int NOT NULL,
      product_options_values_id int NOT NULL,
      PRIMARY KEY (product_options_values_to_product_options_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_product_graduated_prices";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_product_graduated_prices (
      product_id int(11) NOT NULL default '0',
      quantity int(11) NOT NULL default '0',
      unitprice decimal(15,4) NOT NULL default '0.0000',
      KEY product_id (product_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_product_to_categories";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_product_to_categories (
      product_id int NOT NULL,
      categories_id int NOT NULL,
      PRIMARY KEY (product_id,categories_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_specials";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_specials (
      specials_id int NOT NULL auto_increment,
      product_id int NOT NULL,
      specials_new_product_price decimal(15,4) NOT NULL,
      specials_date_added int(10) UNSIGNED,
      specials_last_modified int(10) UNSIGNED,
      expires_date int(10) UNSIGNED,
      date_status_change int(10) UNSIGNED,
      status int(1) NOT NULL DEFAULT '1',
      PRIMARY KEY (specials_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_products_content_manager";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_products_content_manager (
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

    # data

    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (1,0,0,1,'Shipping & Returns','Shipping & Returns','Put here your Shipping & Returns information.',1,'',1,1,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (2,0,0,1,'Privacy Notice','Privacy Notice','Put here your Privacy Notice information.',1,'',1,2,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (3,0,0,1,'Conditions of Use','Conditions of Use','Conditions of Use<br />Put here your Conditions of Use information. <br />1. Validity<br />2. Offers<br />3. Price<br />4. Dispatch and passage of the risk<br />5. Delivery<br />6. Terms of payment<br />7. Retention of title<br />8. Notices of defect, guarantee and compensation<br />9. Fair trading cancelling / non-acceptance<br />10. Place of delivery and area of jurisdiction<br />11. Final clauses',1,'',1,3,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (4,0,0,1,'Contact','Contact','Put here your Contact information.',1,'',1,4,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (5,0,0,1,'Index','Welcome','{\$greeting}<br><br> Dies ist die Standardinstallation des osCommerce Forking Projektes - XT-Commerce. Alle dargestellten Produkte dienen zur Demonstration der Funktionsweise. Wenn Sie Produkte bestellen, so werden diese weder ausgeliefert, noch in Rechnung gestellt. Alle Informationen zu den verschiedenen Produkten sind erfunden und daher kann kein Anspruch daraus abgeleitet werden.<br><br>Sollten Sie daran interessiert sein das Programm, welches die Grundlage fr diesen Shop bildet, einzusetzen, so besuchen Sie bitte die Supportseite von XT-Commerce. Dieser Shop basiert auf der XT-Commerce Version Beta2.<br><br>Der hier dargestellte Text kann in der folgenden Datei einer jeden Sprache ge?dert werden: [Pfad zu catalog]/lang/catalog/[language]/index.php.<br><br>Das kann manuell geschehen, oder ber das Administration Tool mit Sprache->[language]->Sprache definieren, oder durch Verwendung des Hilfsprogrammes->Datei Manager.',1,'',0,5,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (6,0,0,2,'Liefer- und Versandkosten','Liefer- und Versandkosten','Fgen Sie hier Ihre Informationen ber Liefer- und Versandkosten ein.',1,'',1,1,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (7,0,0,2,'Privatsph?e und Datenschutz','Privatsph?e und Datenschutz','Fgen Sie hier Ihre Informationen ber Privatsph?e und Datenschutz ein.',1,'',1,2,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (8,0,0,2,'Unsere AGB\'s','Allgemeine Gesch?tsbedingungen','<strong>Allgemeine Gesch&auml;ftsbedingungen<br></strong><br>F&uuml;gen Sie hier Ihre allgemeinen Gesch&auml;ftsbedingungen ein.<br>1. Geltung<br>2. Angebote<br>3. Preis<br>4. Versand und Gefahr&uuml;bergang<br>5. Lieferung<br>6. Zahlungsbedingungen<br>7. Eigentumsvorbehalt <br>8. M&auml;ngelr&uuml;gen, Gew&auml;hrleistung und Schadenersatz<br>9. Kulanzr&uuml;cknahme / Annahmeverweigerung<br>10. Erf&uuml;llungsort und Gerichtsstand<br>11. Schlussbestimmungen',1,'',1,3,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (9,0,0,2,'Kontakt','Kontakt','Fgen Sie hier Ihre Informationen ber Kontakt ein.',1,'',1,4,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (10,0,0,2,'Index','Willkommen','{\$greeting}<br><br> Dies ist die Standardinstallation des osCommerce Forking Projektes - XT-Commerce. Alle dargestellten Produkte dienen zur Demonstration der Funktionsweise. Wenn Sie Produkte bestellen, so werden diese weder ausgeliefert, noch in Rechnung gestellt. Alle Informationen zu den verschiedenen Produkten sind erfunden und daher kann kein Anspruch daraus abgeleitet werden.<br><br>Sollten Sie daran interessiert sein das Programm, welches die Grundlage fr diesen Shop bildet, einzusetzen, so besuchen Sie bitte die Supportseite von XT-Commerce. Dieser Shop basiert auf der XT-Commerce Version Beta2.<br><br>Der hier dargestellte Text kann in der folgenden Datei einer jeden Sprache ge?dert werden: [Pfad zu catalog]/lang/catalog/[language]/index.php.<br><br>Das kann manuell geschehen, oder ber das Administration Tool mit Sprache->[language]->Sprache definieren, oder durch Verwendung des Hilfsprogrammes->Datei Manager.',1,'',0,5,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (11,0,0,3,'Shipping & Returns','Shipping & Returns','Put here your Shipping & Returns information.',1,'',1,1,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (12,0,0,3,'Privacy Notice','Privacy Notice','Put here your Privacy Notice information.',1,'',1,2,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (13,0,0,3,'Conditions of Use','Conditions of Use','Conditions of Use<br />Put here your Conditions of Use information. <br />1. Validity<br />2. Offers<br />3. Price<br />4. Dispatch and passage of the risk<br />5. Delivery<br />6. Terms of payment<br />7. Retention of title<br />8. Notices of defect, guarantee and compensation<br />9. Fair trading cancelling / non-acceptance<br />10. Place of delivery and area of jurisdiction<br />11. Final clauses',1,'',1,3,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (14,0,0,3,'Contact','Contact','Put here your Contact information.',1,'',1,4,0)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_products_content_manager VALUES (15,0,0,3,'Index','Welcome','{\$greeting}<br><br> Dies ist die Standardinstallation des osCommerce Forking Projektes - XT-Commerce. Alle dargestellten Produkte dienen zur Demonstration der Funktionsweise. Wenn Sie Produkte bestellen, so werden diese weder ausgeliefert, noch in Rechnung gestellt. Alle Informationen zu den verschiedenen Produkten sind erfunden und daher kann kein Anspruch daraus abgeleitet werden.<br><br>Sollten Sie daran interessiert sein das Programm, welches die Grundlage fr diesen Shop bildet, einzusetzen, so besuchen Sie bitte die Supportseite von XT-Commerce. Dieser Shop basiert auf der XT-Commerce Version Beta2.<br><br>Der hier dargestellte Text kann in der folgenden Datei einer jeden Sprache ge?dert werden: [Pfad zu catalog]/lang/catalog/[language]/index.php.<br><br>Das kann manuell geschehen, oder ber das Administration Tool mit Sprache->[language]->Sprache definieren, oder durch Verwendung des Hilfsprogrammes->Datei Manager.',1,'',0,5,0)";
    if (!$q->run($query)) return;

    # configuration_group_id 17
    $query = "INSERT INTO " . $prefix . "_product_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'USE_SPAW', 'true', 17, 1, NULL, '', NULL, 'products_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_product_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ACTIVATE_GIFT_SYSTEM', 'false', 17, 2, NULL, '', NULL, 'products_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_product_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SECURITY_CODE_LENGTH', '10', 17, 3, NULL, '2003-12-05 05:01:41', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_product_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'NEW_SIGNUP_GIFT_VOUCHER_AMOUNT', '0', 17, 4, NULL, '2003-12-05 05:01:41', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_product_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'NEW_SIGNUP_DISCOUNT_COUPON', '', 17, 5, NULL, '2003-12-05 05:01:41', NULL, NULL)";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_product_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'ACTIVATE_SHIPPING_STATUS', 'true', 17, 6, NULL, '', NULL, 'products_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_product_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'DISPLAY_CONDITIONS_ON_CHECKOUT', 'true',17, 7, NULL, '', NULL, 'products_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_product_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'CHECK_CLIENT_AGENT', 'false',17, 7, NULL, '', NULL, 'products_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_product_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'SHOW_IP_LOG', 'false',17, 8, NULL, '', NULL, 'products_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;
    $query = "INSERT INTO " . $prefix . "_product_configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', 'GROUP_CHECK', 'false',  17, 9, NULL, '', NULL, 'products_adminapi_select_option(array(\'true\', \'false\'))')";
    if (!$q->run($query)) return;



# --------------------------------------------------------
#
# Register masks
#
    xarRegisterMask('ViewProductsBlocks','All','products','Block','All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadProductsBlock','All','products','Block','All:All:All','ACCESS_READ');
    xarRegisterMask('EditProductsBlock','All','products','Block','All:All:All','ACCESS_EDIT');
    xarRegisterMask('AddProductsBlock','All','products','Block','All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteProductsBlock','All','products','Block','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminProductsBlock','All','products','Block','All:All:All','ACCESS_ADMIN');
    xarRegisterMask('ViewProducts','All','products','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadProducts','All','products','All','All','ACCESS_READ');
    xarRegisterMask('EditProducts','All','products','All','All:All:All','ACCESS_EDIT');
    xarRegisterMask('AddProducts','All','products','All','All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteProducts','All','products','All','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminProducts','All','products','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up modvars
#
    xarModSetVar('products', 'itemsperpage', 20);




# --------------------------------------------------------
#
# Configure Hooks
#

    // when a new module item is being specified
    if (!xarModRegisterHook('item', 'new', 'GUI',
                           'products', 'admin', 'newhook')) {
        return false;
    }
    // when a module item is created
    if (!xarModRegisterHook('item', 'create', 'API',
                           'products', 'admin', 'createhook')) {
        return false;
    }
    // when a module item is being modified
    if (!xarModRegisterHook('item', 'modify', 'GUI',
                           'products', 'admin', 'modifyhook')) {
        return false;
    }
    // when a module item is updated
    if (!xarModRegisterHook('item', 'update', 'API',
                           'products', 'admin', 'updatehook')) {
        return false;
    }
    // when a module item is deleted
    if (!xarModRegisterHook('item', 'delete', 'API',
                           'products', 'admin', 'deletehook')) {
        return false;
    }
    // when a module configuration is being modified
    if (!xarModRegisterHook('module', 'modifyconfig', 'GUI',
                           'products', 'admin', 'modifyconfighook')) {
        return false;
    }
    // when a module configuration is updated
    if (!xarModRegisterHook('module', 'updateconfig', 'API',
                           'products', 'admin', 'updateconfighook')) {
        return false;
    }
    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'products', 'admin', 'removehook')) {
        return false;
    }

    // when a whole module is displayed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModRegisterHook('item', 'display', 'GUI',
                           'products', 'user', 'displayhook')) {
        return false;
    }

	xarModRegisterHook('module', 'getconfig', 'API','products', 'admin', 'getconfighook');
    xarModAPIFunc('modules','admin','enablehooks',array('callerModName' => 'commerce', 'hookModName' => 'products'));

# --------------------------------------------------------
#
# Delete block types for this module
#
    $blocktypes = xarModAPIfunc(
        'blocks', 'user', 'getallblocktypes',
        array('module' => 'products')
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
            array('modName' => 'products',
                'blockType' => 'categories'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'products',
                'blockType' => 'best_sellers'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'products',
                'blockType' => 'product_notifications'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'products',
                'blockType' => 'search'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'products',
                'blockType' => 'specials'))) return;

# --------------------------------------------------------
#
# Register block instances
#
// Put a search block in the 'left' blockgroup
    $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'products', 'type'=>'search'));
    $leftgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'left'));
    $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                  'name' => 'productsearch',
                                                                  'state' => 0,
                                                                  'groups' => array($leftgroup)));
// Put a categories block in the 'left' blockgroup
    $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'products', 'type'=>'categories'));
    $leftgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'left'));
    $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                  'name' => 'productcategories',
                                                                  'state' => 0,
                                                                  'groups' => array($leftgroup)));

# --------------------------------------------------------
#
# Add this module to the list of installed commerce suite modules
#
    $modules = unserialize(xarModGetVar('commerce', 'ice_modules'));
    $info = xarModGetInfo(xarModGetIDFromName('products'));
    $modules[$info['name']] = $info['regid'];
    $result = xarModSetVar('commerce', 'ice_modules', serialize($modules));

// Initialisation successful
    return true;
}

function products_activate()
{
    return true;
}

/**
 * upgrade the products module from an old version
 */
function products_upgrade($oldversion)
{
    switch($oldversion){
        case '0.3.0.1':

    }
// Upgrade successful
    return true;
}

/**
 * delete the products module
 */
function products_delete()
{
    $tablenameprefix = xarDBGetSiteTablePrefix() . '_product_';
    $tables = xarDBGetTables();
    $q = new xenQuery();
        foreach ($tables as $table) {
        if (strpos($table,$tablenameprefix) === 0) {
            $query = "DROP TABLE IF EXISTS " . $table;
            if (!$q->run($query)) return;
        }
    }

# --------------------------------------------------------
#
# Remove modvars, masks and privilege instances
#
    xarModDelAllVars('products');
    xarRemoveMasks('products');
    xarRemoveInstances('products');

# --------------------------------------------------------
#
# Delete block types for this module
#
    $blocktypes = xarModAPIfunc(
        'blocks', 'user', 'getallblocktypes',
        array('module' => 'products')
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
# Remove blocks instances
#
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'productsearch'));
    if ($blockinfo) {
        if(!xarModAPIFunc('blocks', 'admin', 'delete_instance', array('bid' => $blockinfo['bid']))) return;
    }

    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'productcategories'));
    if ($blockinfo) {
        if(!xarModAPIFunc('blocks', 'admin', 'delete_instance', array('bid' => $blockinfo['bid']))) return;
    }

    // Remove from the list of commerce modules
    $modules = unserialize(xarModGetVar('commerce', 'ice_modules'));
    unset($modules['products']);
    $result = xarModSetVar('commerce', 'ice_modules', serialize($modules));

	// Delete successful
	return true;
}
# --------------------------------------------------------

?>