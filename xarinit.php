<?php
/**
 * Courses initialization functions
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author original idea: XarayaGeek, MichelV.
 */

/**
 * Initialise the courses module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @return bool true on success
 */
function courses_init()
{
    // Get database setup and make tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // The courses table
    $coursestable = $xartable['courses'];
    xarDBLoadTableMaintenanceAPI();
    $fields = array(
        'xar_courseid'      => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_name'          => array('type' => 'varchar', 'size' => 100, 'null' => false),
        'xar_number'        => array('type' => 'varchar', 'size' => 5, 'null' => false),
        'xar_type'          => array('type' => 'varchar', 'size' => 10, 'default' => 'NULL'),
        'xar_level'         => array('type' => 'varchar', 'size' => 20, 'default' => 'NULL'),
        'xar_shortdesc'     => array('null'=>FALSE, 'type'=>'text'),
      //  'xar_intendedcredits' => array('type' => 'integer', 'size' => 30, 'default' => 'NULL'),
        'xar_intendedcredits' => array('type'=>'float', 'size' =>'decimal', 'width'=>5, 'decimals'=>2),
        'xar_freq'          => array('null'=>TRUE, 'type' => 'varchar', 'size' => 20, 'default' => 'NULL'),
        'xar_contactuid'    => array('type' => 'integer', 'size' => 'medium', 'null' => true, 'default' => 'NULL'),
        'xar_contact'       => array('null'=>TRUE, 'type' => 'varchar', 'size' => 255, 'default' => 'NULL'),
        'xar_hidecourse'    => array('type' => 'integer', 'size' => 'tiny', 'null' => false, 'default' => '0'),
        'xar_last_modified' => array('type'=>'integer','size' => 11,'null'=>FALSE)
        );

     $query = xarDBCreateTable($coursestable, $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // The course will be planned for each occurence
    $courses_planning = $xartable['courses_planning'];
    $fields = array(
        'xar_planningid'    => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_courseid'      => array('type' => 'integer', 'null' => false),
       // 'xar_credits'       => array('type' => 'integer', 'size' => 'tiny', 'unsigned'=>TRUE, 'null' => false, 'default' => '0'),
      //  'xar_creditsmin'    => array('type' => 'integer', 'size' => 'tiny', 'unsigned'=>TRUE, 'null' => false, 'default' => '0'),
      //  'xar_creditsmax'    => array('type' => 'integer', 'size' => 'tiny', 'unsigned'=>TRUE, 'null' => false, 'default' => '0'),
        'xar_credits'       => array('type'=>'float', 'size' =>'decimal', 'width'=>5, 'decimals'=>2),
        'xar_creditsmin'    => array('type'=>'float', 'size' =>'decimal', 'width'=>5, 'decimals'=>2),
        'xar_creditsmax'    => array('type'=>'float', 'size' =>'decimal', 'width'=>5, 'decimals'=>2),
        'xar_courseyear'    => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_startdate'     => array('type'=>'integer','size' => 11,'null'=>FALSE, 'default' => '0'),
        'xar_enddate'       => array('type'=>'integer','size' => 11,'null'=>FALSE, 'default' => '0'),
        'xar_prerequisites' => array('null'=>FALSE, 'type'=>'text'),
        'xar_aim'           => array('null'=>TRUE, 'type'=>'text'),
        'xar_method'        => array('null'=>TRUE, 'type'=>'text'),
        'xar_language'      => array('null'=>TRUE, 'type'=>'varchar', 'size'=>100),
        'xar_longdesc'      => array('null'=>FALSE, 'type'=>'text'),
        'xar_costs'         => array('null'=>FALSE, 'type'=>'varchar','size'=>255, 'default'=>''),
        'xar_committee'     => array('null'=>TRUE, 'type'=>'text',),
        'xar_coordinators'  => array('null'=>TRUE, 'type'=>'text'),
        'xar_lecturers'     => array('null'=>TRUE, 'type'=>'text'),
        'xar_location'      => array('null'=>TRUE, 'type'=>'text',),
        'xar_material'      => array('null'=>TRUE, 'type'=>'text'),
        'xar_info'          => array('null'=>TRUE, 'type'=>'text'),
        'xar_program'       => array('null'=>TRUE, 'type'=>'text'),
        'xar_hideplanning'  => array('type' => 'integer', 'size' => 'tiny', 'null' => false, 'default' => '0'),
        'xar_minparticipants' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_maxparticipants' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_closedate'     => array('type'=>'integer','size' => 11,'null'=>FALSE, 'default' => '0'),
        'xar_last_modified' => array('type'=>'integer','size' => 11,'null'=>FALSE, 'default' => '0')
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
        'xar_status' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_regdate'=>array('type'=>'integer','size' => 11,'null'=>FALSE)
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

    //Table for Course type
    //These types will be the itemtypes
    $courses_types = $xartable['courses_types'];
    $fields = array('xar_tid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_type'=>array('null'=>FALSE, 'type'=>'varchar','size'=>50, 'default'=>''),
        'xar_descr'=>array('null'=>FALSE, 'type'=>'varchar','size'=>255, 'default'=>''),
        'xar_settings'=>array('null'=>FALSE, 'type'=>'varchar','size'=>255, 'default'=>'')
        );
    // Create the Table
    $query = xarDBCreateTable($courses_types, $fields);
    if (empty($query)) return; // throw back

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
    // Set up an initial value for a module variable.
    xarModSetVar('courses', 'HideEmptyFields', 0);
    xarModSetVar('courses', 'itemsperpage', 10);
    xarModSetVar('courses', 'AlwaysNotify', 'webmaster@yoursite.com');
    // If your module supports short URLs, the website administrator should
    // be able to turn it on or off in your module administration
    xarModSetVar('courses', 'SupportShortURLs', 0);
    // Number of days for the upcoming block to look ahead
    xarModSetVar('courses', 'BlockDays', 7);
    // Number of months that a planning in the past will be shown (today - months = last occurence shown)
    xarModSetVar('courses', 'OldPlannedMonths', 12);
    // Messages
    xarModSetVar('courses', 'hidecoursemsg', 'This course is currently hidden for display');
    xarModSetVar('courses', 'hideplanningmsg', 'This occurence is currently hidden for display');
    // Set standard group to users
    xarModSetVar('courses', 'coord_group', 5);
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
    // Register blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'courses',
                'blockType' => 'upcoming'))) return;
    // Register our hooks that we are providing to other modules.  The course
    // module shows a hook in the form of the user menu that shows the user the courses
    // he or she is enrolled in.
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'courses', 'user', 'usermenu')) {
        return false;
    }
    // User interface for Search
    if (!xarModRegisterHook('item', 'search', 'GUI',
                                    'courses', 'user', 'search')) {
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

     // Hook for Categories
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'categories'
            ,'callerModName'    => 'courses'));
