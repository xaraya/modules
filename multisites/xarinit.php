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
 	if (($lIsMultisites==1) and ($lIsMaster==1)){
		// this XARAYA is the master, since this var has been created by the master (the DN used to initialize the module), in the new config.php
		// forbidden to initialize this module or forbidden to create it a second time.
		return false;
		}
   // Setup a database table to hold subsites (may or may not need this in the end???)
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $multisitestable = $xartable['multisites'];
    xarDBLoadTableMaintenanceAPI();

    $fields = array(
        'xar_msid'     => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_mssite'   => array('type' => 'varchar', 'size' => 128, 'null' => false),
        'xar_msprefix' => array('type' => 'varchar', 'size' => 15, 'null' => false),
        'xar_msdb'     => array('type' => 'varchar', 'size' => 128, 'null' => false),
        'xar_msshare'  => array('type' => 'varchar', 'size' => 128, 'null' => false, 'default' =>''),
        'xar_msstatus' => array ('type'=>'integer', 'size'=>'tiny', 'null'=>false, 'default'=>'1')
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
    xarModSetVar('multisites', 'sitefolder', 'xarsites');
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
        case 0.1:
            return multisites_upgrade(0.1);
        case 0.2:
            // Code to upgrade from version 1.0 goes here
            return multisites_upgrade(0.2);
            break;
        case 2.0:
            // Code to upgrade from version 2.0 goes here
            break;
    }
    // Update successful
    return true;
}

function multisites_delete() {
global $HTTP_HOST;
// I never get there :-(
// $output = new xarHTML();
// $output->Text(xarConfigGetVar('master'));
// return $output->GetOutput();
// ------

    //Remove the multisites database
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    // adodb does not provide the functionality to abstract table creates
    // across multiple databases.  Xaraya offers the xarDropeTable function
    // contained in the following file to provide this functionality.
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
    xarModDelVar('multisites', 'sitefolder');
    xarModDelVar('multisites', 'themepath');
    xarModDelVar('multisites', 'varpath');
    xarModDelVar('multisites', 'itemsperpage');
    xarModDelVar('multisites', 'itemsperpage');
    xarModDelVar('multisites', 'masterurl');
    xarModDelVar('multisites', 'DNexts');
    // Remove Masks and Instances
    xarRemoveMasks('multisites');
    xarRemoveInstances('multisites');

    // Remove the multisite config.system.php file - must be chmod 666
    // Write back the single site version
  //Check the master config data folder is writable
    $var = is_writeable('./var/config.system.php');
    if ($var == true) {
          // echo "The file is writable";
    } else {
            $msg = xarML('The master config.system.php file  /var/config.system.php is not writeable!\n
                          Please chmod 666 and try again.');
            xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NON-WRITABLE', new DefaultUserException($msg));
    return false;
    }

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
        return false;
     }
     umask($oldumask);
     // Removed


return true;

/*
if 	(trim(xarConfigGetVar('master'))==trim($HTTP_HOST)) {
	// ok
	if (unlink('multisites.php')) {
		copy("config_multisites_sauve.php","config.php");
		xarModDelVar('multisites', 'init');
	    xarModDelVar('multisites', 'master');
		return true;
	} else {
		return false;
		}
	}
else {
	return false;
	}
*/
}
?>
