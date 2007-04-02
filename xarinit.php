<?php

sys::import('modules.xen.xarclasses.xenquery');
//Load Table Maintainance API
xarDBLoadTableMaintenanceAPI();

function customers_init()
{
    $q = new xenQuery();
    $prefix = xarDBGetSiteTablePrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_customers_address_book";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_customers_address_book (
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

    $query = "DROP TABLE IF EXISTS " . $prefix . "_customers_customers_ip";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_customers_customers_ip (
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

    $query = "DROP TABLE IF EXISTS " . $prefix . "_customers_customers_status";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_customers_customers_status (
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

    $query = "DROP TABLE IF EXISTS " . $prefix . "_customers_customers_status_history";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_customers_customers_status_history (
      customers_status_history_id int(11) NOT NULL auto_increment,
      customers_id int(11) NOT NULL default '0',
      new_value int(5) NOT NULL default '0',
      old_value int(5) default NULL,
      date_added datetime NOT NULL default '0000-00-00 00:00:00',
      customer_notified int(1) default '0',
      PRIMARY KEY  (customers_status_history_id)
    )";
    if (!$q->run($query)) return;

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewCustomers','All','customers','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('AdminCustomers','All','customers','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('AdminCustomers','All','customers','All','All','ACCESS_ADMIN');
    xarMakePrivilegeRoot('AdminCustomers');

# --------------------------------------------------------
#
# Set up modvars
#
    xarModSetVar('customers', 'itemsperpage', 20);

# --------------------------------------------------------
#
# Configure hooks
#
    // This is a GUI hook for the roles module that enhances the roles profile page
    if (!xarModRegisterHook('item', 'usermenu', 'GUI', 'customers', 'user', 'usermenu')) return false;
    xarModAPIFunc('modules', 'admin', 'enablehooks', array('callerModName' => 'roles', 'hookModName' => 'customers'));

    xarModRegisterHook('module', 'getconfig', 'API','customers', 'admin', 'getconfighook');
    xarModAPIFunc('modules','admin','enablehooks',array('callerModName' => 'commerce', 'hookModName' => 'customers'));

# --------------------------------------------------------
#
# Delete block details for this module (for now)
#
    $blocktypes = xarModAPIfunc(
        'blocks', 'user', 'getallblocktypes',
        array('module' => 'customers')
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
            array('modName' => 'customers',
                'blockType' => 'customers_status'))) return;


# --------------------------------------------------------
#
# Set extensions
#

    $dd_objects = array('ice_customers','ice_customer_groups');

    // Treat destructive right now
    $existing_objects  = xarModApiFunc('dynamicdata','user','getobjects');
    foreach($existing_objects as $objectid => $objectinfo) {
        if(in_array($objectinfo['name'], $dd_objects)) {
            // KILL
            if(!xarModApiFunc('dynamicdata','admin','deleteobject', array('objectid' => $objectid))) return;
        }
    }

    $objects = unserialize(xarModVars::get('commerce','dd_objects'));
    foreach($dd_objects as $ice_object) {
        $def_file = 'modules/customers/xardata/'.$ice_object.'-def.xml';
        $dat_file = 'modules/customers/xardata/'.$ice_object.'-data.xml';

        $objectid = xarModAPIFunc('dynamicdata','util','import', array('file' => $def_file));
        if (!$objectid) continue;
        else $objects[$ice_object] = $objectid;
        // Let data import be allowed to be empty
        if(file_exists($dat_file)) {
            // And allow it to fail for now
            xarModAPIFunc('dynamicdata','util','import', array('file' => $dat_file,'keepitemid' => true));
        }
    }

    xarModSetVar('commerce','dd_objects',serialize($objects));

    $role = xarFindRole('Customers');
    if (empty($role)) {
        $parent = xarFindRole('CommerceRoles');
        if (empty($parent)) $parent = xarFindRole('Everybody');
        $new = array('name' => 'Customers',
                     'itemtype' => ROLES_GROUPTYPE,
                     'parentid' => $parent->getID(),
                    );
        $uid1 = xarModAPIFunc('roles','admin','create',$new);
    }

# --------------------------------------------------------
#
# Add this module to the list of installed commerce suite modules
#
    $modules = unserialize(xarModVars::get('commerce', 'ice_modules'));
    $info = xarModGetInfo(xarModGetIDFromName('customers'));
    $modules[$info['name']] = $info['regid'];
    $result = xarModSetVar('commerce', 'ice_modules', serialize($modules));

    return true;
}

function customers_upgrade()
{
    return true;
}

function customers_delete()
{
# --------------------------------------------------------
#
# Purge all the roles created by this module
#
    $role = xarFindRole('Customers');
    $descendants = $role->getDescendants();
    foreach ($descendants as $item)
        if (!$item->purge()) return;
    if (!$role->purge()) return;

# --------------------------------------------------------
#
# Remove this module from the list of commerce modules
#
    $modules = unserialize(xarModVars::get('commerce', 'ice_modules'));
    unset($modules['customers']);
    $result = xarModSetVar('commerce', 'ice_modules', serialize($modules));

    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => 'customers'));
}

?>
