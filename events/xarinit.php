<?php
/**
 * File: $Id: s.xarinit.php 1.17 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 *
 * Events initialization functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage events
 * @author Events module development team
 */

/**
 * Upgraded to the new security schema by Vassilis Stratigakis
 * http://www.tequilastarrise.net
 */

/**
 * initialise the events module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function events_init()
{

	    if(!xarModIsAvailable('categories')) {
        $msg=xarML('The categories module should be activated first');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY',
                        new SystemException($msg));
        return;
    }

    // Get datbase setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // It's good practice to name the table definitions you
    // are using - $table doesn't cut it in more complex
    // modules
    $eventstable = $xartable['events'];

    // adodb does not provide the functionality to abstract table creates
    // across multiple databases.  Xaraya offers the xarCreateTable function
    // contained in the following file to provide this functionality.
    xarDBLoadTableMaintenanceAPI();

    // Define the table structure in this associative array
    // There is one element for each field.  The key for the element is
    // the physical field name.  The element contains another array specifying the
    // data type and associated parameters
    $fields = array(
        'xar_exid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_name'=>array('type'=>'varchar','size'=>32,'null'=>FALSE),
        'xar_number'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'0')
    );

//    $query = "CREATE TABLE $eventstable (
//            xar_exid int(10) NOT NULL auto_increment,
//            xar_name varchar(32) NOT NULL default '',
//            xar_number int(5) NOT NULL default 0,
//            PRIMARY KEY(xar_exid))";

    // More sample field create statements
    // 'xar_i000'=>array('null'=>FALSE, 'type'=>'integer','unsigned'=>TRUE,'increment'=>TRUE,'primary_key'=>TRUE),
    // 'xar_i001'=>array('null'=>FALSE, 'type'=>'integer','size'=>'tiny',   'default'=>'0'),
    // 'xar_i002'=>array('null'=>FALSE, 'type'=>'integer','size'=>'small',  'default'=>'0'),
    // 'xar_i003'=>array('null'=>TRUE, 'type'=>'integer','size'=>'medium', 'default'=>'0'),
    // 'xar_i004'=>array('type'=>'integer','size'=>'big',    'default'=>'0'),
    // 'xar_v001'=>array('null'=>FALSE, 'type'=>'varchar','size'=>255),
    // 'xar_v002'=>array('null'=>TRUE,  'type'=>'varchar','size'=>100, 'default'=>'NULL'),
    // 'xar_v003'=>array('null'=>TRUE,  'type'=>'varchar','size'=>11,  'default'=>'XX'),
    // 'xar_c001'=>array('null'=>FALSE, 'type'=>'char','size'=>255),
    // 'xar_c002'=>array('null'=>TRUE,  'type'=>'char','size'=>100, 'default'=>'NULL'),
    // 'xar_c003'=>array('null'=>TRUE,  'type'=>'char','size'=>11,  'default'=>'XX'),
    // 'xar_t001'=>array('null'=>FALSE, 'type'=>'text'),
    // 'xar_t002'=>array('null'=>FALSE, 'type'=>'text', 'size'=>'tiny'),
    // 'xar_t003'=>array('null'=>FALSE, 'type'=>'text', 'size'=>'medium'),
    // 'xar_t004'=>array('null'=>FALSE, 'type'=>'text', 'size'=>'long'),
    // 'xar_b001'=>array('null'=>FALSE, 'type'=>'blob'),
    // 'xar_b002'=>array('null'=>FALSE, 'type'=>'blob','size'=>'tiny'),
    // 'xar_b003'=>array('null'=>FALSE, 'type'=>'blob','size'=>'medium'),
    // 'xar_b004'=>array('null'=>FALSE, 'type'=>'blob','size'=>'long'),
    // 'xar_l001'=>array('null'=>FALSE, 'type'=>'boolean','default'=>FALSE),
    // 'xar_d001'=>array('type'=>'datetime','default'=>array('year'=>2002,'month'=>04,'day'=>17,'hour'=>'12','minute'=>59,'second'=>0)),
    // 'xar_d002'=>array('null'=>FALSE, 'type'=>'date','default'=>array('year'=>2002,'month'=>04,'day'=>17)),
    // 'xar_f000'=>array('type'=>'float'),
    // 'xar_f001'=>array('type'=>'float', 'width'=>6,'decimals'=>2),
    // 'xar_f002'=>array('type'=>'float', 'size'=>'double','width'=>12, 'decimals'=>2),
    // 'xar_f003'=>array('type'=>'float', 'size'=>'decimal','width'=>12, 'decimals'=>2),
    // 'xar_ts01'=>array('type'=>'timestamp'),
    // 'xar_ts02'=>array('type'=>'timestamp', 'size'=>'YYYYMMDD'),


    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($eventstable,$fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // If Categories API loaded and available, generate proprietary
    // module master category cid and child subcids
    if(xarModIsAvailable('categories'))
    {
        $eventscid = xarModAPIFunc('categories',
                                    'admin',
                                    'create',
                                    Array('name' => 'Events',
                                          'description' => 'Events Categories',
                                          'parent_id' => 0));
        // Note: you can have more than 1 mastercid (cfr. articles module)
        xarModSetVar('events', 'number_of_categories', 1);
        xarModSetVar('events', 'mastercids', $eventscid);
        $eventscategories = array();
        $eventscategories[] = array('name' => "Seminars",
                                    'description' => "Seminar announcements");
        $eventscategories[] = array('name' => "Concerts",
                                      'description' => "Concert announcements");
        foreach($eventscategories as $subcat)
        {
            $eventssubcid = xarModAPIFunc('categories',
                                           'admin',
                                           'create',
                                           Array('name' => $subcat['name'],
                                                 'description' =>
                                                     $subcat['description'],
                                                 'parent_id' => $eventscid));
        }
    }


        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'events', 'hookModName' => 'categories'));

    // Set up an initial value for a module variable.  Note that all module
    // variables should be initialised with some value in this way rather
    // than just left blank, this helps the user-side code and means that
    // there doesn't need to be a check to see if the variable is set in
    // the rest of the code as it always will be
    XarModSetVar('events', 'eventsperpage', 10);
	XarModSetVar('events', 'currency', '€');
    XarModSetVar('events', 'ticketsperuser', 5);
    XarModSetVar('events', 'imageuploadpath', 'modules/events/eventimages');
    XarModSetVar('events', 'headerimagesize', 512000);
    XarModSetVar('events', 'bodyimagesize', 512000);
    XarModSetVar('events', 'headerimagewidth', 640);
    XarModSetVar('events', 'headerimageheight', 200);
    XarModSetVar('events', 'bodyimagewidth', 640);
    XarModSetVar('events', 'bodyimageheight', 200);
	XarModSetVar('events', 'notificationemail', xarModGetVar('mail', 'adminmail'));
	XarModSetVar('events', 'sendadminemail', 1);    
	XarModSetVar('events', 'senduseremail', 1);
	xarModSetVar('events', 'SupportShortURLs', 0);
	XarModSetVar('events', 'opeventname', 1);
	XarModSetVar('events', 'opcompanyname', 1);
    XarModSetVar('events', 'opspeakername', 1);  
	XarModSetVar('events', 'opeventstartdate', 1);
	XarModSetVar('events', 'opeventenddate', 1);    
	XarModSetVar('events', 'opeventstarttime', 1);    
	XarModSetVar('events', 'opeventendtime', 1);
    XarModSetVar('events', 'opeventregistrationtime', 1);
	XarModSetVar('events', 'opeventaddress', 1);
	XarModSetVar('events', 'opeventsummary', 1);   
	XarModSetVar('events', 'opeventheadertext', 1);   
	XarModSetVar('events', 'opeventbodytext', 1);   
	XarModSetVar('events', 'opeventprinttickets', 1);   
    XarModSetVar('events', 'opeventticketsavailable', 1);  
	XarModSetVar('events', 'opeventcost', 1);  
	XarModSetVar('events', 'opeventtelephone', 1);  
	XarModSetVar('events', 'opeventheaderimage', 0); 
	XarModSetVar('events', 'opeventbodyimage', 0);    

    // Register Block types (this *should* happen at activation/deactivation)
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'events',
                             'blockType'=> 'events'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'events',
                             'blockType'=> 'otherevents'))) return;

    // Register our hooks that we are providing to other modules.  The events
    // module shows an events hook in the form of the user menu.
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
                            'events', 'user', 'usermenu')) {
        return false;
    }

    /*********************************************************************
    * Define instances for this module
    * Format is
    * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/

	// Instance definitions serve two purposes:
	// 1. The define "filters" that are added to masks at runtime, allowing us to set
	//    security checks over single objects or groups of objects
	// 2. They generate dropdowns the UI uses to present the user with choices when
	//    definng or modifying privileges.

	// For each component we need to tell the system how to generate
	// a list (dropdown) of all the component's instances.
	// In addition, we add a header which will be displayed for greater clarity, and a number
	// (limit) which defines the maximum number of rows a dropdown can have. If the number of
	// instances is greater than the limit (e.g. registered users), the UI instead presents an
	// input field for manual input, which is then checked for validity.

	$query1 = "SELECT DISTINCT xar_name FROM ".$eventstable;
	$query2 = "SELECT DISTINCT xar_number FROM ".$eventstable;
	$query3 = "SELECT DISTINCT xar_exid FROM ".$eventstable;
	$instances = array(
						array('header' => 'Events Name:',
								'query' => $query1,
								'limit' => 20
							),
						array('header' => 'Events Number:',
								'query' => $query2,
								'limit' => 20
							),
						array('header' => 'Events ID:',
								'query' => $query3,
								'limit' => 20
							)
					);
    xarDefineInstance('events', 'Events', $instances, 0, 'All', 'All', 'All', 'Security instance for events module.');


    /*********************************************************************
    * Register the module components that are privileges objects
    * Format is
    * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/

    xarRegisterMask('OverviewEvents','All','events','Event','All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadEvents','All','events','Event','All:All:All','ACCESS_READ');
    xarRegisterMask('EditEvents','All','events','Event','All:All:All','ACCESS_EDIT');
    xarRegisterMask('AddEvents','All','events','Event','All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteEvents','All','events','Event','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminEvents','All','events','Event','All:All:All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the events module from an old version
 * This function can be called multiple times
 */
