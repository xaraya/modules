<?php
/**
 * Sharecontent Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage sharecontent Module
 * @link http://xaraya.com/index.php/release/894.html
 * @author Andrea Moro
 */
/**
 * initialise the sharecontent module
 */
function sharecontent_init()
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
        'xar_active' => array('type' => 'boolean', 'null'=>false, 'default' => '1')
        );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($xartable['sharecontent'], $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and 
	// send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Load the initial setup of the publication types
    if (file_exists('modules/sharecontent/xarsetup.php')) {
        include 'modules/sharecontent/xarsetup.php';
    } else {
        // TODO: add some defaults here
        $websites= array();
    }

    // Save  websites
    foreach ($websites as $website) {
        list($title,$homeurl,$submiturl,$image,$active) = $website;
        $nextId = $dbconn->GenId($xartable['sharecontent']);
        $query = "INSERT INTO $xartable[sharecontent] (xar_id,xar_title, xar_homeurl, xar_submiturl, xar_image,xar_active) VALUES (?,?,?,?,?,?)";
        $bindvars = array($nextId,$title,$homeurl,$submiturl,$image,$active);
        $result =& $dbconn->Execute($query,$bindvars);
        if (!$result)  sharecontent_delete();
    }

    // Set up module variables
    xarModSetVar('sharecontent', 'enablemail', '0');
    xarModSetVar('sharecontent', 'maxemails', '1');
    xarModSetVar('sharecontent', 'htmlmail', '0');
    xarModSetVar('sharecontent', 'bcc', '');

    // Set up module hooks
    if (!xarModRegisterHook('item',
            'display',
            'GUI',
            'sharecontent',
            'user',
            'display')) {
        return false;
    }

	// define instances
	$query = "SELECT DISTINCT xar_smodule FROM $xartable[hooks] WHERE xar_tmodule='sharecontent'  ";
	$instances = array( array('header'=>'Hooked module','query'=>$query,'limit'=>20));
    xarDefineInstance('sharecontent', 'Web', $instances);
    xarDefineInstance('sharecontent', 'Mail', $instances);

    // Register the module components that are privileges objects
    // Format: xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    xarRegisterMask('ReadSharecontentWeb', 'All', 'sharecontent', 'Web', 'All', 'ACCESS_READ');
    xarRegisterMask('SendSharecontentMail', 'All', 'sharecontent', 'Mail', 'All', 'ACCESS_COMMENT');
    xarRegisterMask('AdminSharecontent', 'All', 'sharecontent', 'All', 'All', 'ACCESS_ADMIN');

    // Initialisation successful
	// run upgrades
	if (sharecontent_upgrade('0.9.3')) {;
       return true;
	} else {
	   return false;
    }
}

/**
 * upgrade the sharecontent module from an old version
 * @param string oldversion
 * @return bool true on success of upgrade
 */
function sharecontent_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
	    case '0.9.2':
            xarModSetVar('sharecontent', 'bcc', '');
		case '0.9.3':
            // Pass the Table Create DDL to adodb to create the table and 
        	// send exception if unsuccessful
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
        
            // Load the initial setup of the publication types
            if (file_exists('modules/sharecontent/xarsetup.php')) {
                include 'modules/sharecontent/xarsetup.php';
            } else {
                // TODO: add some defaults here
                $websites= array();
            }
        
            // Save  websites
            foreach ($websites2 as $website) {
                list($title,$homeurl,$submiturl,$image,$active) = $website;
                $nextId = $dbconn->GenId($xartable['sharecontent']);
                $query = "INSERT INTO $xartable[sharecontent] (xar_id,xar_title, xar_homeurl, xar_submiturl, xar_image,xar_active) VALUES (?,?,?,?,?,?)";
                $bindvars = array($nextId,$title,$homeurl,$submiturl,$image,$active);
                $result =& $dbconn->Execute($query,$bindvars);
                if (!$result)  sharecontent_delete();
            }
    }

    return true;
}

/**
 * delete the sharecontent module
 * @return bool true on successfull deletion
 */
function sharecontent_delete()
{
    // Remove module hooks
    if (!xarModUnregisterHook('item',
            'display',
            'GUI',
            'sharecontent',
            'user',
            'display')) return;

    if (!xarModUnregisterHook('module', 'remove', 'API',
                             'sharecontent', 'admin', 'deleteall')) {
        return;
    }

    // Delete module variables
    xarModDelVar('sharecontent', 'enablemail');
    xarModDelVar('sharecontent', 'maxemails');
    xarModDelVar('sharecontent', 'htmlmail');
    xarModDelVar('sharecontent', 'bcc');

    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    // Delete tables
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['sharecontent']);
    if (empty($query)) return; // throw back
    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // Remove Masks and Instances
    xarRemoveMasks('sharecontent');
    xarRemoveInstances('sharecontent');
    // Deletion successful
    return true;
}

?>
