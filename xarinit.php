<?php
/**
 * File: $Id: s.xarinit.php 1.17 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 *
 * Courses initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author XarayaGeek, Michel V.
 */

/**
 * initialise the courses module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function courses_init()
{
    // Get database setup and make tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // The courses table
    $coursestable = $xartable['courses'];
    xarDBLoadTableMaintenanceAPI();
    $fields = array('xar_courseid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_name' => array('type' => 'varchar', 'size' => 55, 'null' => false),
        'xar_number' => array('type' => 'varchar', 'size' => 5, 'null' => false),
        'xar_type' => array('type' => 'varchar', 'size' => 10, 'default' => 'NULL'),
        'xar_level' => array('type' => 'varchar', 'size' => 20, 'default' => 'NULL'),
        'xar_shortdesc'=>array('null'=>FALSE, 'type'=>'text'),
        'xar_language'=>array('null'=>TRUE, 'type'=>'text'),
        'xar_freq' =>array('null'=>TRUE, 'type' => 'varchar', 'size' => 20, 'default' => 'NULL'),
        'xar_contact' => array('null'=>TRUE, 'type' => 'varchar', 'size' => 255, 'default' => 'NULL'),
        'xar_hidecourse' => array('type' => 'integer', 'size' => 'tiny', 'null' => false, 'default' => '0')
        );

     $query = xarDBCreateTable($coursestable, $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // The course will be planned for each occurence
    $courses_planning = $xartable['courses_planning'];
    $fields = array('xar_planningid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_courseid' => array('type' => 'integer', 'null' => false),
        'xar_credits' => array('type' => 'integer', 'size' => 'tiny', 'unsigned'=>TRUE, 'null' => false, 'default' => '0'),
        'xar_creditsmin' => array('type' => 'integer', 'size' => 'tiny', 'unsigned'=>TRUE, 'null' => false, 'default' => '0'),
        'xar_creditsmax' => array('type' => 'integer', 'size' => 'tiny', 'unsigned'=>TRUE, 'null' => false, 'default' => '0'),
        'xar_courseyear' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_startdate'=>array('type'=>'datetime'),
        'xar_enddate'=>array('type'=>'datetime'),
        'xar_prerequisites'=>array('null'=>FALSE, 'type'=>'text'),
        'xar_aim'=>array('null'=>TRUE, 'type'=>'text'),
        'xar_method'=>array('null'=>TRUE, 'type'=>'text'),
        'xar_longdesc'=>array('null'=>FALSE, 'type'=>'text'),
        'xar_costs'=>array('null'=>FALSE, 'type'=>'varchar','size'=>100, 'default'=>'0'),
        'xar_committee'=>array('null'=>TRUE, 'type'=>'text',),
        'xar_coordinators'=>array('null'=>TRUE, 'type'=>'text'),
        'xar_lecturers'=>array('null'=>TRUE, 'type'=>'text'),
        'xar_location'=>array('null'=>TRUE, 'type'=>'text',),
        'xar_material'=>array('null'=>TRUE, 'type'=>'text'),
        'xar_info'=>array('null'=>TRUE, 'type'=>'text'),
        'xar_program'=>array('null'=>TRUE, 'type'=>'text'),
        'xar_hideplanning' => array('type' => 'integer', 'size' => 'tiny', 'null' => false, 'default' => '0'),
        'xar_minparticipants' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_maxparticipants' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_closedate'=>array('type'=>'date')
        );

     $query = xarDBCreateTable($courses_planning, $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    //Table for students per planned course
    //status makes it possible to set the status of this enroll to this course
    $courses_students = $xartable['courses_students'];

    $fields = array('xar_sid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_userid' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_planningid' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_status' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0')
        );

    $query = xarDBCreateTable($courses_students, $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    $courses_teachers = $xartable['courses_teachers'];

    $fields = array('xar_tid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_userid' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_planningid' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_type' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0')
        );

    $query = xarDBCreateTable($courses_teachers, $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    
    //Table for Course levels
    //This will be taken to dyn data.
    $courses_levels = $xartable['courses_levels'];
    $fields = array('xar_lid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_level'=>array('null'=>FALSE, 'type'=>'varchar','size'=>50, 'default'=>'0')
        );
    // Create the Table
    $query = xarDBCreateTable($courses_levels, $fields);
    if (empty($query)) return; // throw back

    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    //Table for Student status
    //This will be taken to dyn data.
    $courses_studstatus = $xartable['courses_studstatus'];
    $fields = array('xar_statusid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_studstatus'=>array('null'=>FALSE, 'type'=>'varchar','size'=>50, 'default'=>'0')
        );
    // Create the Table
    $query = xarDBCreateTable($courses_studstatus, $fields);
    if (empty($query)) return; // throw back

    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    //Table for Course years
    //This will be taken to dyn data.
    $courses_years = $xartable['courses_years'];
    $fields = array('xar_yearid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_year'=>array('null'=>FALSE, 'type'=>'varchar','size'=>25, 'default'=>'0')
        );
    // Create the Table
    $query = xarDBCreateTable($courses_years, $fields);
    if (empty($query)) return; // throw back

    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
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

    /* FIXME: does generate errors
     *
     * REGISTER THE TABLES AT DYNAMICDATA
     
    $path = "modules/courses/xardata/";
     
    $objectid = xarModAPIFunc('dynamicdata','util','import',array('file'  => $path . '/courses_levels.xml'));

    if (empty($objectid)) return;
    xarModSetVar('courses','levelsobjectid',$objectid);
    
    $objectid = xarModAPIFunc('dynamicdata','util','import',array('file'  => $path . '/courses_levels_data.xml'));

    if (empty($objectid)) return;
*/

    /**
     * Define instances for this module
     * Format is
     * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
     */
    // Instance definitions serve two purposes:
    // 1. The define "filters" that are added to masks at runtime, allowing us to set
    // security checks over single objects or groups of objects
    // 2. They generate dropdowns the UI uses to present the user with choices when
    // defining or modifying privileges.
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
    xarDefineInstance('Courses', 'Course', $instances);
    //For the planning of courses
    $query1 = "SELECT DISTINCT xar_planningid FROM " . $courses_planning;
    $query2 = "SELECT DISTINCT xar_userid FROM " . $courses_teachers;
    $query3 = "SELECT DISTINCT xar_courseid FROM " . $courses_planning;
    $instances = array(
        array('header' => 'Planning ID:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'UserID of teacher:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Course ID:',
            'query' => $query3,
            'limit' => 20
            )
        );
    xarDefineInstance('Courses', 'Planning', $instances);
    
    //Blocks
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
    // The block will be maybe be used
    // The courses themselves need to be adminable
    xarRegisterMask('ReadCoursesBlock', 'All', 'courses', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewCourses', 'All', 'courses', 'Course', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadCourses', 'All', 'courses', 'Course', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditCourses', 'All', 'courses', 'Course', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddCourses', 'All', 'courses', 'Course', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteCourses', 'All', 'courses', 'Course', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminCourses', 'All', 'courses', 'Course', 'All:All:All', 'ACCESS_ADMIN');
    //Planning
    xarRegisterMask('ViewPlanning', 'All', 'courses', 'Planning', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadPlanning', 'All', 'courses', 'Planning', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditPlanning', 'All', 'courses', 'Planning', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddPlanning', 'All', 'courses', 'Planning', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeletePlanning', 'All', 'courses', 'Planning', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminPlanning', 'All', 'courses', 'Planning', 'All:All:All', 'ACCESS_ADMIN');

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
        case '0.0.1':
        
    // Get database setup and make tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    //Table for Course years
    //This will be taken to dyn data.
    $courses_years = $xartable['courses_years'];
    $fields = array('xar_yearid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_year'=>array('null'=>FALSE, 'type'=>'varchar','size'=>25, 'default'=>'0')
        );
    // Create the Table
    $query = xarDBCreateTable($courses_years, $fields);
    if (empty($query)) return; // throw back

    $result = &$dbconn->Execute($query);
    if (!$result) return;
            return courses_upgrade('0.0.2');
            
        case '0.0.2':
    // Create table for teachers
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    $courses_teachers = $xartable['courses_teachers'];
    $fields = array('xar_tid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_userid' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_planningid' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_type' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0')
        );

    $query = xarDBCreateTable($courses_teachers, $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
            return courses_upgrade('0.0.3');
        
        case '0.0.3':
        // Upgrade instances
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $courses_teachers = $xartable['courses_teachers'];
    $courses_planning = $xartable['courses_planning'];
        
    //For the planning of courses
    $query1 = "SELECT DISTINCT xar_planningid FROM " . $courses_planning;
    $query2 = "SELECT DISTINCT xar_userid FROM " . $courses_teachers;
    $query3 = "SELECT DISTINCT xar_courseid FROM " . $courses_planning;
    $instances = array(
        array('header' => 'Planning ID:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'UserID of teacher:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Course ID:',
            'query' => $query3,
            'limit' => 20
            )
        );
    xarDefineInstance('Courses', 'Planning', $instances);
            return courses_upgrade('0.0.4');

        case '0.0.4':
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
    
    // xarDBLoadTableMaintenanceAPI();
    $courses_planning = $xartable['courses_planning'];
        $fields = "xar_minparticipants SMALLINT NOTNULL default 0,
        xar_maxparticipants SMALLINT NOTNULL default 0,
        xar_closedate DATE"
        ;
        xarDBLoadTableMaintenanceAPI();
        $result = $datadict->addColumn($courses_planning, $fields);

        if (!$result) return;
    
        // Apply changes
        xarDBLoadTableMaintenanceAPI();
        if (!$result) return;
            
            return courses_upgrade('0.0.5');
            
       case '0.0.5':
       break;
    }
    // Update successful
    return true;
}

/**
 * delete the courses module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function courses_delete()
{
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['courses']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;

     $query = xarDBDropTable($xartable['courses_students']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBDropTable($xartable['courses_planning']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // Delete any module variables
    xarModDelAllVars('courses');
    
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
       // Hook for module roles
    xarModAPIFunc('modules','admin','disablehooks',array('hookModName' => 'roles','callerModName' => 'courses'));

       // Hook for module search
    xarModAPIFunc('modules','admin','disablehooks',array('hookModName' => 'search','callerModName' => 'courses'));
        // Hook for module comments
    xarModAPIFunc('modules','admin','disablehooks',array('hookModName' => 'comments','callerModName' => 'courses'));
     // Hook for Categories
    xarModAPIFunc('modules','admin','disablehooks',array('hookModName' => 'categories','callerModName' => 'courses'));
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
