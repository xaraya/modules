<?php
/**
 * Initialise the xarpages module.
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarpages
 * @author Jason Judge
 */

/**
 * Initialise the xarpages module
 * @return bool
 */
function ievents_init()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $module = 'ievents';
    $eventstable = $xartable[$module . '_events'];
    $calendarstable = $xartable[$module . '_calendars'];

    // Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    /*
        CREATE TABLE `xar_ievents_calendars` (
            `cid` int(11) NOT NULL auto_increment,
            `status` varchar(10) NOT NULL default 'ACTIVE',
            `short_name` varchar(60) NOT NULL default '',
            `long_name` varchar(200) default NULL,
            `description` text,
            PRIMARY KEY  (`cid`)
        );
    */

    // Calendars
    $fields = array(
        'cid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'status' => array('type' => 'varchar', 'size' => 10, 'null' => false, 'default' => 'ACTIVE'),
        'short_name' => array('type' => 'varchar', 'size' => 60, 'null' => true),
        'long_name' => array('type' => 'varchar', 'size' => 200, 'null' => true),
        'description' => array('type' => 'text', 'null' => true),
    );

    // Create the calendar table.
    $query = xarDBCreateTable($calendarstable, $fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    /*
        CREATE TABLE `xar_ievents_events` (
            `eid` int(11) NOT NULL auto_increment,
            `calendar_id` int(11) NOT NULL default '0',
            `title` varchar(255) NOT NULL default '',
            `status` varchar(10) NOT NULL default 'DRAFT',
            `created_time` int(11) NOT NULL default '0',
            `updated_time` int(11) NOT NULL default '0',
            `created_by` int(11) NOT NULL default '0',
            `updated_by` int(11) NOT NULL default '0',
            `all_day` char(1) NOT NULL default 'A',
            `start_date` int(11) NOT NULL default '0',
            `end_date` int(11) default NULL,
            `summary` text,
            `description` text,
            `url` varchar(255) default NULL,
            `external_source` varchar(100) default NULL,
            `external_ref` varchar(100) default NULL,
            `flags` varchar(100) default NULL,
            `location_venue` varchar(255) default NULL,
            `location_address` text,
            `location_country` varchar(200) default NULL,
            `location_postcode` varchar(20) default NULL,
            `contact_email` varchar(200) default NULL,
            `contact_phone` varchar(60) default NULL,
            `contact_details` text,
            `cost` varchar(255) default NULL,
            PRIMARY KEY  (`eid`),
            KEY `calendar_id` (`calendar_id`)
        );
    */

    // Events
    $fields = array(
        'eid' => array('type' => 'integer', 'null' => FALSE, 'increment' => TRUE, 'primary_key' => TRUE),
        'calendar_id' => array('type' => 'integer', 'size' => 11, 'null' => FALSE, 'default' => '0'),
        'title' => array('type' => 'varchar', 'size' => 255),
        'status' => array('type' => 'varchar', 'size' => 10, 'null' => FALSE, 'default' => 'DRAFT'),
        'created_time' => array('type' => 'integer', 'size' => 11, 'null' => FALSE, 'default' => '0'),
        'updated_time' => array('type' => 'integer', 'size' => 11, 'null' => FALSE, 'default' => '0'),
        'created_by' => array('type' => 'integer', 'size' => 11, 'null' => FALSE, 'default' => '0'),
        'updated_by' => array('type' => 'integer', 'size' => 11, 'null' => FALSE, 'default' => '0'),
        'all_day' => array('type' => 'varchar', 'size' => 1, 'null' => FALSE, 'default' => 'A'),
        'start_date' => array('type' => 'integer', 'size' => 11, 'null' => FALSE, 'default' => '0'),
        'end_date' => array('type' => 'integer', 'size' => 11),
        'summary' => array('type' => 'text'),
        'description' => array('type' => 'text'),
        'url' => array('type' => 'varchar', 'size' => 255),
        'external_source' => array('type' => 'varchar', 'size' => 100),
        'external_ref' => array('type' => 'varchar', 'size' => 100),
        'flags' => array('type' => 'varchar', 'size' => 100),
        'location_venue' => array('type' => 'varchar', 'size' => 255),
        'location_address' => array('type' => 'text'),
        'location_country' => array('type' => 'varchar', 'size' => 200),
        'location_postcode' => array('type' => 'varchar', 'size' => 20),
        'contact_name' => array('type' => 'varchar', 'size' => 200),
        'contact_email' => array('type' => 'varchar', 'size' => 200),
        'contact_phone' => array('type' => 'varchar', 'size' => 60),
        'contact_details' => array('type' => 'text'),
        'cost' => array('type' => 'varchar', 'size' => 255),
    );

    // Create the calendar table.
    $query = xarDBCreateTable($eventstable, $fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Create indexes.
    $indexes = array();
    $indexes[] = array(
        'name' => 'i_' . xarDBGetSiteTablePrefix() . '_calendar_id',
        'fields'    => array('calendar_id'),
        'unique'    => false
    );
    $indexes[] = array(
        'name' => 'i_' . xarDBGetSiteTablePrefix() . '_title',
        'fields'    => array('title'),
        'unique'    => false
    );
    $indexes[] = array(
        'name' => 'i_' . xarDBGetSiteTablePrefix() . '_location_venue',
        'fields'    => array('location_venue'),
        'unique'    => false
    );
    $indexes[] = array(
        'name' => 'i_' . xarDBGetSiteTablePrefix() . '_location_postcode',
        'fields'    => array('location_postcode'),
        'unique'    => false
    );
    $indexes[] = array(
        'name' => 'i_' . xarDBGetSiteTablePrefix() . '_external_ref',
        'fields'    => array('external_source', 'external_ref'),
        'unique'    => false
    );

    // Create indexes on events table
    foreach($indexes as $index) {
        $query = xarDBCreateIndex($eventstable, $index);
        $result =& $dbconn->Execute($query);
        if (!$result) return;
    }

    // FULLTEXT indexes for MySQL only, since xarDBCreateIndex() does not support full text indexes
    if (preg_match('/^mysql/', $dbconn->databaseType)) {
        $fulltext_columns = array('summary', 'description', 'location_address', 'contact_details');
        $query = "ALTER TABLE ${eventstable} ADD FULLTEXT (" . implode(', ', $fulltext_columns) . ")";

        // Try creating the fulltext index, but don't affect the overall result if it failed.
        $fulltext_result = $dbconn->Execute($query);

        // Store the result, so we know if we can do fulltext searches.
        if (!empty($fulltext_result)) {
            xarModSetVar($module, 'fulltext_search', '1');
        } else {
            xarModSetVar($module, 'fulltext_search', '0');
        }
    }

    // Set up module variables.
    //xarModSetVar($module, 'foo', 'bar');

    // Switch short URL support on by default.
    xarModSetVar($module, 'SupportShortURLs', 1);

    // Privileges.

    // Set up component 'IEvent'.

    $comp = 'IEvent';
    $instances = array (
        array (
            'header' => 'Calendar ID',
            'query' => 'SELECT cid FROM ' . $calendarstable . ' ORDER BY cid',
            'limit' => 50
        ),
        array (
            'header' => 'Event ID',
            'query' => 'SELECT DISTINCT eid FROM ' . $eventstable . ' ORDER BY eid',
            'limit' => 50
        ),
        // We don't actually want a query here, we just want a box, but that 
        // is not supported by Xaraya. Setting the limit to zero helps by forcing
        // the box to appear instead of the query drop-down. We select from any
        // table that has at least one row.
        array (
            'header' => 'Owner ID',
            'query' => 'SELECT \'All\' FROM ' . $xartable['modules'],
            'limit' => 0
        ),
    );
    // This function is a misnomer. It actually defines a _component_
    xarDefineInstance(
        $module, $comp, $instances, 0, 'All', 'All', 'All',
        xarML('Security component for #(1) #(2)', $module, $comp)
    );

    // Masks for the component 'IEvent'.
    // Each mask defines something the user is able to do.
    // The masks are linked to the instances at runtime when security checks
    // are made:
    // xarSecurityCheck($mask, $showException, $component, $instance, $module, ...)
    // xarRegisterMask($name, $realm, $module, $component, $instance, $level, $description='')
    xarRegisterMask(
        'Overview' . $comp, 'All', $module, $comp, 'All', 'ACCESS_OVERVIEW',
        xarML('Overview of an event')
    );
    xarRegisterMask(
        'Read' . $comp, 'All', $module, $comp, 'All', 'ACCESS_READ',
        xarML('View an event')
    );
    xarRegisterMask(
        'Comment' . $comp, 'All', $module, $comp, 'All', 'ACCESS_COMMENT',
        xarML('Submit an event')
    );
    xarRegisterMask(
        'Moderate' . $comp, 'All', $module, $comp, 'All', 'ACCESS_MODERATE',
        xarML('Change status of an event')
    );
    xarRegisterMask(
        'Edit' . $comp, 'All', $module, $comp, 'All', 'ACCESS_EDIT',
        xarML('Move and rename an event')
    );
    xarRegisterMask(
        'Delete' . $comp, 'All', $module, $comp, 'All', 'ACCESS_DELETE',
        xarML('Add and remove events')
    );
    xarRegisterMask(
        'Admin' . $comp, 'All', $module, $comp, 'All', 'ACCESS_ADMIN',
        xarML('Administer the module')
    );

    // Set up component 'IEventCal'.
    $comp = 'IEventCal';
    $instances = array (
        array (
            'header' => 'Calendar',
            'query' => 'SELECT cid FROM ' . $calendarstable . ' ORDER BY cid',
            'limit' => 50
        )
    );
    xarDefineInstance(
        $module, $comp, $instances, 0, 'All', 'All', 'All',
        xarML('Security component for #(1) #(2)', $module, $comp)
    );

    // Masks for the component 'IEventCal'.
    xarRegisterMask(
        'Edit' . $comp, 'All', $module, $comp, 'All', 'ACCESS_EDIT',
        xarML('Edit details for a calendar')
    );
    xarRegisterMask(
        'Delete' . $comp, 'All', $module, $comp, 'All', 'ACCESS_DELETE',
        xarML('Add and remove calendars')
    );
    xarRegisterMask(
        'Admin' . $comp, 'All', $module, $comp, 'All', 'ACCESS_ADMIN',
        xarML('Administer module')
    );

    // Switch on all hooks from categories.
    // Do *not* hook dynamicdata (DD) to this module, since it uses DD directly.
    if (xarModIsAvailable('dynamicdata')) {
        xarModAPIFunc('modules', 'admin', 'enablehooks',
            array('callerModName' => $module, 'hookModName' => 'categories')
        );
    }


    // Register block types.
    // None.

    // Set up module hooks
    // None.

    // Create the DD objects.
    // TODO: these will work only if the database prefix is 'xar'. For any other
    // prefixes, the data files should be modified by hand and imported.
    $objectid = xarModAPIFunc(
        'dynamicdata', 'util', 'import',
        array('file' => 'modules/' .$module. '/xardata/' .$module. '_calendars-def.xml', 'keepitemid' => false)
    );
    $objectid = xarModAPIFunc(
        'dynamicdata', 'util', 'import',
        array('file' => 'modules/' .$module. '/xardata/' .$module. '_events-def.xml', 'keepitemid' => false)
    );

    //This initialization takes us to version 0.1.0 - continue in upgrade
    return ievents_upgrade('0.1.0');
}

/**
 * Upgrade the xarpages module from an old version.
 *
 * @param string oldversion
 * @return bool true on success
 */
function ievents_upgrade($oldversion)
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $module = 'ievents';

    $eventstable = $xartable[$module . '_events'];
    $typestable = $xartable[$module . '_calendars'];

    // Upgrade dependent on old version number.
    switch ($oldversion) {
        case '0.1.0':
            // Upgrading from 0.1.0
			// Locks moved to their own property, and requires a table column added in ievents
			// Consistent with init, use tablemaintenance api
			
		   xarDBLoadTableMaintenanceAPI();
            // Update the topics table with a first post date tfpost field
           $query = xarDBAlterTable($eventstable,
                              array('command' => 'add',
                                    'field'   => 'locks',
                                    'type'    => 'varchar',
                                    'null'    => false,
                                    'size'    => '10',
                                    'default' => ''));
                                    
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;      
            
        case '0.1.1':
            // Upgrading from 0.1.1

        break;
    }

    // Update successful.
    return true;
}

/**
 * Delete (remove) the xarpages module.
 * @return bool true on success
 */
function ievents_delete()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $module = 'ievents';
    $eventstable = $xartable[$module . '_events'];
    $calendarstable = $xartable[$module . '_calendars'];

    // Delete module variables
    xarModDelAllVars($module);

    // Drop privileges.
    xarRemoveMasks($module);
    xarRemoveInstances($module);

    // Deletion successful.
    return true;
}

?>