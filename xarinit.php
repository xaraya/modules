<?php
/**
 * webshare Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage webshare Module
 * @link http://xaraya.com/index.php/release/883.html
 * @author Andrea Moro
 */
/**
 * initialise the webshare module
 */
function webshare_init()
{
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();
    // Create table
    $fields = array('xar_id' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_title' => array('type' => 'varchar', 'size'=>64, 'null' => false),
        'xar_homeurl' => array('type' => 'varchar', 'size'=>128),
        'xar_submiturl' => array('type' => 'varchar', 'size'=>128,'null' => false),
        'xar_image' => array('type' => 'varchar', 'size' => 128),
        'xar_active' => array('type' => 'boolean', 'default' => 'true')
        );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($xartable['webshare'], $fields);
	if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Load the initial setup of the publication types
	if (file_exists('modules/webshare/xarsetup.php')) {
	    include 'modules/webshare/xarsetup.php';
	} else {
		// TODO: add some defaults here
		$websites= array();
	}

    // Save  websites
    foreach ($websites as $website) {
        list($title,$homeurl,$submiturl,$image,$active) = $website;
		$nextId = $dbconn->GenId($xartable['webshare']);
		$query = "INSERT INTO $xartable[webshare] (xar_id,xar_title, xar_homeurl, xar_submiturl, xar_image,xar_active) VALUES (?,?,?,?,?,?)";
	    $bindvars = array($nextId,$title,$homeurl,$submiturl,$image,$active);
        $result =& $dbconn->Execute($query,$bindvars);
	    if (!$result)  webshare_delete();
	}


    // Set up module variables
    xarModSetVar('webshare', 'defaultstyle', 'partialhide');
    // Set up module hooks
    if (!xarModRegisterHook('item',
            'display',
            'GUI',
            'webshare',
            'user',
            'display')) {
        return false;
    }

    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'webshare', 'admin', 'deleteall')) {
        return false;
    }

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ReadWebshareWeb', 'All', 'webshare', 'Web', 'All', 'ACCESS_READ');
    xarRegisterMask('ReadWebshareMail', 'All', 'webshare', 'Mail', 'All', 'ACCESS_READ');
    xarRegisterMask('AdminWebshare', 'All', 'webshare', 'All', 'All', 'ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the webshare module from an old version
 * @param string oldversion
 * @return bool true on success of upgrade
 */
function webshare_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
	    case '0.9':
		    $oldversion = '0.911';
		case '0.91':
		    $oldversion = '0.911';
        case '0.912':
		    xarUnregisterMask('Readwebshare');
		    xarUnregisterMask('Adminwebshare');
            xarRegisterMask('ReadWebshareWeb', 'All', 'webshare', 'Web', 'All', 'ACCESS_READ');
            xarRegisterMask('ReadWebshareMail', 'All', 'webshare', 'Mail', 'All', 'ACCESS_READ');
            xarRegisterMask('AdminWebshare', 'All', 'webshare', 'All', 'All', 'ACCESS_ADMIN');

    }
    return true;
}

/**
 * delete the webshare module
 * @return bool true on successfull deletion
 */
function webshare_delete()
{
    // Remove module hooks
    if (!xarModUnregisterHook('item',
            'display',
            'GUI',
            'webshare',
            'user',
            'display')) return;

    if (!xarModUnregisterHook('module', 'remove', 'API',
                             'webshare', 'admin', 'deleteall')) {
        return;
    }

    // Delete module variables
    xarModDelVar('webshare', 'partialhide');

    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    // Delete tables
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['webshare']);
    if (empty($query)) return; // throw back
    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // Remove Masks and Instances
    xarRemoveMasks('webshare');
    xarRemoveInstances('webshare');
    // Deletion successful
    return true;
}

?>
