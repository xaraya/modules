<?php
/**
 * File: $Id: s.xarinit.php 1.17 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 *
 * Example initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author XarayaGeek
 */

/**
 * Upgraded to the new security schema by Vassilis Stratigakis
 * http://www.tequilastarrise.net
 */

/**
 * initialise the example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function courses_init()
{
    // Get datbase setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table definitions you
    // are using - $table doesn't cut it in more complex
    // modules
    $coursestable = $xartable['courses'];
    // adodb does not provide the functionality to abstract table creates
    // across multiple databases.  Xaraya offers the xarCreateTable function
    // contained in the following file to provide this functionality.
    xarDBLoadTableMaintenanceAPI();
    // Define the table structure in this associative array
    // There is one element for each field.  The key for the element is
    // the physical field name.  The element contains another array specifying the
    // data type and associated parameters
    $fields = array('xar_courseid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_name' => array('type' => 'varchar', 'size' => 32, 'null' => false),
		'xar_number' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_hours' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
	    'xar_ceu' => array('type'=>'float'),
		'xar_startdate'=>array('type'=>'datetime'),
		'xar_enddate'=>array('type'=>'datetime'),
		'xar_shortdesc'=>array('null'=>FALSE, 'type'=>'text'),
		'xar_longdesc'=>array('null'=>FALSE, 'type'=>'text')
        );

	 $query = xarDBCreateTable($coursestable, $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

	$courses_studentsTable = $xartable['courses_students'];

	$fields = array('xar_sid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
		'xar_uid' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
		'xar_course' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0')
		);

    // $query = "CREATE TABLE $exampletable (
    // xar_exid int(10) NOT NULL auto_increment,
    // xar_name varchar(32) NOT NULL default '',
    // xar_number int(5) NOT NULL default 0,
    // PRIMARY KEY(xar_exid))";
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
    $query = xarDBCreateTable($courses_studentsTable, $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    /*
     * REGISTER THE TABLES AT DYNAMICDATA
     */
    $objectid = xarModAPIFunc(
        'dynamicdata'
        ,'util'
        ,'import'
        ,array(
            'file'  => 'modules/courses/xarobject.xml'));

    if (empty($objectid)) return;
    // If Categories API loaded and available, generate proprietary
    // module master category cid and child subcids
    if (xarModIsAvailable('categories')) {
        $coursescid = xarModAPIFunc('categories',
            'admin',
            'create',
            Array('name' => 'courses',
                'description' => 'Courses Categories',
                'parent_id' => 0));
        // Note: you can have more than 1 mastercid (cfr. articles module)
        xarModSetVar('courses', 'number_of_categories', 1);
        xarModSetVar('courses', 'mastercids', $coursescid);
        $coursescategories = array();
        $coursescategories[] = array('name' => "IT",
            'description' => "IT Courses");
        $coursescategories[] = array('name' => "General Education",
            'description' => "General Education Categories");
        $coursescategories[] = array('name' => "Management",
            'description' => "Management Courses");
        foreach($coursescategories as $subcat) {
            $coursessubcid = xarModAPIFunc('categories',
                'admin',
                'create',
                Array('name' => $subcat['name'],
                    'description' =>
                    $subcat['description'],
                    'parent_id' => $coursescid));
        }
    }
    // Set up an initial value for a module variable.  Note that all module
    // variables should be initialised with some value in this way rather
    // than just left blank, this helps the user-side code and means that
    // there doesn't need to be a check to see if the variable is set in
    // the rest of the code as it always will be
    xarModSetVar('courses', 'bold', 0);
    xarModSetVar('courses', 'itemsperpage', 10);
    // If your module supports short URLs, the website administrator should
    // be able to turn it on or off in your module administration
    xarModSetVar('courses', 'SupportShortURLs', 0);
    // Register Block types (this *should* happen at activation/deactivation)
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'courses',
                'blockType' => 'others'))) return;
    // Register blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'courses',
                'blockType' => 'new'))) return;
    // Register our hooks that we are providing to other modules.  The course
    // module shows ahook in the form of the user menu that shows the user the courses
    // he or she is enrolled in.
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'courses', 'user', 'usermenu')) {
        return false;
    }

     xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'roles'
            ,'callerModName'    => 'courses'));

    // Hook for module Search
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'search'
            ,'callerModName'    => 'courses'));
	// Hook for module comments
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'comments'
            ,'callerModName'    => 'courses'));
     // Hook for Categories
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'categories'
            ,'callerModName'    => 'courses'));
    // Hook for Dynamic Data
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'dynamicdata'
            ,'callerModName'    => 'courses'));


    /**
     * Define instances for this module
     * Format is
     * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
     */
    // Instance definitions serve two purposes:
    // 1. The define "filters" that are added to masks at runtime, allowing us to set
    // security checks over single objects or groups of objects
    // 2. They generate dropdowns the UI uses to present the user with choices when
    // definng or modifying privileges.
    // For each component we need to tell the system how to generate
    // a list (dropdown) of all the component's instances.
    // In addition, we add a header which will be displayed for greater clarity, and a number
    // (limit) which defines the maximum number of rows a dropdown can have. If the number of
    // instances is greater than the limit (e.g. registered users), the UI instead presents an
    // input field for manual input, which is then checked for validity.
    $query1 = "SELECT DISTINCT xar_name FROM " . $coursestable;
    $query2 = "SELECT DISTINCT xar_number FROM " . $coursestable;
    $query3 = "SELECT DISTINCT xar_courseid FROM " . $coursestable;
    $instances = array(
        array('header' => 'Course Name:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Course Number:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Course ID:',
            'query' => $query3,
            'limit' => 20
            )
        );
    xarDefineInstance('Courses', 'Item', $instances);
    // You can also use some external "wizard" function to specify instances :

    // $instances = array(
    // array('header' => 'external', // this keyword indicates an external "wizard"
    // 'query'  => xarModURL('example','admin','privileges',array('foo' =>'bar')),
    // 'limit'  => 0
    // )
    // );
    // xarDefineInstance('example', 'Item', $instances);
    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_title FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'courses'";
    $instances = array(
        array('header' => 'Courses Block Title:',
            'query' => $query,
            'limit' => 20
            )
        );
    xarDefineInstance('courses', 'Block', $instances);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ReadCoursesBlock', 'All', 'courses', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewCourses', 'All', 'courses', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadCourses', 'All', 'courses', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditCourses', 'All', 'courses', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddCourses', 'All', 'courses', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteCourses', 'All', 'courses', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminCourses', 'All', 'courses', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
	 xarRegisterPrivilege('EditCourses','All','courses','item','All','ACCESS_EDIT',xarML('Enroll in Courses'));
    return true;
}

