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



// multisites is just initialized here. the admin will have to go only once in the admin menu to continue the installation. He will not be able to modify anything once installed. His only solution will be to deactivate / remove this module, then install it again.


function multisites_init()
	{
    xarModSetVar('multisites', 'servervar', 'httphost');

	$lIsMultisites = xarConfigGetVar('multiSites');
	if ($lIsMultisites and (!(xarConfigGetVar('master')))) {
		// this XARAYA is not the master, since this var has been created by the master (the DN used to initialize the module), in the new config.php
		// forbidden to initialize this module or forbidden to create it a second time.
		return false;
		}

    
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
