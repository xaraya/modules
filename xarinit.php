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

function gmaps_init()
{
# --------------------------------------------------------
#
# Create database tables
#
    $gmaps_objects = array(
    					 'gmaps_locations',
//    					 'gmaps_emails',
                         );

    // Treat destructive right now
    $existing_objects  = xarModApiFunc('dynamicdata','user','getobjects');
    foreach($existing_objects as $objectid => $objectinfo) {
        if(in_array($objectinfo['name'], $gmaps_objects)) {
            // KILL
            if(!xarModApiFunc('dynamicdata','admin','deleteobject', array('objectid' => $objectid))) return;
        }
    }

    // Most information will have a DD object presentation, some will be
    // dynamic, others defined with a static datasource.
    // These definitions and data are in the xardata directory in this module.
    // and provide the definition and optionally  the initialisation
    // data in XML files [gmaps-objectname]-def.xml an [gmaps-objectname]-data.xml

    // TODO: This will bomb out if the object already exists
	$objects = array();

    foreach($gmaps_objects as $gmaps_object) {
        $def_file = 'modules/gmaps/xardata/'.$gmaps_object.'-def.xml';
        $dat_file = 'modules/gmaps/xardata/'.$gmaps_object.'-data.xml';

        $objects = array();
        $objectid = xarModAPIFunc('dynamicdata','util','import', array('file' => $def_file, 'keepitemid' => true));
        if (!$objectid) return;
        else $objects[$gmaps_object] = $objectid;
        // Let data import be allowed to be empty
        if(file_exists($dat_file)) {
            // And allow it to fail for now
            xarModAPIFunc('dynamicdata','util','import', array('file' => $dat_file,'keepitemid' => true));
        }
    }

	xarModSetVar('gmaps','gmaps_objects',serialize($objects));

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewGmaps','All','gmaps','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('AdminGmaps','All','gmaps','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('AdminGmaps','All','gmaps','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up modvars
#
    xarModSetVar('gmaps', 'mapwidth', 800);
    xarModSetVar('gmaps', 'mapheight', 400);
    xarModSetVar('gmaps', 'zoomlevel', 16);
    xarModSetVar('gmaps', 'latitude', 0);
    xarModSetVar('gmaps', 'longitude', 30);
    xarModSetVar('gmaps', 'gmapskey', 'xxxxxx');

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
    $this_module = 'gmaps';

# --------------------------------------------------------
#
# Remove database tables
#
    // Load table maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $prefix = xarDBGetSiteTablePrefix();
    $table = $prefix . "_" . $this_module;
    $query = xarDBDropTable($table);
    if (empty($query)) return; // throw back

# --------------------------------------------------------
#
# Delete all DD objects created by this module
#
	$rsvp_objects = unserialize(xarModGetVar($this_module,$this_module . '_objects'));
	foreach ($rsvp_objects as $key => $value)
	    $result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $value));

# --------------------------------------------------------
#
# Remove the categories
#
    xarModAPIFunc('categories', 'admin', 'deletecat',
                         array('cid' => xarModGetVar($this_module, 'basecategory'))
						);

# --------------------------------------------------------
#
# Remove modvars, masks and privilege instances
#
    xarRemoveMasks($this_module);
    xarRemoveInstances($this_module);
    xarModDelAllVars($this_module);

    return true;
}

?>
