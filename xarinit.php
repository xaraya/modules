<?php
/*/
 * shopping/xarinit.php 1.00 July 25th 2003 jared_rich@excite.com
 *
 * Shopping Module Initialization File
 *
 * copyright (C) 2003 by Jared Rich
 * license GPL <http://www.gnu.org/licenses/gpl.html>
 * author: Jared Rich
/*/

/*/
 * Initialise the shopping module
 * This function is only ever called once during the lifetime of a particular module instance.
 * @return: bool
/*/
function shopping_init()
{
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

     // Get table names into a more useable form
    $orderstable = $xartable['shopping_orders'];
    $detailstable = $xartable['shopping_orders_details'];
    $carttable = $xartable['shopping_cart'];
    $itemstable = $xartable['shopping_items'];
    $picstable = $xartable['shopping_items_pics'];
    $recostable = $xartable['shopping_recommendations'];
    $profilestable = $xartable['shopping_profiles'];

    // Neccessary to create tables
    xarDBLoadTableMaintenanceAPI();
    $prefix = xarDBGetSiteTablePrefix();

    // ********  Define Orders Table Structure and create the table ******** //
    $fields = array('xar_oid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
                    'xar_uid' => array('type' => 'integer', 'null' => false),
                    'xar_ototal' => array('type' => 'float', 'size' => 'double', 'width' => 12, 'decimals' => 2, 'null' => false),
                    'xar_ostatus' => array('type' => 'integer', 'null' => false, 'default' => '0'),
                    'xar_odate' => array('type' => 'date', 'null' => false));
    $sql = xarDBCreateTable($orderstable, $fields);
    if (empty($sql)) return;
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    // Create indexes for the Orders Table
    $index = array('name'      => 'i_' . $prefix . '_shopping_orders_uid',
                   'fields'    => array('xar_uid'),
                   'unique'    => false);
    $sql = xarDBCreateIndex($orderstable, $index);
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_shopping_orders_ostatus',
                   'fields'    => array('xar_ostatus'),
                   'unique'    => false);
    $sql = xarDBCreateIndex($orderstable, $index);
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_shopping_orders_odate',
                   'fields'    => array('xar_odate'),
                   'unique'    => false);
    $sql = xarDBCreateIndex($orderstable, $index);
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    // ********  Define Details Table Structure and create the table ******** //
    $fields = array('xar_oid' => array('type' => 'integer', 'null' => false, 'primary_key' => true),
                    'xar_iid' => array('type' => 'integer', 'null' => false, 'primary_key' => true),
                    'xar_iname' => array('type' => 'varchar', 'size' => 250, 'null' => false),
                    'xar_iprice' => array('type' => 'float', 'size' => 'double', 'width' => 12, 'decimals' => 2, 'null' => false),
                    'xar_iquantity' => array('type' => 'integer', 'null' => false, 'default' => '1'));
    $sql = xarDBCreateTable($detailstable, $fields);
    if (empty($sql)) return;
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    // ********  Define Cart Table Structure and create the table ******** //
    $fields = array('xar_uid' => array('type' => 'integer', 'null' => false, 'primary_key' => true),
                    'xar_iid' => array('type' => 'integer', 'null' => false, 'primary_key' => true),
                    'xar_iquantity' => array('type' => 'integer', 'null' => false, 'default' => '1'),
                    'xar_cstatus' => array('type' => 'integer', 'null' => false, 'default' => '0'));
    $sql = xarDBCreateTable($carttable, $fields);
    if (empty($sql)) return;
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    // Create indexes for the Cart Table
    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_shopping_cart_cstatus',
                   'fields'    => array('xar_cstatus'),
                   'unique'    => false);
    $sql = xarDBCreateIndex($carttable, $index);
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    // ********  Define Items Table Structure and create the table ******** //
    $fields = array('xar_iid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
                    'xar_iname' => array('type' => 'varchar', 'size' => 250, 'null' => false),
                    'xar_iprice' => array('type' => 'float', 'size' => 'double', 'width' => 12, 'decimals' => 2, 'null' => false),
                    'xar_isummary' => array('type' => 'text', 'size' => 'tiny', 'null' => false),
                    'xar_idescription' => array('type' => 'text', 'size' => 'long', 'null' => false),
                    'xar_istatus' => array('type' => 'integer', 'null' => false),
                    'xar_istock' => array('type' => 'integer', 'null' => false, 'size' => 'big'),
                    'xar_idate' => array('type' => 'date', 'null' => false),
                    'xar_ibuys' => array('type' => 'integer', 'size' => 'big', 'null' => false));
    $sql = xarDBCreateTable($itemstable, $fields);
    if (empty($sql)) return;
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    // Create indexes for the Items Table
    $index = array('name'      => 'i_' . $prefix . '_shopping_items_iname',
                   'fields'    => array('xar_iname'),
                   'unique'    => true);
    $sql = xarDBCreateIndex($itemstable, $index);
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_shopping_items_istatus',
                   'fields'    => array('xar_istatus'),
                   'unique'    => false);
    $sql = xarDBCreateIndex($itemstable, $index);
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_shopping_items_idate',
                   'fields'    => array('xar_idate'),
                   'unique'    => false);
    $sql = xarDBCreateIndex($itemstable, $index);
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_shopping_items_ibuys',
                   'fields'    => array('xar_ibuys'),
                   'unique'    => false);
    $sql = xarDBCreateIndex($itemstable, $index);
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    // ********  Define Pics Table Structure and create the table ******** //
    $fields = array('xar_iid' => array('type' => 'integer', 'null' => false, 'primary_key' => true),
                    'xar_ipic' => array('type' => 'varchar', 'size' => 250, 'null' => false, 'primary_key' => true));
    $sql = xarDBCreateTable($picstable, $fields);
    if (empty($sql)) return;
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    // ********  Define Recos Table Structure and create the table ******** //
    $fields = array('xar_rid' =>  array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
                    'xar_uname' =>  array('type' => 'varchar', 'size' => 250, 'null' => false),
                    'xar_iid1' => array('type' => 'integer', 'null' => false),
                    'xar_iid2' => array('type' => 'integer', 'null' => false));
    $sql = xarDBCreateTable($recostable, $fields);
    if (empty($sql)) return;
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    // ********  Define Profiles Table Structure and create the table ******** //
    $fields = array('xar_uid'     => array('type' => 'integer', 'null' => false, 'primary_key' => true),
                    'xar_pstatus' => array('type' => 'integer', 'null' => false, 'default' => '0'),
                    'xar_sstreet' => array('type' => 'varchar', 'size' => 250, 'null' => false),
                    'xar_scity'   => array('type' => 'varchar', 'size' => 100, 'null' => false),
                    'xar_sstate'  => array('type' => 'varchar', 'size' => 2, 'null' => false),
                    'xar_szip'    => array('type' => 'varchar', 'size' => 5, 'null' => false),
                    'xar_bstreet' => array('type' => 'varchar', 'size' => 250, 'null' => false),
                    'xar_bcity'   => array('type' => 'varchar', 'size' => 100, 'null' => false),
                    'xar_bstate'  => array('type' => 'varchar', 'size' => 2, 'null' => false),
                    'xar_bzip'    => array('type' => 'varchar', 'size' => 5, 'null' => false),
                    'xar_paymethod' => array('type' => 'integer', 'null' => false, 'default' => '0'),
                    'xar_ccnum'   => array('type' => 'varchar', 'size' => 20, 'null' => true),
                    'xar_ccname'  => array('type' => 'varchar', 'size' => 50, 'null' => true),
                    'xar_ccexp'    => array('type' => 'varchar', 'size' => 10, 'null' => true),
                    'xar_cctype'  => array('type' => 'integer', 'null' => true));
    $sql = xarDBCreateTable($profilestable, $fields);
    if (empty($sql)) return;
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    // Create indexes for the Profiles Table
    $index = array('name'      => 'i_' . $prefix . '_shopping_profiles_paymethod',
                   'fields'    => array('xar_paymethod'),
                   'unique'    => false);
    $sql = xarDBCreateIndex($profilestable, $index);
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    $index = array('name'      => 'i_' . $prefix . '_shopping_profiles_cctype',
                   'fields'    => array('xar_cctype'),
                   'unique'    => false);
    $sql = xarDBCreateIndex($profilestable, $index);
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    // Create example categories -- should be customized by admin later
    if (xarModIsAvailable('categories')) {
        // create the main category
        $shoppingcid = xarModAPIFunc('categories',
                                     'admin',
                                     'create',
                                     array('name' => 'Shopping',
                                           'description' => '',
                                           'parent_id' => 0));
        // create the subcategories
        $featurecid = xarModAPIFunc('categories',
                                    'admin',
                                    'create',
                                    array('name' => "Featured",
                                          'description' => "Featured items",
                                          'parent_id' => $shoppingcid));
        $bookscatcid = xarModAPIFunc('categories',
                                     'admin',
                                     'create',
                                     array('name' => "Books",
                                           'description' => "Types of books for sale",
                                           'parent_id' => $shoppingcid));
        $dvdcatcid = xarModAPIFunc('categories',
                                     'admin',
                                     'create',
                                     array('name' => "DVD",
                                           'description' => "Types of DVD's for sale",
                                           'parent_id' => $shoppingcid));
        $musiccatcid = xarModAPIFunc('categories',
                                     'admin',
                                     'create',
                                     array('name' => "Music",
                                           'description' => "Types of CD's for sale",
                                           'parent_id' => $shoppingcid));


        // create subcategories for subcategories
        // subcats for Books
        $bookscats = array();
        $bookscats[] = array('name' => "Science Fiction / Fantasy",
                             'description' => "Science Fiction / Fantasy Genre");
        $bookscats[] = array('name' => "Information Technology",
                             'description' => "IT Genre");
        $bookscats[] = array('name' => "Biography",
                             'description' => "Biography Genre");
        foreach($bookscats as $subcat) {
            $bookscatsubcid = xarModAPIFunc('categories',
                                             'admin',
                                             'create',
                                             array('name' => $subcat['name'],
                                                   'description' => $subcat['description'],
                                                   'parent_id' => $bookscatcid));
        }
        // subcats for DVD
        $dvdcats = array();
        $dvdcats[] = array('name' => "Science Fiction",
                           'description' => "Science Fiction Films");
        $dvdcats[] = array('name' => "Horror",
                           'description' => "Horror Films");
        $dvdcats[] = array('name' => "Action",
                           'description' => "Action Films");
        foreach($dvdcats as $subcat) {
            $dvdcatsubcid = xarModAPIFunc('categories',
                                               'admin',
                                               'create',
                                               array('name' => $subcat['name'],
                                                 'description' => $subcat['description'],
                                                 'parent_id' => $dvdcatcid));
        }
        // subcats for Music
        $musiccats = array();
        $musiccats[] = array('name' => "Hard Rock",
                                 'description' => "Hard Rock CD's");
        $musiccats[] = array('name' => "Alternative",
                                 'description' => "Alternative CD's");
        $musiccats[] = array('name' => "Pop",
                                 'description' => "Pop CD's");
        foreach($musiccats as $subcat) {
            $musiccatsubcid = xarModAPIFunc('categories',
                                               'admin',
                                               'create',
                                               array('name' => $subcat['name'],
                                                 'description' => $subcat['description'],
                                                 'parent_id' => $musiccatcid));
        }

        // set some category vars
        xarModSetVar('shopping', 'number_of_categories', 1);
        xarModSetVar('shopping', 'mastercids', $shoppingcid);
        xarModSetVar('shopping', 'featurecat', $featurecid);
     }

    // Set up module variables
    if (file_exists('modules/shopping/xarvars.php')) {
        include 'modules/shopping/xarvars.php';
    } else {
      return;
    }

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName' => 'shopping',
                             'blockType' => 'shoppingcart'))) return;

    //if (!xarModAPIFunc('blocks',
    //                   'admin',
    //                   'register_block_type',
    //                   array('modName' => 'shopping',
    //                         'blockType' => 'shoppingrecos'))) return;

    // Register hooks
    if (!xarModRegisterHook('item', 'search', 'GUI',
                            'shopping', 'user', 'search')) {
        return false;
    }

    // Enable hooks
    // Enable shopping hooks for search
    if (xarModIsAvailable('search')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'search', 'hookModName' => 'shopping'));
    }

    // Enable categories hooks for shopping
    if (xarModIsAvailable('categories')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'shopping', 'hookModName' => 'categories'));
    }

    // Enable comments hooks for shopping
    if (xarModIsAvailable('comments')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'shopping', 'hookModName' => 'comments'));
    }
    // Enable hitcount hooks for shopping
    if (xarModIsAvailable('hitcount')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'shopping', 'hookModName' => 'hitcount'));
    }
    // Enable ratings hooks for shopping
    if (xarModIsAvailable('ratings')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'shopping', 'hookModName' => 'ratings'));
    }

