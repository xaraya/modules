<?php
/**
 *
 * Function init
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Maps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  Initialization routine for this module
 *
 * @param to be added
 * @return to be added
 *
 */

include_once 'modules/xen/xarclasses/xenobject.class.php';

function maps_init()
{
# --------------------------------------------------------
#
# Create database tables
#

    $q = new xenQuery();
	$prefix = xarDBGetSiteTablePrefix();
	$query = "DROP TABLE IF EXISTS " . $prefix . "_maps_directory";
	if (!$q->run($query)) return;

	$query = "CREATE TABLE " . $prefix . "_maps_directory (
	  id int(11) NOT NULL auto_increment,
	  postal_code varchar(255) NOT NULL default '',
	  latitude varchar(255) NOT NULL default '',
	  longitude varchar(255) NOT NULL default '',
	  city varchar(255) NOT NULL default '',
	  state varchar(255) NOT NULL default '',
	  county varchar(255) NOT NULL default '',
	  pc_class varchar(255) NOT NULL default '',
	PRIMARY KEY  (id)
	) TYPE=MyISAM";
	if (!$q->run($query)) return;

// Load the data as a csv import using phpmyadmin for mysql. It's a lot faster
/*	include "modules/maps/xardata/maps_directory-dat.php";
	$rows = maps_dat1();
	foreach ($rows as $row) {
		if (!$q->run($row)) return;
	}
	$rows = maps_dat2();
	foreach ($rows as $row) {
		if (!$q->run($row)) return;
	}
*/
# --------------------------------------------------------
#
# Create DD objects
#
    $module = 'maps';
    $objects = array(
					 'maps_locations',
					 );
    if(!xarModAPIFunc('xen','admin','install',array('module' => $module, 'objects' => $objects))) return;

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewMaps','All','maps','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadMaps','All','maps','All','All','ACCESS_READ');
    xarRegisterMask('CommentMaps','All','maps','All','All','ACCESS_COMMENT');
    xarRegisterMask('ModerateMaps','All','maps','All','All','ACCESS_MODERATE');
    xarRegisterMask('EditMaps','All','maps','All','All','ACCESS_EDIT');
    xarRegisterMask('AddMaps','All','maps','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteMaps','All','maps','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminMaps','All','maps','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('ViewMaps','All','maps','All','All','ACCESS_OVERVIEW');
    xarRegisterPrivilege('ReadMaps','All','maps','All','All','ACCESS_READ');
    xarRegisterPrivilege('CommentMaps','All','maps','All','All','ACCESS_COMMENT');
    xarRegisterPrivilege('ModerateMaps','All','maps','All','All','ACCESS_MODERATE');
    xarRegisterPrivilege('EditMaps','All','maps','All','All','ACCESS_EDIT');
    xarRegisterPrivilege('AddMaps','All','maps','All','All','ACCESS_ADD');
    xarRegisterPrivilege('DeleteMaps','All','maps','All','All','ACCESS_DELETE');
    xarRegisterPrivilege('AdminMaps','All','maps','All','All','ACCESS_ADMIN');
    xarMakePrivilegeRoot('ViewMaps');
    xarMakePrivilegeRoot('ReadMaps');
    xarMakePrivilegeRoot('CommentMaps');
    xarMakePrivilegeRoot('ModerateMaps');
    xarMakePrivilegeRoot('EditMaps');
    xarMakePrivilegeRoot('AddMaps');
    xarMakePrivilegeRoot('DeleteMaps');
    xarMakePrivilegeRoot('AdminMaps');

# --------------------------------------------------------
#
# Set up modvars
#
    xarModVars::set('maps', 'mapwidth', 800);
    xarModVars::set('maps', 'mapheight', 600);
    xarModVars::set('maps', 'zoomlevel', 6);
    xarModVars::set('maps', 'centerlatitude', 48.5132);
    xarModVars::set('maps', 'centerlongitude', 2.1745);
    xarModVars::set('maps', 'gmapskey', 'Paste your google maps key here');
    xarModVars::set('maps', 'ymapskey', 'Paste your yahoo maps key here');
    xarModVars::set('maps', 'glargemapcontrol', 0);
    xarModVars::set('maps', 'gsmallmapcontrol', 0);
    xarModVars::set('maps', 'gsmallzoomcontrol', 0);
    xarModVars::set('maps', 'gscalecontrol', 0);
    xarModVars::set('maps', 'gmaptypecontrol', 0);
    xarModVars::set('maps', 'goverviewmapcontrol', 0);
    xarModVars::set('maps', 'uselocations', serialize(array('dynamic')));

# --------------------------------------------------------
#
# Create a parent category for maps
#
    $cid = xarModAPIFunc('categories', 'admin', 'create',
                         array('name' => 'Maps Category',
                               'description' => 'Maps Base Category',
                               'parent_id' => 0));
    // save the id for later
    xarModVars::set('maps', 'basecategory', $cid);

# --------------------------------------------------------
#
# Set up hooks
#
    // This is a GUI hook for the roles module that enhances the roles profile page
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'maps', 'user', 'usermenu')) {
        return false;
    }

    xarModAPIFunc('modules', 'admin', 'enablehooks',
        array('callerModName' => 'maps', 'hookModName' => 'maps'));

    return true;
}

function maps_upgrade()
{
    return true;
}

function maps_delete()
{
    return xarModAPIFunc('xen','admin','deinstall',array('module' => 'maps'));
}

?>
