<?php
/**
 * File: $Id$
 * 
 * Paid Membership table definitions function
 * 
 * @copyright (C) 2003 by the Wyome Consulting, LLC
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.wyome.com
 * @subpackage pmember
 * @author John Cox <john.cox@wyome.com>
 */
/**
 * initialise the pmember module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function pmember_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['pmember'];
    xarDBLoadTableMaintenanceAPI();
    $query = xarDBCreateTable($xartable['pmember'],
                             array('xar_uid'        => array('type'         => 'integer',
                                                            'null'          => false,
                                                            'increment'     => false,
                                                            'primary_key'   => true),
                                   'xar_subscribed' => array('type'         => 'integer',
                                                            'unsigned'      => TRUE,
                                                            'null'          => FALSE,
                                                            'default'       => '0'),
                                   'xar_expires'    => array('type'         => 'integer',
                                                            'unsigned'      => TRUE,
                                                            'null'          => FALSE,
                                                            'default'       => '0')
                                  ));

    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // allow several entries for the same keyword here
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_pmember_uid',
        'fields'    => array('xar_uid'),
        'unique'    => true
    );
    $query = xarDBCreateIndex($table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    // Default group for membership
    $defaultgroup = xarModGetVar('roles', 'defaultgroup');
    xarModSetVar('pmember', 'defaultgroup', $defaultgroup);
    xarModSetVar('pmember', 'sendmail', FALSE);
    xarModSetVar('pmember', 'typeoffee', 'subscription');
    xarModSetVar('pmember', 'period', 30);
    xarModSetVar('pmember', 'time', 1);
    xarModSetVar('pmember', 'price', 20);
    xarModSetVar('pmember', 'message', '');
    xarModSetVar('pmember', 'benefits', '');

    // Set up the hooks
    if (!xarModRegisterHook('item', 'create', 'API',
                           'pmember', 'admin', 'createhook')) {
        return false;
    }

    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'pmember', 'user', 'usermenu')) {
        return false;
    }

    xarModAPIFunc('modules', 'admin', 'enablehooks',
        array('callerModName' => 'paypalipn', 'hookModName' => 'pmember'));

    xarModAPIFunc('modules', 'admin', 'enablehooks',
        array('callerModName' => 'roles', 'hookModName' => 'pmember'));

    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
        array('modName' => 'pmember', 'blockType' => 'subscription'))) return;

    xarRegisterMask('AdminPMember', 'All', 'pmember', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    xarRegisterMask('ViewPMember', 'All', 'pmember', 'Item', 'All:All:All', 'ACCESS_READ');
    // Initialisation successful
    return true;
}

/**
 * upgrade the pmember module from an old version
 * This function can be called multiple times
 */
function pmember_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.0':
        case '1.0.0':

            break;
    }
    // Update successful
    return true;
}
/**
 * delete the pmember module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function pmember_delete()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['pmember']);
    if (empty($query)) return; // throw back
    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    if (!xarModUnregisterHook('item', 'create', 'API', 'pmember', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI', 'pmember', 'user', 'usermenu')) {
        return false;
    }
    // UnRegister blocks
    if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type', array('modName'  => 'pmember',  'blockType'=> 'subscription'))) return;

    // Remove Masks and Instances
    xarRemoveMasks('pmember');
    xarRemoveInstances('pmember'); 
    xarModDelAllVars('pmember');;
    // Deletion successful
    return true;
}
?>