/**
 * upgrade the example module from an old version
 * This function can be called multiple times
 */
function courses_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
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
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            // It's good practice to name the table and column definitions you
            // are getting - $table and $column don't cut it in more complex
            // modules
            // This code could be moved outside of the switch statement if
            // multiple upgrades need it
            $coursestable = $xartable['courses'];
            // Add a column to the table
            // adodb does not provide the functionality to abstract table creates
            // across multiple databases.  Xaraya offers the xarCreateTable function
            // contained in the following file to provide this functionality.
            xarDBLoadTableMaintenanceAPI();
            // $query = "ALTER TABLE $exampletable
            // ADD xar_number INTEGER NOT NULL DEFAULT '0'";
            $query = xarDBAlterTable($coursestable,
                array('command' => 'add',
                    'field' => 'xar_hours',
                    'type' => 'integer',
                    'null' => false,
                    'default' => '0'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;
            // At the end of the successful completion of this function we
            // recurse the upgrade to handle any other upgrades that need
            // to be done.  This allows us to upgrade from any version to
            // the current version with ease
            return courses_upgrade(1.0);
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
 * delete the example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function courses_delete()
{
    // Get datbase setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // adodb does not provide the functionality to abstract table creates
    // across multiple databases.  Xaraya offers the xarDropeTable function
    // contained in the following file to provide this functionality.
    xarDBLoadTableMaintenanceAPI();
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['courses']);
    if (empty($query)) return; // throw back

     $query = xarDBDropTable($xartable['courses_students']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // Delete any module variables
    xarModDelVar('courses', 'itemsperpage');
    xarModDelVar('courses', 'bold');


    xarModDelVar('courses', 'SupportShortURLs');
    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'courses',
                'blockType' => 'new'))) return;
    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'courses',
                'blockType' => 'others'))) return;
                
    // Remove module hooks
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'courses', 'user', 'usermenu')) {
        return false;
    }
    // Remove Masks and Instances
    // these functions remove all the registered masks and instances of a module
    // from the database. This is not strictly necessary, but it's good housekeeping.
    xarRemoveMasks('courses');
    xarRemoveInstances('courses');
	xarRemovePrivileges('courses');

  if (xarModIsAvailable('categories')) {
        xarModDelVar('courses', 'number_of_categories');
        xarModDelVar('courses', 'mastercids');

	}


       // Hook for module search
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'disablehooks'
        ,array(
            'hookModName'       => 'search'
            ,'callerModName'    => 'courses'));
        // Hook for module comments
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'disablehooks'
        ,array(
            'hookModName'       => 'comments'
            ,'callerModName'    => 'courses'));
     // Hook for Categories
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'disablehooks'
        ,array(
            'hookModName'       => 'categories'
            ,'callerModName'    => 'courses'));
    // Hook for Dynamic Data
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'disablehooks'
        ,array(
            'hookModName'       => 'dynamicdata'
            ,'callerModName'    => 'courses'));
    // Hook for Mail
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'disablehooks'
        ,array(
            'hookModName'       => 'mail'
            ,'callerModName'    => 'courses'));
    /*
     * REMOVE all comments (which are stored via the comments api)
     */
    xarModAPIFunc('comments',
                  'admin',
                  'delete_module_nodes',
                   array('modid' => xarModGetIDFromName('courses')));

   // remove the table from dynamic data
    $objectinfo = xarModAPIFunc(
        'dynamicdata'
        ,'user'
        ,'getobjectinfo'
        ,array(
            'modid'     => xarModGetIDFromName('courses' )
            ,'itemtype' => 0 ));

    if (!isset($objectinfo) || empty($objectinfo['objectid'])) {
        return;
    }
    $objectid = $objectinfo['objectid'];

    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }

	// Deletion successful
    return true;
}

?>
