<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Sebastien Bernard
// Purpose of file:  Administration of the multisites system.
// based on the templates developped by Jim McDee.
// ----------------------------------------------------------------------



// multisites is just initialized here. the admin will have to go only once in the admin menu to continue the installation. 
// He will not be able to modify anything once installed. His only solution will be to deactivate / remove this module, then install it again.
//<jojodee>: With xar Multisites you can modify certain configuration vars without removal/installation

function multisites_init()
{
	$lIsMultisites = xarConfigGetVar('System.MS.MultiSites');
	$lIsMaster=xarConfigGetVar('System.MS.Master');
 	if (($lIsMultisites==1) and ($lIsMaster!=2)){ //jojodee: allow 'sub' masters only to initialize - not implemented yet
		// this XARAYA is the master, since this var has been created by the master (the DN used to initialize the module), in the new config.php
		// forbidden to initialize this module or forbidden to create it a second time.
		return false;
   }
   // Setup a database table to hold subsites (may or may not need this in the end???)
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $multisitestable = $xartable['multisites'];
    xarDBLoadTableMaintenanceAPI();

    $fields = array(
        'xar_msid'       => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_mssite'     => array('type' => 'varchar', 'size' => 128, 'null' => false),
        'xar_msprefix'   => array('type' => 'varchar', 'size' => 20, 'null' => false),
        'xar_msdb'       => array('type' => 'varchar', 'size' => 128, 'null' => false),
        'xar_msshare'    => array('type' => 'varchar', 'size' => 20, 'null' => false, 'default' =>''),
        'xar_msstatus'   => array('type' => 'integer', 'size' => 'tiny', 'null'=>false, 'default'=>'1'),
        'xar_sitefolder' => array('type' => 'varchar', 'size' => 128, 'null' => false)
        );

    $query = xarDBCreateTable($multisitestable, $fields);
    if (empty($query)) return;

    $result = &$dbconn->Execute($query);
    if (!$result) return;


   // Set up an intitial value for the config variable
   // If we get to here then this is going to be the master initialization
    xarConfigSetVar('System.MS.MultiSites',1);
    xarConfigSetVar('System.MS.Master',0);
   // Set up an initial value for a module variable.
    xarModSetVar('multisites', 'servervar', 'HTTP_HOST');
    xarModSetVar('multisites', 'masterfolder', 'xarsites');
    xarModSetVar('multisites', 'themepath', 'themes');
    xarModSetVar('multisites', 'varpath', 'var');
    xarModSetVar('multisites', 'itemsperpage', 10);
    xarModSetVar('multisites', 'masterurl','');
    xarModSetVar('multisites', 'DNexts','.com,.net,.org');
   /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ReadMultisites','All','multisites','All','All','ACCESS_READ');
    xarRegisterMask('EditMultisites','All','multisites','All','All','ACCESS_EDIT');
    xarRegisterMask('AddMultisites','All','multisites','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteMultisites','All','multisites','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminMultisites','All','multisites','All','All','ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

function multisites_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.1':
            break;
        case '0.2':
            // Code to upgrade from version 1.0 goes here
            break;
        case '0.21':
        case '0.21.0':
            break;
        case '2.0.0':
            // Code to upgrade from version 2.0 goes here
            break;
    }
    // Update successful
    return true;
}

function multisites_delete() {
global $HTTP_HOST;

    //Remove the multisites database
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['multisites']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;


    //Reset configvars
    xarConfigSetVar('System.MS.MultiSites',0);
    xarConfigSetVar('System.MS.Master',0);
    //Delete module vars
    xarModDelVar('multisites', 'servervar');
    xarModDelVar('multisites', 'masterfolder');
    xarModDelVar('multisites', 'themepath');
    xarModDelVar('multisites', 'varpath');
    xarModDelVar('multisites', 'itemsperpage');
    xarModDelVar('multisites', 'itemsperpage');
    xarModDelVar('multisites', 'masterurl');
    xarModDelVar('multisites', 'DNexts');
    // Remove Masks and Instances
    xarRemoveMasks('multisites');
    xarRemoveInstances('multisites');

     // Write back the single site version
    //Check the master config data folder is writable
    //if so return it to normal state else send a message.
    $var = is_writeable('./var/config.system.php');
    if ($var == true) {
        $oldConfig=file('./var/config.system.php');
        $fd = fopen('./var/config.system.php','r');
        while (list ($line_num, $line) = each ($oldConfig)) {
            if ((strstr($line,"<?php")) ||
                (strstr($line,"?>")) ||
                (strstr($line,"GLOBALS")) ||
                (strstr($line,"file_exists")) ||
                (strstr($line,"else")) ||
                (strstr($line,"}")) ||
                (strstr($line,"Multisites")) ||
                (strstr($line,"'MS."))) {
           // do nothing
            }else{
              $holdConfig[$line_num]=$oldConfig[$line_num];
            }
         }
         fclose($fd);

        //Create the new config data for the master site
        $newConf=file('./var/config.system.php');
        $oldumask = umask(0);
        $IOk=fopen('./var/config.system.php','w');
        if ($IOk) {
            fwrite($IOk, "<?php\n");
            while (list ($line_num, $line) = each($holdConfig)) {
                fwrite($IOk,$line);
        }
            fwrite($IOk, "?>\n");
            fclose($IOk);
        } else {
        //echo "Can't modify the old config file";
        //return false; No - let uninstall complete
        }
        umask($oldumask);
    } else {
          $msg = xarML("Could not write /var/config.system.php! Please manually copy back your original single site config.system.php file!");
          xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NON-WRITEABLE', new DefaultUserException($msg));
          return $msg;
    }

return true;

}
?>