function events_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.5':
            // Version 0.5 didn't have a 'number' field, it was added
            // in version 1.0

            // Get datbase setup - note that both xarDBGetConn() and xarDBGetTables()
            // return arrays but we handle them differently.  For xarDBGetConn()
            // we currently just want the first item, which is the official
            // database handle.  For xarDBGetTables() we want to keep the entire
            // tables array together for easy reference later on
            // This code could be moved outside of the switch statement if
            // multiple upgrades need it
            list($dbconn) = xarDBGetConn();
            $xartable = xarDBGetTables();

            // It's good practice to name the table and column definitions you
            // are getting - $table and $column don't cut it in more complex
            // modules
            // This code could be moved outside of the switch statement if
            // multiple upgrades need it
            $eventstable = $xartable['events'];

            // Add a column to the table

            // adodb does not provide the functionality to abstract table creates
            // across multiple databases.  Xaraya offers the xarCreateTable function
            // contained in the following file to provide this functionality.
            xarDBLoadTableMaintenanceAPI();

//            $query = "ALTER TABLE $eventstable
//                    ADD xar_number INTEGER NOT NULL DEFAULT '0'";

            $query = xarDBAlterTable($eventstable,
                                     array('command' => 'add',
                                           'field' => 'xar_number',
                                           'type' => 'integer',
                                           'null' => false,
                                           'default' => '0'));

            // Pass to ADODB, and send exception if the result isn't valid.
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // At the end of the successful completion of this function we
            // recurse the upgrade to handle any other upgrades that need
            // to be done.  This allows us to upgrade from any version to
            // the current version with ease
            return events_upgrade(1.0);
        case '1.0':
            // Code to upgrade from version 1.0 goes here
            break;
        case '2.0.0':
            // Code to upgrade from version 2.0 goes here
            break;
    }

    // Update successful
    return true;
}

