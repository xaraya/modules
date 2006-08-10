<?php
/**
 *
 * Function init
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Gmaps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  Initialization routine for this module
 *
 * @param to be added
 * @return to be added
 *
 */

include_once 'modules/xen/xarclasses/xenobject.class.php';

function gmaps_init()
{
# --------------------------------------------------------
#
# Create database tables
#

    $q = new xenQuery();
	$prefix = xarDBGetSiteTablePrefix();
	$query = "DROP TABLE IF EXISTS " . $prefix . "_gmaps_directory";
	if (!$q->run($query)) return;

	$query = "CREATE TABLE " . $prefix . "_gmaps_directory (
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
/*	include "modules/gmaps/xardata/gmaps_directory-dat.php";
	$rows = gmaps_dat1();
	foreach ($rows as $row) {
		if (!$q->run($row)) return;
	}
	$rows = gmaps_dat2();
	foreach ($rows as $row) {
		if (!$q->run($row)) return;
	}
*/
# --------------------------------------------------------
#
# Create DD objects
#
    $module = 'gmaps';
    $objects = array(
					 'gmaps_locations',
					 );
    if(!xarModAPIFunc('xen','admin','install',array('module' => $module, 'objects' => $objects))) return;

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewGmaps','All','gmaps','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadGmaps','All','gmaps','All','All','ACCESS_READ');
    xarRegisterMask('AdminGmaps','All','gmaps','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('ReadGmaps','All','gmaps','All','All','ACCESS_READ');
    xarRegisterPrivilege('AdminGmaps','All','gmaps','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up modvars
#
    xarModSetVar('gmaps', 'mapwidth', 800);
    xarModSetVar('gmaps', 'mapheight', 600);
    xarModSetVar('gmaps', 'zoomlevel', 6);
    xarModSetVar('gmaps', 'latitude', 48.51);
    xarModSetVar('gmaps', 'longitude', 2.17);
    xarModSetVar('gmaps', 'gmapskey', 'xxxxxxxxxx');

# --------------------------------------------------------
#
# Create a parent category for gmaps
#
    $cid = xarModAPIFunc('categories', 'admin', 'create',
                         array('name' => 'Gmaps Category',
                               'description' => 'Gmaps Base Category',
                               'parent_id' => 0));
    // save the id for later
    xarModSetVar('gmaps', 'basecategory', $cid);

# --------------------------------------------------------
#
# Set up hooks
#
    // This is a GUI hook for the roles module that enhances the roles profile page
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'gmaps', 'user', 'usermenu')) {
        return false;
    }

    xarModAPIFunc('modules', 'admin', 'enablehooks',
        array('callerModName' => 'gmaps', 'hookModName' => 'gmaps'));

    return true;
}

function gmaps_upgrade()
{
    return true;
}

function gmaps_delete()
{
    return xarModAPIFunc('xen','admin','deinstall',array('module' => 'gmaps'));
}

?>
