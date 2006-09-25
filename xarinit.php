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
 * initialise the carts module
 */
function carts_init()
{
    $q = new xenQuery();
    $prefix = xarDBGetSiteTablePrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_carts_configuration";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_carts_configuration (
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

    $query = "DROP TABLE IF EXISTS " . $prefix . "_carts_configuration_group";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_carts_configuration_group (
      configuration_group_id int NOT NULL auto_increment,
      configuration_group_title varchar(64) NOT NULL,
      configuration_group_description varchar(255) NOT NULL,
      sort_order int(5) NULL,
      visible int(1) DEFAULT '1' NULL,
      PRIMARY KEY (configuration_group_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_carts_counter";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_carts_counter (
      startdate char(8),
      counter int(12)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_carts_counter_history";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_carts_counter_history (
      month char(8),
      counter int(12)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_carts_basket";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_carts_basket (
      item_id int NOT NULL auto_increment,
      customer_id int NOT NULL,
      product_id tinytext NOT NULL,
      quantity int(2) NOT NULL,
      price decimal(15,4) NOT NULL,
      date_added int NOT NULL,
      PRIMARY KEY (item_id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_carts_customers_basket_attributes";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_carts_customers_basket_attributes (
      customers_basket_attributes_id int NOT NULL auto_increment,
      customers_id int NOT NULL,
      product_id tinytext NOT NULL,
      products_options_id int NOT NULL,
      products_options_value_id int NOT NULL,
      PRIMARY KEY (customers_basket_attributes_id)
    )";
    if (!$q->run($query)) return;

    # data

# --------------------------------------------------------
#
# Register masks
#
    xarRegisterMask('ViewCartsBlocks','All','carts','Block','All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadCartsBlock','All','carts','Block','All:All:All','ACCESS_READ');
    xarRegisterMask('EditCartsBlock','All','carts','Block','All:All:All','ACCESS_EDIT');
    xarRegisterMask('AddCartsBlock','All','carts','Block','All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteCartsBlock','All','carts','Block','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminCartsBlock','All','carts','Block','All:All:All','ACCESS_ADMIN');
    xarRegisterMask('ViewCarts','All','carts','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadCarts','All','carts','All','All','ACCESS_READ');
    xarRegisterMask('EditCartsBlock','All','carts','Block','All:All:All','ACCESS_EDIT');
    xarRegisterMask('AddCartsBlock','All','carts','Block','All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteCartsBlock','All','carts','Block','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminCarts','All','carts','All','All','ACCESS_ADMIN');


# --------------------------------------------------------
#
# Set up modvars
#
    xarModVars::set('carts', 'itemsperpage', 20);

# --------------------------------------------------------
#
# Delete block details for this module (for now)
#
    $blocktypes = xarModAPIfunc(
        'blocks', 'user', 'getallblocktypes',
        array('module' => 'carts')
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
            array('modName' => 'carts',
                'blockType' => 'shopping_cart'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'carts',
                'blockType' => 'order_history'))) return;

# --------------------------------------------------------
#
# Register block instances
#
// Put a shopping cart block in the 'right' blockgroup
    $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'carts', 'type'=>'shopping_cart'));
    $rightgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'right'));
    $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                  'name' => 'cartscart',
                                                                  'state' => 0,
                                                                  'groups' => array($rightgroup)));


# --------------------------------------------------------
#
# Configure hooks
#

    // when a whole module is displayed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModRegisterHook('item', 'display', 'GUI', 'carts', 'user', 'displayhook')) return false;
    if (!xarModRegisterHook('item', 'usermenu', 'GUI', 'carts', 'user', 'usermenu'))  return false;

    xarModRegisterHook('module', 'getconfig', 'API','carts', 'admin', 'getconfighook');
    xarModAPIFunc('modules','admin','enablehooks',array('callerModName' => 'commerce', 'hookModName' => 'carts'));

# --------------------------------------------------------
#
# Add this module to the list of installed commerce suite modules
#
    $modules = unserialize(xarModVars::get('commerce', 'ice_modules'));
    $info = xarModGetInfo(xarModGetIDFromName('carts'));
    $modules[$info['name']] = $info['regid'];
    $result = xarModVars::set('commerce', 'ice_modules', serialize($modules));

    // Initialisation successful
    return true;
}

function carts_activate()
{
    return true;
}

/**
 * upgrade the carts module from an old version
 */
function carts_upgrade($oldversion)
{
    switch($oldversion){
        case '0.3.0.1':

    }
// Upgrade successful
    return true;
}

/**
 * delete the carts module
 */
function carts_delete()
{
# --------------------------------------------------------
#
# Remove database tables
#
    $tablenameprefix = xarDBGetSiteTablePrefix() . '_carts_';
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
    xarModDelAllVars('carts');
    xarRemoveMasks('carts');
    xarRemoveInstances('carts');

    // The modules module will take care of all the blocks

# --------------------------------------------------------
#
# Delete block types for this module
#
    $blocktypes = xarModAPIfunc(
        'blocks', 'user', 'getallblocktypes',
        array('module' => 'carts')
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
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'cartscart'));
    if ($blockinfo) {
        if(!xarModAPIFunc('blocks', 'admin', 'delete_instance', array('bid' => $blockinfo['bid']))) return;
    }

# --------------------------------------------------------
#
# Remove this module from the list of commerce modules
#
    $modules = unserialize(xarModVars::get('commerce', 'ice_modules'));
    unset($modules['carts']);
    $result = xarModVars::set('commerce', 'ice_modules', serialize($modules));

    // Delete successful

    return true;
}
# --------------------------------------------------------

?>