/**
 * delete the events module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function events_delete()
{
    // Get datbase setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // adodb does not provide the functionality to abstract table creates
    // across multiple databases.  Xaraya offers the xarDropeTable function
    // contained in the following file to provide this functionality.
    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['events']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Delete any module variables
    XarModDelVar('events', 'eventsperpage');
	XarModDelVar('events', 'currency');
    XarModDelVar('events', 'ticketsperuser');
	XarModDelVar('events', 'imageuploadpath');
    XarModDelVar('events', 'headerimagesize');
    XarModDelVar('events', 'bodyimagesize');
    XarModDelVar('events', 'headerimagewidth');
    XarModDelVar('events', 'headerimageheight');
    XarModDelVar('events', 'bodyimagewidth');
    XarModDelVar('events', 'bodyimageheight');
    XarModDelVar('events', 'notificationemail');
	XarModDelVar('events', 'sendadminemail');    
	XarModDelVar('events', 'senduseremail');
	xarModDelVar('events', 'SupportShortURLs');
	XarModDelVar('events', 'opeventname');
	XarModDelVar('events', 'opcompanyname');
    XarModDelVar('events', 'opspeakername');  
	XarModDelVar('events', 'opeventstartdate');
	XarModDelVar('events', 'opeventenddate');    
	XarModDelVar('events', 'opeventstarttime');    
	XarModDelVar('events', 'opeventendtime');
    XarModDelVar('events', 'opeventregistrationtime');
	XarModDelVar('events', 'opeventaddress');
	XarModDelVar('events', 'opeventsummary');   
	XarModDelVar('events', 'opeventheadertext');   
	XarModDelVar('events', 'opeventbodytext');   
	XarModDelVar('events', 'opeventprinttickets');   
    XarModDelVar('events', 'opeventticketsavailable');  
	XarModDelVar('events', 'opeventcost');  
	XarModDelVar('events', 'opeventtelephone');  
	XarModDelVar('events', 'opeventheaderimage'); 
	XarModDelVar('events', 'opeventbodyimage');
    if(xarModIsAvailable('categories'))
    {
        xarModDelVar('events', 'number_of_categories');
        xarModDelVar('events', 'mastercids');
    }


    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'events',
                             'blockType'=> 'events'))) return;
    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'events',
                             'blockType'=> 'otherevents'))) return;


    // Remove module hooks
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
                              'events', 'user', 'usermenu')) {
        return false;
    }

    // Remove Masks and Instances
    xarRemoveMasks('events');
    xarRemoveInstances('events');

    // Deletion successful
    return true;
}

?>