// TODO :: register BL tags

    // Define instances
    // Insatnces for Orders
    $sql1 = "SELECT DISTINCT xar_oid FROM $orderstable";
    $sql2 = "SELECT DISTINCT xar_uid FROM $orderstable";
    $instances = array(array('header' => 'Order ID:',
                             'query' => $sql1,
                             'limit' => 20),
                       array('header' => 'User ID:',
                             'query' => $sql2,
                             'limit' => 20));
    xarDefineInstance('shopping', 'Orders', $instances);

    // Instances for Items
    $sql1 = "SELECT DISTINCT xar_iid FROM $itemstable";
    $sql2 = "SELECT DISTINCT xar_iname FROM $itemstable";
    $instances = array(array('header' => 'Item ID:',
                             'query' => $sql1,
                             'limit' => 20),
                       array('header' => 'Item Name:',
                             'query' => $sql2,
                             'limit' => 20));
    xarDefineInstance('shopping', 'Items', $instances);

    // Instances for Recos
    $sql1 = "SELECT DISTINCT xar_rid FROM $recostable";
    $sql2 = "SELECT DISTINCT xar_uid FROM $recostable";
    $instances = array(array('header' => 'Recommendation ID:',
                             'query' => $sql1,
                             'limit' => 20),
                       array('header' => 'User ID:',
                             'query' => $sql2,
                             'limit' => 20));
    xarDefineInstance('shopping', 'Recommendations', $instances);

    // Instances for Blocks
    $sql = "SELECT DISTINCT instances.xar_title FROM xar_block_instances as instances LEFT JOIN xar_block_types as btypes ON btypes.xar_id = instances.xar_type_id WHERE xar_module = 'shopping'";
    $instances = array(array('header' => 'Block Title:',
                             'query' => $sql,
                             'limit' => 20));
    xarDefineInstance('shopping','Blocks',$instances);

    // Register masks
    // Global Masks
    xarRegisterMask('ViewShopping', 'All', 'shopping', 'All', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('EditShopping', 'All', 'shopping', 'All', 'All', 'ACCESS_EDIT');
    xarRegisterMask('AdminShopping', 'All', 'shopping', 'All', 'All', 'ACCESS_ADMIN');
    // Masks for Orders
    xarRegisterMask('ReadShoppingOrders', 'All', 'shopping', 'Orders', 'All:All', 'ACCESS_READ');
    xarRegisterMask('SubmitShoppingOrders', 'All', 'shopping', 'Orders', 'All:All', 'ACCESS_COMMENT');
    xarRegisterMask('EditShoppingOrders', 'All', 'shopping', 'Orders', 'All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddShoppingOrders', 'All', 'shopping', 'Orders', 'All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteShoppingOrders', 'All', 'shopping', 'Orders', 'All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminShoppingOrders', 'All', 'shopping', 'Orders', 'All:All', 'ACCESS_ADMIN');
    // Masks for Items
    xarRegisterMask('ViewShoppingItems', 'All', 'shopping', 'Items', 'All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadShoppingItems', 'All', 'shopping', 'Items', 'All:All', 'ACCESS_READ');
    xarRegisterMask('EditShoppingItems', 'All', 'shopping', 'Items', 'All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddShoppingItems', 'All', 'shopping', 'Items', 'All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteShoppingItems', 'All', 'shopping', 'Items', 'All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminShoppingItems', 'All', 'shopping', 'Items', 'All:All', 'ACCESS_ADMIN');
    // Masks for Recos
    xarRegisterMask('ViewShoppingRecos', 'All', 'shopping', 'Recommendations', 'All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('SubmitShoppingRecos', 'All', 'shopping', 'Recommendations', 'All:All', 'ACCESS_COMMENT');
    xarRegisterMask('DeleteShoppingRecos', 'All', 'shopping', 'Recommendations', 'All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminShoppingRecos', 'All', 'shopping', 'Recommendations', 'All:All', 'ACCESS_ADMIN');
    // Masks for Blocks
    xarRegisterMask('ReadShoppingBlocks', 'All', 'shopping', 'Blocks', 'All', 'ACCESS_READ');
    xarRegisterMask('AdminShoppingBlocks', 'All', 'shopping', 'Blocks', 'All', 'ACCESS_ADMIN');

    return true;
}

/*/
 * Upgrade the shopping module from an old version
 * This function can be called multiple times
 * @return: bool
/*/
function shopping_upgrade($oldversion)
{
    switch ($oldversion) {
        case '0.5':
            // Code to upgrade from version 0.5 goes here
            break;
        case '1.0.0':
            // Code to upgrade from version 1.0.0 goes here
            break;
        case '2.0.0':
            // Code to upgrade from version 2.0.0 goes here
            break;
    }

    return true;
}

/*/
 * Delete the shopping module
 * This function is only ever called once during the lifetime of a particular module instance
 * @return: bool
/*/
function shopping_delete()
{
    // Drop tables
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Neccessary to drop tables
    xarDBLoadTableMaintenanceAPI();

    // Drop the Orders Table
    $sql = xarDBDropTable($xartable['shopping_orders']);
    if (empty($sql)) return;
    $result =& $dbconn->Execute($sql);
    if (!$result) return;
    // Drop the Details Table
    $sql = xarDBDropTable($xartable['shopping_orders_details']);
    if (empty($sql)) return;
    $result =& $dbconn->Execute($sql);
    if (!$result) return;
    // Drop the Cart Table
    $sql = xarDBDropTable($xartable['shopping_cart']);
    if (empty($sql)) return;
    $result =& $dbconn->Execute($sql);
    if (!$result) return;
    // Drop the Items Table
    $sql = xarDBDropTable($xartable['shopping_items']);
    if (empty($sql)) return;
    $result =& $dbconn->Execute($sql);
    if (!$result) return;
    // Drop the Pics Table
    $sql = xarDBDropTable($xartable['shopping_items_pics']);
    if (empty($sql)) return;
    $result =& $dbconn->Execute($sql);
    if (!$result) return;
    // Drop the Recos Table
    $sql = xarDBDropTable($xartable['shopping_recommendations']);
    if (empty($sql)) return;
    $result =& $dbconn->Execute($sql);
    if (!$result) return;
    // Drop the Profiles Table
    $sql = xarDBDropTable($xartable['shopping_profiles']);
    if (empty($sql)) return;
    $result =& $dbconn->Execute($sql);
    if (!$result) return;

   // Delete Module Vars
   xarModDelAllVars('shopping');

   /* Remove entries from category linkage table
   if (xarModIsAvailable('categories')) {
       $catlinktable = 'xar_categories_linkage';
       $modulestable = $xartable['modules'];
       $sql = "DELETE FROM $catlinktable, $modulestable
               WHERE xar_regid = xar_modid AND
                     xar_name = 'shopping'";
       $result = &$dbconn->Execute($sql);
       if (!$result) return;
   }
   */

   // Unregister blocks
   if (!xarModAPIFunc('blocks',
                      'admin',
                      'unregister_block_type',
                      array('modName'  => 'shopping',
                            'blockType'=> 'shoppingcart'))) return false;

   if (!xarModAPIFunc('blocks',
                      'admin',
                      'unregister_block_type',
                      array('modName'  => 'shopping',
                            'blockType'=> 'shoppingrecos'))) return false;

   // Unregister hooks
   if (!xarModUnregisterHook('item', 'search', 'GUI',
                             'shopping', 'user', 'search')) return false;

   // Remove instances and masks
   xarRemoveInstances('shopping');
   xarRemoveMasks('shopping');

   return true;
}
?>
