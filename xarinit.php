<?php
/**
 * xarcpshop initialization functions
 *
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * initialise the xarcpshop module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xarcpshop_init()
{

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    //Create the Stores Table
    $cpstorestable = $xartable['cpstores'];
    $fields = array(
             'xar_storeid' => array('type'       => 'integer',
                                   'size'       => 10,
                                   'null'       => false,
                                   'increment'  => true,
                                   'primary_key' => true),
               'xar_name' => array('type'      => 'varchar',
                                   'size'       => 32,
                                   'null'       => false,
                                   'default'    => ''),
         'xar_toplevel'=> array('type'       => 'varchar',
                                   'size'       => 32,
                                   'null'       => false,
                                   'default'    =>''),
         'xar_nickname'=> array('type'       => 'varchar',
                                   'size'       => 64,
                                   'null'       => false,
                                   'default'    =>''),
             'xar_tid'    => array('type'       => 'integer',
                                   'size'       => '10',
                                   'null'       => false,
                                   'default'    => '0')
    );

    $query = xarDBCreateTable($cpstorestable, $fields);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    //Create the Items Table
    $cpitemstable = $xartable['cpitems'];
    $fields = array(
          'xar_itemid'    => array('type'       => 'varchar',
                                   'size'       => '64',
                                   'null'       => false,
                                   'primary_key'=> true),
            'xar_type'    => array('type'      => 'varchar',
                                   'size'      => '8',
                                   'null'       => false,
                                   'default'   => 'product'),
           'xar_title'    => array('type'       => 'varchar',
                                   'size'       => '255',
                                   'null'       => false,
                                   'default'    =>''),
         'xar_content'    => array('type'       => 'text',
                                   'null'       => false,
                                   'default'    => ''),
          'xar_parent'    => array('type'       => 'varchar',
                                   'size'       => '64',
                                   'null'       => false,
                                   'default'    => '0'),
      'xar_prodtypeid'    => array('type'       => 'integer',
                                   'size'       => '10',
                                   'null'       => false,
                                   'default'    => '0'),
           'xar_price'    => array('type'       => 'varchar',
                                   'size'       => '16',
                                   'null'       => false,
                                   'default'    => '0'),
          'xar_rating'    => array('type'       => 'varchar',
                                    'size'      => '3',
                                   'default'    => 'PG'),
           'xar_md5'    => array('type'       => 'varchar',
                                   'size'       => '32',
                                   'null'       => false,
                                   'default'    => '0'),
        'xar_disabled'    => array('type'       => 'integer',
                                   'size'       => 'tiny',
                                   'null'       => false,
                                   'default'    => '0'),
           'xar_storeid' => array('type'       => 'varchar',
                                   'size'       => 128,
                                   'null'       => false,
                                   'default'    => ''),
         'xar_mainimg'    => array('type'       => 'varchar',
                                   'size'       => '32',
                                   'null'       => false,
                                   'default'    => ''),
           'xar_update'   => array('type'       => 'integer',
                                   'size'       => 'tiny',
                                   'default'    => '0')
    );
    $query = xarDBCreateTable($cpitemstable, $fields);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $cptypestable = $xartable['cptypes'];
    $fields = array(
      'xar_prodtypeid'    => array('type'       => 'integer',
                                   'size'       => '10',
                                   'null'       => false,
                                   'default'    => '0',
                                   'primary_key'=> true),
        'xar_prodtype'    => array('type'      => 'varchar',
                                   'size'       => 128,
                                   'null'       => false,
                                   'default'    => ''),
     'xar_description'    => array('type'       => 'text',
                                   'null'       => false,
                                   'default'    =>'')
    );
    $query = xarDBCreateTable($cptypestable, $fields);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $path = xarCoreGetVarDirPath();
    xarModSetVar('xarcpshop', 'itemsperpage', 10);
    xarModSetVar('xarcpshop', 'SupportShortURLs', 0);
    xarModSetVar('xarcpshop', 'closed', 0);
    xarModSetVar('xarcpshop', 'cpdown', 0);
    xarModSetVar('xarcpshop', 'breadcrumb', 1);
    xarModSetVar('xarcpshop', 'litemode', 0);
    xarModSetVar('xarcpshop', 'defaultstore', 1);
    xarModSetVar('xarcpshop', 'localhtml', "{$path}/cp/html/");
    xarModSetVar('xarcpshop', 'localimages', "{$path}/cp/images/");
    xarModSetVar('xarcpshop', 'cart', 'cp');
    xarModSetVar('xarcpshop', 'verbose', 1);
    xarModSetVar('xarcpshop', 'sectionthumbmaxsize', '200');


    // Register our hooks that we are providing to other modules.  The xarcpshop
    // module shows an xarcpshop hook in the form of the user menu.
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'xarcpshop', 'user', 'usermenu')) {
        return false;
    }
    if (!xarModAPIFunc('blocks',
                      'admin',
            'register_block_type',
            array('modName' => 'xarcpshop',
                'blockType' => 'cpfeature'))) return;
    if (!xarModAPIFunc('blocks',
                      'admin',
            'register_block_type',
            array('modName' => 'xarcpshop',
                'blockType' => 'cprandom'))) return;

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ReadxarCPShopBlock', 'All', 'xarcpshop', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewxarCPShop', 'All', 'xarcpshop', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadxarCPShop', 'All', 'xarcpshop', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditxarCPShop', 'All', 'xarcpshop', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddxarCPShop', 'All', 'xarcpshop', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeletexarCPShop', 'All', 'xarcpshop', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminxarCPShop', 'All', 'xarcpshop', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * upgrade the xarcpshop module from an old version
 * This function can be called multiple times
 */
function xarcpshop_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '0.0.1':
            return xarcpshop_upgrade('0.1.0');
        case '0.1.0':
            // Code to upgrade from version 0.1.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the xarcpshop module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xarcpshop_delete()
{
   $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
        // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['cpstores']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBDropTable($xartable['cpitems']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBDropTable($xartable['cptypes']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;
  if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'xarcpshop',
                'blockType' => 'cpfeature'))) return;

      // Delete any module variables
    xarModDelAllVars('xarcpshop');

      // Remove module hooks
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'xarcpshop', 'user', 'usermenu')) {
        return false;
    }
    // Remove Masks and Instances
    xarRemoveMasks('xarcpshop');
    xarRemoveInstances('xarcpshop');

    // Deletion successful
    return true;
}

?>