/*    // Hook for Dynamic Data
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'dynamicdata'
            ,'callerModName'    => 'courses'));
    /*
     *
     * REGISTER THE TABLES AT DYNAMICDATA
     */
    $path = "modules/courses/xardata/";
    // Course levels
    $objectid = xarModAPIFunc('dynamicdata','util','import',array('file'  => $path . '/courses_levels.xml'));
    if (empty($objectid)) return;
    xarModSetVar('courses','levelsobjectid',$objectid);

    $objectid = xarModAPIFunc('dynamicdata','util','import',array('file'  => $path . '/courses_levels_data.xml'));
    if (empty($objectid)) return;

    // Student status
    $objectid = xarModAPIFunc('dynamicdata','util','import',array('file'  => $path . '/courses_studstatus.xml'));
    if (empty($objectid)) return;
    xarModSetVar('courses','studstatusobjectid',$objectid);

    $objectid = xarModAPIFunc('dynamicdata','util','import',array('file'  => $path . '/courses_studstatus_data.xml'));
    if (empty($objectid)) return;

    // Course levels
    $objectid = xarModAPIFunc('dynamicdata','util','import',array('file'  => $path . '/courses_years.xml'));
    if (empty($objectid)) return;
    xarModSetVar('courses','yearsobjectid',$objectid);

    $objectid = xarModAPIFunc('dynamicdata','util','import',array('file'  => $path . '/courses_years_data.xml'));
    if (empty($objectid)) return;

    /**
     * Define instances for this module
     * Format is
     * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
     * CourseID: Per course
     * PlanningID: Per Planned Course
     * YearID: per year. In combination with courses
     */

    $query1 = "SELECT DISTINCT xar_courseid FROM " . $coursestable; // Check for the courseid
    $query2 = "SELECT DISTINCT xar_planningid FROM " . $courses_planning; // Make a planned course selectable
    $query3 = "SELECT DISTINCT xar_yearid FROM " . $courses_years; // Specify per year
    $instances = array(
        array('header' => 'Course ID:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Planning ID:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Year ID:',
            'query' => $query3,
            'limit' => 20
            )
        );
    xarDefineInstance('Courses', 'Course', $instances);

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
    // The courses themselves need to be adminable
    xarRegisterMask('ReadCoursesBlock', 'All', 'courses', 'Block', 'All', 'ACCESS_OVERVIEW');
    // courseid:planningid:yearid
    xarRegisterMask('ViewCourses',      'All', 'courses', 'Course', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadCourses',      'All', 'courses', 'Course', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditCourses',      'All', 'courses', 'Course', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddCourses',       'All', 'courses', 'Course', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteCourses',    'All', 'courses', 'Course', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminCourses',     'All', 'courses', 'Course', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * upgrade the courses module from an old version
 * This function can be called multiple times
 * @param string oldversion
 * @return bool true on success
 * @throws DATABASE_ERROR
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
            xarModSetVar('courses', 'HideEmptyFields', 0);
            return courses_upgrade('0.0.6');
       case '0.0.6':
            if (!xarModRegisterHook('item', 'search', 'GUI',
                                    'courses', 'user', 'search')) {
                return false;
            }
            return courses_upgrade('0.0.7');
       case '0.0.7':
            // Add last modified column to coursestable
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            $coursestable = $xartable['courses'];
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->addColumn($coursestable, 'xar_last_modified datetime');
            if (!$result) return;

            // Add last modified column to planningtable
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            $planningtable = $xartable['courses_planning'];
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->addColumn($planningtable, 'xar_last_modified datetime');
            if (!$result) return;

            // Add last modified column to studentstable
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            $studentstable = $xartable['courses_students'];
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->addColumn($studentstable, 'xar_regdate datetime');
            if (!$result) return;

            // Privilege for teachers
            xarRegisterPrivilege('PlanningForTeacher','All','courses','Planning','All','ACCESS_EDIT',xarML('Teacher access'));

            return courses_upgrade('0.0.8');
       case '0.0.8':
            // Set the always receive e-mail
            xarModSetVar('courses', 'AlwaysNotify', 'webmaster@yoursite.com');
            // Remove Masks and Instances
            // these functions remove all the registered masks and instances of a module
            // from the database. This is not strictly necessary, but it's good housekeeping.
            xarRemoveMasks('courses');
            xarRemoveInstances('courses');
            xarRemovePrivileges('courses');
            // New Privileges
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            // The courses table
            $coursestable = $xartable['courses'];
            $courses_planning = $xartable['courses_planning'];
            $courses_years = $xartable['courses_years'];
            //Create new masks and privileges.
            $query1 = "SELECT DISTINCT xar_courseid FROM " . $coursestable; // Check for the courseid
            $query2 = "SELECT DISTINCT xar_planningid FROM " . $courses_planning; // Make a planned course selectable
            $query3 = "SELECT DISTINCT xar_yearid FROM " . $courses_years; // Specify per year
            $instances = array(
                array('header' => 'Course ID:',
                    'query' => $query1,
                    'limit' => 20
                    ),
                array('header' => 'Planning ID:',
                    'query' => $query2,
                    'limit' => 20
                    ),
                array('header' => 'Year ID:',
                    'query' => $query3,
                    'limit' => 20
                    )
                );
            xarDefineInstance('Courses', 'Course', $instances);

            // Create new masks
            xarRegisterMask('ReadCoursesBlock', 'All', 'courses', 'Block', 'All', 'ACCESS_OVERVIEW');
            xarRegisterMask('ViewCourses', 'All', 'courses', 'Course', 'All:All:All', 'ACCESS_OVERVIEW');
            xarRegisterMask('ReadCourses', 'All', 'courses', 'Course', 'All:All:All', 'ACCESS_READ');
            xarRegisterMask('EditCourses', 'All', 'courses', 'Course', 'All:All:All', 'ACCESS_EDIT');
            xarRegisterMask('AddCourses', 'All', 'courses', 'Course', 'All:All:All', 'ACCESS_ADD');
            xarRegisterMask('DeleteCourses', 'All', 'courses', 'Course', 'All:All:All', 'ACCESS_DELETE');
            xarRegisterMask('AdminCourses', 'All', 'courses', 'Course', 'All:All:All', 'ACCESS_ADMIN');

       //Change table layout
            // Add contactuid to coursestable
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            $coursestable = $xartable['courses'];
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->addColumn($coursestable, 'xar_contactuid integer(medium) null default(NULL)');
            if (!$result) return;

            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            // Using the Datadict method to be up to date ;)
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            $coursestable = $xartable['courses'];
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->alterColumn($coursestable, 'xar_name varchar(100) NOTNUll');
            if (!$result) return;

            return courses_upgrade('0.0.9');
       case '0.0.9':

            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            // Using the Datadict method to be up to date ;)
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            $planningtable = $xartable['courses_planning'];
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->alterColumn($planningtable, 'xar_startdate date NOTNULL 00-00-0000');
            if (!$result) return;

            $result = $datadict->alterColumn($planningtable, 'xar_enddate date NOTNULL 00-00-0000');
            if (!$result) return;

            return courses_upgrade('0.1.0');
        case '0.1.0':
            // Add language to planningtable
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            $planningtable = $xartable['courses_planning'];
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->addColumn($planningtable, 'xar_language varchar(100) null default(NULL)');
            if (!$result) return;

            // Using the Datadict method to be up to date ;)
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            $coursestable = $xartable['courses'];
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->dropColumn($coursestable, 'xar_language');
            if (!$result) return;

            return courses_upgrade('0.1.1');
        case '0.1.1':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            $coursestable = $xartable['courses'];
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->addColumn($coursestable, 'xar_intendedcredits varchar(30) null default(NULL)');
            if (!$result) return;

            return courses_upgrade('0.1.2');

        case '0.1.2':
            // Register blocks
            if (!xarModAPIFunc('blocks',
                               'admin',
                               'register_block_type',
                               array('modName' => 'courses',
                                     'blockType' => 'upcoming'))) return;
            xarModSetVar('courses', 'BlockDays', 7);
            return courses_upgrade('0.1.3');
        case '0.1.3':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            $planningtable = $xartable['courses_planning'];
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->alterColumn($planningtable, "xar_costs C(255) NOTNULL Default '' ");
            if (!$result) return;
            return courses_upgrade('0.1.4');
        case '0.1.4':
        case '0.2.0':
            xarModSetVar('courses', 'hidecoursemsg', 'This course is currently hidden for display');
            xarModSetVar('courses', 'hideplanningmsg', 'This occurence is currently hidden for display');

            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            $coursestable = $xartable['courses'];
            // Apply change to int for time() functions
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->alterColumn($coursestable, 'xar_last_modified I4 null default 0');
            if (!$result) return;

            $courses_students = $xartable['courses_students'];
            $result = $datadict->alterColumn($courses_students, 'xar_regdate I4 null default 0');
            if (!$result) return;

            $courses_planning = $xartable['courses_planning'];
            $result = $datadict->alterColumn($courses_planning, 'xar_last_modified I4 null default 0');
            if (!$result) return;
            $result = $datadict->alterColumn($courses_planning, 'xar_closedate I4 null default 0');
            if (!$result) return;
            $result = $datadict->alterColumn($courses_planning, 'xar_startdate I4 null default 0');
            if (!$result) return;
            $result = $datadict->alterColumn($courses_planning, 'xar_enddate I4 null default 0');
            if (!$result) return;
            return courses_upgrade('0.2.1');
        case '0.2.1':
            // Change lay out of date fields to time() format
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            xarDBLoadTableMaintenanceAPI();
            $courses_planning = $xartable['courses_planning'];
            $result = $datadict->alterColumn($courses_planning, 'xar_last_modified I4 null default 0');
            if (!$result) return;
            $result = $datadict->alterColumn($courses_planning, 'xar_closedate I4 null default 0');
            if (!$result) return;
            $result = $datadict->alterColumn($courses_planning, 'xar_startdate I4 null default 0');
            if (!$result) return;
            $result = $datadict->alterColumn($courses_planning, 'xar_enddate I4 null default 0');
            if (!$result) return;
            return courses_upgrade('0.2.2');
        case '0.2.2':
        case '0.2.3':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            xarDBLoadTableMaintenanceAPI();
            //Table for Course type
            //These types will be the itemtypes
            $courses_types = $xartable['courses_types'];
            $fields = array('xar_tid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
                            'xar_type'=>array('null'=>FALSE, 'type'=>'varchar','size'=>50, 'default'=>''),
                            'xar_descr'=>array('null'=>FALSE, 'type'=>'varchar','size'=>255, 'default'=>''),
                            'xar_settings'=>array('null'=>FALSE, 'type'=>'varchar','size'=>255, 'default'=>'')
                            );
            // Create the Table
            $query = xarDBCreateTable($courses_types, $fields);
            if (empty($query)) return; // throw back

            $result = &$dbconn->Execute($query);
            if (!$result) return;
        case '0.3.0':
        case '0.3.1':
            xarModSetVar('courses', 'coord_group', 5);
        case '0.3.2':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            // Using the Datadict method to be up to date ;)
            $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
            $coursestable = $xartable['courses'];
            $planningtable = $xartable['courses_planning'];
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->alterColumn($planningtable, 'xar_credits N(5.2)');
            $result = $datadict->alterColumn($planningtable, 'xar_creditsmin N(5.2)');
            $result = $datadict->alterColumn($planningtable, 'xar_creditsmax N(5.2)');
            $result = $datadict->alterColumn($coursestable, 'xar_intendedcredits N(5.2)');
            if (!$result) return;
        case '0.4.0':
            // Number of months that a planning in the past will be shown (today - months = last occurence shown)
            xarModSetVar('courses', 'OldPlannedMonths', 12);
        case '0.4.1':
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the courses module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @author MichelV <michelv@xarayahosting.nl>
 * @return bool true on success
 */
function courses_delete()
{
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
   // xarDBLoadTableMaintenanceAPI();
    // Generate the SQL to drop the table using the API
  //  $query = xarDBDropTable($xartable['courses']);
  //  if (empty($query)) return; // throw back

    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $result = $datadict->dropTable($xartable['courses']);
    // Initialise table array
    $basename = 'courses';

    foreach(array('students', 'planning', 'teachers', 'types') as $table) {
        /* Drop the tables */
         $result = $datadict->dropTable($xartable[$basename . '_' . $table]);
    }

    /* Drop the Dyn data objects */
    $objectid = xarModGetVar('courses','studstatusobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }
    /* Drop the Dyn data objects */
    $objectid = xarModGetVar('courses','levelsobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }
    /* Drop the Dyn data objects */
    $objectid = xarModGetVar('courses','yearsobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }


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

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'courses',
                'blockType' => 'upcoming'))) return;

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
    xarModAPIFunc('modules','admin','disablehooks',array('hookModName' => 'dynamicdata','callerModName' => 'courses'));
    // Hook for Mail
    xarModAPIFunc('modules','admin','disablehooks',array('hookModName' => 'mail',     'callerModName'   => 'courses'));
    /*
     * REMOVE all comments (which are stored via the comments api)
     */
    xarModAPIFunc('comments','admin','delete_module_nodes',array('modid' => xarModGetIDFromName('courses')));

    // Deletion successful
    return true;
}

?>