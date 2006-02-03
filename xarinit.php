<?php
/**
 * Julian initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module development team
 */

/**
 * Julian initialization functions
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 *
 * @author Julian module development team
 * @link changelog.txt for table definitions and descriptions
 */
function julian_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $event_table = $xartable['julian_events'];
    // Get a data dictionary object with all the item create methods in it
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $fields = "
              event_id      I       AUTO       PRIMARY,
              calendar_id   I8      unsigned NOTNULL default 0,
              type          I       NOTNULL default 0,
              organizer     I       NOTNULL default 0,
              contact       X       NOTNULL default '',
              url           C(200)  DEFAULT '',
              summary       C(255)  default '',
              description   X       NOTNULL default '',
              related_to    I       NULL,
              reltype       I       NULL,
              class         I       NOTNULL default 0,
              share_uids    C(255)  NULL,
              priority      I       NOTNULL default 0,
              status        I       NOTNULL default 1,
              location      X       NOTNULL DEFAULT '',
              street1       C(30)   NULL,
              street2       C(30)   NULL,
              city          C(30)   NULL,
              state         C(50)   NULL,
              zip           C(10)   NULL,
              phone         C(25)   NULL,
              email         C(70)   NULL,
              fee           C(10)   NULL,
              exdate        X       NOTNULL DEFAULT '',
              categories    X       NOTNULL DEFAULT '',
              rrule         I       NOTNULL DEFAULT 0,
              recur_freq    I       default 0,
              recur_until   T       NULL ,
              recur_count   I       default 0,
              recur_interval I      default 0,
              dtstart       T       NULL ,
              dtend         T       NULL ,
              duration      C(50)   NULL,
              isallday      I4      default 0,
              freebusy      X       NOTNULL DEFAULT '',
              due           T       NULL ,
              transp        I       NOTNULL default 1,
              created       C(20)   NOTNULL default '',
              last_modified T       NULL
              ";

    // Create or alter the table as necessary
    $result = $datadict->changeTable($event_table, $fields);
    if (!$result) {return;}

//default '0000-00-00 00:00:00'

    xarDBLoadTableMaintenanceAPI();
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Create new table for category-to-color linkage
    // cid: The category id (conforming to the 'categories' module.
    // Color: The category color for giving a visual to the user of the type of event
    $category_properties_table = $xartable['julian_category_properties'];
    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $category_properties_fields="
                                cid I NOTNULL default 0,
                                color C(15) NOTNULL default ''
                                ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($category_properties_table, $category_properties_fields);
    if (!$result) {return;}

    // Create separate table to store event information (date, time, etc.) for external (hooked) items.
    $event_linkage_table = $xartable['julian_events_linkage'];
    $event_linkage_fields = "
              event_id        I   AUTO    PRIMARY,
              hook_modid      I   NOTNULL DEFAULT 0,
              hook_itemtype   I   NOTNULL DEFAULT 0,
              hook_iid        I   NOTNULL DEFAULT 0,
              dtstart         T   NULL,
              duration        C(50)   NULL,
              isallday        L   DEFAULT 0,
              rrule           I   NOTNULL DEFAULT 0,
              recur_freq      I   DEFAULT 0,
              recur_count     I   DEFAULT 0,
              recur_interval  I   DEFAULT 0,
              recur_until     T   NULL
              ";
    $result = $datadict->changeTable($event_linkage_table, $event_linkage_fields);
    if (!$result) {return;}


     // Create the master category for Julian (in the categories module that will be hooked)
     $mastercid = xarModAPIFunc('categories', 'admin', 'create',
                                 array('name' => 'Julian',
                                                 'description' => 'Main Julian Calendar categories',
                                                 'parent_id' => 0));

     // Store info on the master category in the Julian module.
     xarModSetVar('julian', 'number_of_categories', 1);
     xarModSetVar('julian', 'mastercids', $mastercid);

     // Enable hooks to the 'categories' module for Julian
     xarModAPIFunc('modules','admin','enablehooks', array('callerModName' => 'julian', 'hookModName' => 'categories'));

    /*
    // Create alerts table
    $alerts_table = $xartable['julian_alerts'];
    $alerts_fields = array(
        // ID::the alert id, auto-increment
        'id'=>array('type'=>'integer','size'=>'11','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),

        // UID::user id
        'uid'=>array('type'=>'integer','size'=>'11','null'=>FALSE,'default'=>'0'),

        // UID::a serialized string of all the type events (events assoc. w/specific categories) the user wants to be alerted about via email
        'subscriptions'=>array('type'=>'varchar','size'=>'255','null'=>FALSE)
    );
    $sql = xarDBCreateTable($alerts_table,$alerts_fields);
    if (empty($sql)) return; // throw back
    if (!$dbconn->Execute($sql)) return;
    */

     // Register hooks: julian can couple a date+time to any item from any module, provided
     // the module in question calls (Julian's) hooks when editing items.
    if (!xarModRegisterHook('item', 'new',    'GUI', 'julian', 'user', 'newhook'))     return false;
    if (!xarModRegisterHook('item', 'create', 'API', 'julian', 'user', 'createhook'))  return false;
    if (!xarModRegisterHook('item', 'modify', 'GUI', 'julian', 'user', 'modifyhook'))  return false;
    if (!xarModRegisterHook('item', 'update', 'API', 'julian', 'user', 'updatehook'))  return false;
    if (!xarModRegisterHook('item', 'delete', 'API', 'julian', 'user', 'deletehook'))  return false;
    if (!xarModRegisterHook('item', 'display','GUI', 'julian', 'user', 'displayhook')) return false;


    // Register blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'julian',
                'blockType' => 'calevent'))) return;
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'julian',
                'blockType' => 'calmonth'))) return;

    // Register the Module Variables
    //Show ical links by default
    xarModSetVar('julian','ical_links',1);
    //Set default share group to "Users"
    xarModSetVar('julian','share_group',5);
     //Set default name for from address for calendar alerts
    xarModSetVar('julian','from_name','Xaraya_Site');
    //Set default from email address for calendar alerts
    xarModSetVar('julian','from_email','Julian_Calendar@yoursite.com');
    //Set default starting day of week
    xarModSetVar('julian','startDayOfWeek','1');
    //Set type of telephone field
    xarModSetVar('julian','TelFieldType','US');
    //Set the format for the date to display
    xarModSetVar('julian','dateformat','m/d/Y');
    //Set the format for the time to display
    xarModSetVar('julian','timeformat','h:i a');
    //Set the standard symbol for event categories
    xarModSetVar('julian','BulletForm','bull');
    //default alert supscriptions;none. Users can configure this
    xarModSetVar('julian','alerts','a:0:{}');
    //default number of items per page
    xarModSetVar('julian','numitems','10');
    // Duration minute interval
    xarModSetVar('julian', 'DurMinInterval', 15);
    // Starttime minute interval
    xarModSetVar('julian', 'StartMinInterval', 15);
    // Alias
    xarModSetVar('julian', 'useModuleAlias',false);
    xarModSetVar('julian','aliasname','');

    /**
     * Define instances for this module
     * Format is
     * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
     */

    $query1 = "SELECT DISTINCT event_id FROM " . $event_table;
    $query2 = "SELECT DISTINCT organizer FROM " . $event_table;
    $query3 = "SELECT DISTINCT class FROM " . $event_table;
    $query4 = "SELECT DISTINCT calendar_id FROM " . $event_table;
    $instances = array(
        array('header' => 'Event ID:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Organizer uid:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Class of calendar:',
            'query' => $query3,
            'limit' => 20
            ),
        array('header' => 'Calendar ID:',
            'query' => $query4,
            'limit' => 20
            )
        );
    xarDefineInstance('julian', 'Item', $instances);
/*
    // allow users to see the calendar w/ events
    xarRegisterMask('Viewjulian','All','julian','All','All','ACCESS_READ');
    // allows users to add events, but not categories
    xarRegisterMask('Editjulian','All','julian','All','All','ACCESS_EDIT');
    // allow full admin of the calendar
    xarRegisterMask('Adminjulian','All','julian','All','All','ACCESS_ADMIN');
*/

    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_title FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'julian'";
    $instances = array(
        array('header' => 'Julian Block Title:',
            'query' => $query,
            'limit' => 20
            )
        );
    xarDefineInstance('julian', 'Block', $instances);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */


    // Block persmissions
    xarRegisterMask('ReadJulianBlock', 'All', 'julian', 'Block', 'All', 'ACCESS_OVERVIEW');

    // New Masks $event_id:$organizer:$class:$calendar_id:$catid
    // calendar_id is not in use yet
    xarRegisterMask('ViewJulian', 'All', 'julian', 'Item', 'All:All:All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadJulian', 'All', 'julian', 'Item', 'All:All:All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditJulian', 'All', 'julian', 'Item', 'All:All:All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddJulian', 'All', 'julian', 'Item', 'All:All:All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteJulian', 'All', 'julian', 'Item', 'All:All:All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminJulian', 'All', 'julian', 'Item', 'All:All:All:All:All', 'ACCESS_ADMIN');



    return true;
}

/**
 *  Module Upgrade Function
 */
function julian_upgrade($oldversion)
{
        switch ($oldversion) {
        case '0.1.0':
            // Version 0.1.0 (0.1.1) had a smaller email field, we need to upgrade to VARCHAR(70)
            // in version 0.1.2

            $dbconn = xarDBGetConn();
            $xartable = xarDBGetTables();
            // Using the Datadict method to be up to date ;)
            $datadict = xarDBNewDataDict($dbconn, 'CREATE');

            $juliantable = xarDBgetSiteTablePrefix() . '_julian_events';
            // Apply changes
                        xarDBLoadTableMaintenanceAPI();
            $result = $datadict->alterColumn($juliantable, 'email varchar(70) Null');
            if (!$result) return;
            // At the end of the successful completion of this function we
            // recurse the upgrade to handle any other upgrades that need
            // to be done.  This allows us to upgrade from any version to
            // the current version with ease
                        return julian_upgrade('0.1.2');

        case '0.1.1':
                        // Same as for version 0.1.0
            $dbconn = xarDBGetConn();
            $xartable = xarDBGetTables();
                        // Using the Datadict method to be up to date ;)
                        $datadict = xarDBNewDataDict($dbconn, 'CREATE');
                        $juliantable = xarDBgetSiteTablePrefix() . '_julian_events';
            // Apply changes
                        xarDBLoadTableMaintenanceAPI();
            $result = $datadict->alterColumn($juliantable, 'email varchar(70) Null');
            if (!$result) return;
            return julian_upgrade('0.1.2');

        case '0.1.2':
           return julian_upgrade('0.1.3');

        case '0.1.3':
                            //Set default starting day of week
                            xarModSetVar('julian','startDayOfWeek','1');
                    return julian_upgrade('0.1.4');

        case '0.1.4':
                //should upgrade the telephone field here to 25
                        $dbconn = xarDBGetConn();
                        $xartable = xarDBGetTables();
                        // Using the Datadict method to be up to date ;)
                        $datadict = xarDBNewDataDict($dbconn, 'CREATE');
                        $juliantable = xarDBgetSiteTablePrefix() . '_julian_events';
                        // Apply changes
                        xarDBLoadTableMaintenanceAPI();
                        $result = $datadict->alterColumn($juliantable, 'phone char(25) Null');
            if (!$result) return;
                        return julian_upgrade('0.1.5');

        case '0.1.5':
            //Set type of telephone field
            xarModSetVar('julian','TelFieldType','US');
                        //Set number of days for event list
                        xarModSetVar('julian','EventBlockDays','7');
                        return julian_upgrade('0.1.6');

        case '0.1.6':
            //Set number of days for event list
            xarModSetVar('julian','dateformat','m/d/Y');
            //Set the format for the time to display
            xarModSetVar('julian','timeformat','h:i a');
            return julian_upgrade('0.1.7');

        case '0.1.7':
            // check if categories module is available
            if (!xarModIsAvailable('categories')) {
                $msg = xarML('The module [#(1)] should be activated first.', 'categories');
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MODULE_DEPENDENCY', new SystemException($msg));
                return;
            }

            // Create the master category for Julian (in the categories module that will be hooked)
            $mastercid = xarModAPIFunc('categories', 'admin', 'create',
                                        array('name' => 'Julian',
                                                         'description' => 'Main Julian Calendar categories',
                                                         'parent_id' => 0));

            // Store info on the master category in the Julian module.
            xarModSetVar('julian', 'number_of_categories', 1);
            xarModSetVar('julian', 'mastercids', $mastercid);

            // Enable hooks to the 'categories' module for Julian
            xarModAPIFunc('modules','admin','enablehooks', array('callerModName' => 'julian', 'hookModName' => 'categories'));

            xarDBLoadTableMaintenanceAPI();
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();

            // Create new table for category-to-color linkage
            $category_properties_table = $xartable['julian_category_properties'];
            $category_properties_fields = array(
                      // The category id (conforming to the 'categories' module.
                      'cid'=>array('type'=>'integer','null'=>false),

                      //The category color for giving a visual to the user of the type of event
                      'color'=>array('type'=>'varchar','size'=>'15','null'=>false,'default'=>''),
            );
            $sql = xarDBCreateTable($category_properties_table,$category_properties_fields);
            if (empty($sql)) return; // throw back
            if (!$dbconn->Execute($sql)) return;

            // Below: migrate existing categories (add them as children of the Julian master category)

            // Get existing Julian tables.
            $events_table = $xartable['julian_events'];
            $categories_table = $xartable['julian_categories'];

            // Get existing categories.
            $query = "SELECT cat_id,cat_name,color FROM " . $categories_table;
            $result = $dbconn->Execute($query);
            if (!$result) return;

            // Migrate exisiting categories to new categories in the 'categories' module.
            while (!$result->EOF) {
                list($oldcid,$cat_name,$color) = $result->fields;

                // Create new category as child of master category.
                $newcid = xarModAPIFunc('categories', 'admin', 'create',
                                          array('name' => $cat_name,
                                                'description' => $cat_name,
                                                'parent_id' => $mastercid));

                // Link existing color to newly migrated category.
                $query = "INSERT INTO $category_properties_table ( cid , color ) VALUES ($newcid,'$color')";
                $result_catprop = $dbconn->Execute($query);
                if (!$result_catprop) {
                    return;
                }

                // Get ids of the events that belong to this category.
                $query_events = "SELECT  event_id  FROM $events_table WHERE  categories ='$oldcid'";
                $result_events = $dbconn->Execute($query_events);
                if (!$result_events) return;

                // Make links between migrated category and associated events.
                $item = array('module' => 'julian');

                while (!$result_events->EOF) {
                      $item['cids'] = array($newcid);
                      $hooks = xarModCallHooks('item', 'create', $result_events->fields[0], $item);
                      $result_events->MoveNext();
                              }
                      $result_events->Close();

                      // Move to next category.
                      $result->MoveNext();
                }

                $result->Close();

                // Drop superfluous categories table.
                $query = xarDBDropTable($xartable['julian_categories']);
                if(empty($query)) return; //throw back
                if(!$dbconn->Execute($query)) return;

            return julian_upgrade('0.2.0');

        case '0.2.0':
            //Set the standard symbol for event categories
            xarModSetVar('julian','BulletForm','&bull;');
            //default alert supscriptions;none. Users can configure this
            xarModSetVar('julian','alerts','a:0:{}');

            // Drop superfluous alerts table.
            xarDBLoadTableMaintenanceAPI();
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();

            $query = xarDBDropTable($xartable['julian_alerts']);
            if(empty($query)) return; //throw back
            if(!$dbconn->Execute($query)) return;
            //Not necessary any more
            xarModDelVar('julian','EventBlockDays');

            return julian_upgrade('0.2.1');
         case '0.2.1':
            //Set the standard symbol for event categories
            xarModSetVar('julian','BulletForm','bull');
            // Register hooks: julian can couple a date+time to any item from any module, provided
            // the module in question calls (Julian's) hooks when editing items.

            if (!xarModRegisterHook('item', 'new',    'GUI', 'julian', 'user', 'newhook'))    return false;
            if (!xarModRegisterHook('item', 'create', 'API', 'julian', 'user', 'createhook')) return false;
            if (!xarModRegisterHook('item', 'modify', 'GUI', 'julian', 'user', 'modifyhook')) return false;
            if (!xarModRegisterHook('item', 'update', 'API', 'julian', 'user', 'updatehook')) return false;
            if (!xarModRegisterHook('item', 'delete', 'API', 'julian', 'user', 'deletehook')) return false;
            if (!xarModRegisterHook('item', 'display','GUI', 'julian', 'user', 'displayhook')) return false;

            xarDBLoadTableMaintenanceAPI();
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();

             // Create separate table to store event information (date, time, etc.) for external (hooked) items.
             $event_linkage_table = $xartable['julian_events_linkage'];
             $event_linkage_fields = array(
                      // UID::the linked-event id, auto-increment
                      'event_id'=>array('type'=>'integer','size'=>'medium','unsigned'=>TRUE,'null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                      'hook_modid'   =>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
                      'hook_itemtype'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
                      'hook_iid'     =>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
                      'dtstart'=>array('type'=>'datetime','size'=>'','null'=>FALSE,'default'=>''),
                      'duration'=>array('type'=>'varchar','size'=>'50','null'=>TRUE),
                      'isallday'=>array('type'=>'integer','size'=>'tiny','default'=>'0'),
                      'rrule'=>array('type'=>'text','null'=>TRUE),
                      'recur_freq'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
                      'recur_count'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
                      'recur_interval'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
                      'recur_until'=>array('type'=>'datetime','size'=>'','null'=>FALSE,'default'=>''),
             );
             $sql = xarDBCreateTable($event_linkage_table,$event_linkage_fields);
             if (empty($sql)) return; // throw back
             if (!$dbconn->Execute($sql)) return;

            return julian_upgrade('0.2.2');
        case '0.2.2':
            //default number of items per page
            xarModSetVar('julian','numitems','10');
            return julian_upgrade('0.2.3');

        case '0.2.3':
            /*
             * STATE::enlarge and change type from char(2)
             * 'state'=>array('type'=>'varchar','size'=>'50','null'=>TRUE)
             */
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict = xarDBNewDataDict($dbconn, 'CREATE');
            $juliantable = xarDBgetSiteTablePrefix() . '_julian_events';
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->alterColumn($juliantable, 'state C(50) Null');
            if (!$result) return;
            return julian_upgrade('0.2.4');
        case '0.2.4':
            /* Nothing yet
             * Remove masks and make new ones...?
             */
            // Duration minute interval
            xarModSetVar('julian', 'DurMinInterval', 15);
            // Starttime minute interval
            xarModSetVar('julian', 'StartMinInterval', 15);
            // Alias
            xarModSetVar('julian', 'useModuleAlias',false);
            xarModSetVar('julian','aliasname','');
            return julian_upgrade('0.2.6');
        case '0.2.6':
            /*
             * We have changed a lot of fields to be compatible with PostGres
             * This upgrade will introduce NULLs
             * Still left the time fields to do, will change later to int(11)
             */
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict = xarDBNewDataDict($dbconn, 'CREATE');
            $juliantable = xarDBgetSiteTablePrefix() . '_julian_events';
            // Apply changes
            xarDBLoadTableMaintenanceAPI();
            $result = $datadict->alterColumn($juliantable, 'url C(200) Default "" ' );
            if (!$result) return;
            $result = $datadict->alterColumn($juliantable, 'summary C(255) Default "" ');
            if (!$result) return;
            $result = $datadict->alterColumn($juliantable, 'exdate X NOTNULL DEFAULT "" ');
            if (!$result) return;
            $result = $datadict->alterColumn($juliantable, 'recur_until T NULL ');
            if (!$result) return;
            $result = $datadict->alterColumn($juliantable, 'dtstart T NULL ');
            if (!$result) return;
            $result = $datadict->alterColumn($juliantable, 'dtend T NULL ');
            if (!$result) return;
            $result = $datadict->alterColumn($juliantable, 'due T NULL ');
            if (!$result) return;

            $event_linkage_table = $xartable['julian_events_linkage'];
            $result = $datadict->alterColumn($event_linkage_table, 'dtstart T NULL ');
            if (!$result) return;
            $result = $datadict->alterColumn($event_linkage_table, 'rrule I NOTNULL DEFAULT 0 ');
            if (!$result) return;
            $result = $datadict->alterColumn($event_linkage_table, 'recur_until T NULL ');
            if (!$result) return;

            return julian_upgrade('0.2.7');
        case '0.2.7':
            // Remove Masks and Instances
            xarRemoveMasks('julian');
            xarRemoveInstances('julian');
            /**
             * Define instances for this module
             * Format is
             * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
             */
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $datadict = xarDBNewDataDict($dbconn, 'CREATE');
            $event_table = xarDBgetSiteTablePrefix() . '_julian_events';

            $query1 = "SELECT DISTINCT event_id FROM " . $event_table;
            $query2 = "SELECT DISTINCT organizer FROM " . $event_table;
            $query3 = "SELECT DISTINCT class FROM " . $event_table;
            $query4 = "SELECT DISTINCT calendar_id FROM " . $event_table;
            $instances = array(
                array('header' => 'Event ID:',
                    'query' => $query1,
                    'limit' => 20
                    ),
                array('header' => 'Organizer uid:',
                    'query' => $query2,
                    'limit' => 20
                    ),
                array('header' => 'Class of calendar:',
                    'query' => $query3,
                    'limit' => 20
                    ),
                array('header' => 'Calendar ID:',
                    'query' => $query4,
                    'limit' => 20
                    )
                );
            xarDefineInstance('julian', 'Item', $instances);
        /*
            // allow users to see the calendar w/ events
            xarRegisterMask('Viewjulian','All','julian','All','All','ACCESS_READ');
            // allows users to add events, but not categories
            xarRegisterMask('Editjulian','All','julian','All','All','ACCESS_EDIT');
            // allow full admin of the calendar
            xarRegisterMask('Adminjulian','All','julian','All','All','ACCESS_ADMIN');
        */

            $instancestable = $xartable['block_instances'];
            $typestable = $xartable['block_types'];
            $query = "SELECT DISTINCT i.xar_title FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'julian'";
            $instances = array(
                array('header' => 'Julian Block Title:',
                    'query' => $query,
                    'limit' => 20
                    )
                );
            xarDefineInstance('julian', 'Block', $instances);

            /**
             * Register the module components that are privileges objects
             * Format is
             * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
             */


            // Block persmissions
            xarRegisterMask('ReadJulianBlock', 'All', 'julian', 'Block', 'All', 'ACCESS_OVERVIEW');

            // New Masks $event_id:$organizer:$class:$calendar_id:$catid
            // calendar_id is not in use yet
            xarRegisterMask('ViewJulian', 'All', 'julian', 'Item', 'All:All:All:All:All', 'ACCESS_OVERVIEW');
            xarRegisterMask('ReadJulian', 'All', 'julian', 'Item', 'All:All:All:All:All', 'ACCESS_READ');
            xarRegisterMask('EditJulian', 'All', 'julian', 'Item', 'All:All:All:All:All', 'ACCESS_EDIT');
            xarRegisterMask('AddJulian', 'All', 'julian', 'Item', 'All:All:All:All:All', 'ACCESS_ADD');
            xarRegisterMask('DeleteJulian', 'All', 'julian', 'Item', 'All:All:All:All:All', 'ACCESS_DELETE');
            xarRegisterMask('AdminJulian', 'All', 'julian', 'Item', 'All:All:All:All:All', 'ACCESS_ADMIN');

            return julian_upgrade('0.2.8');
        case '0.2.8':
            break;
    }
    // Update successful
    return true;
}

/**
 *  Module Delete Function
 */
function julian_delete()
{
    /* Get database setup */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    // Initialise table array
    $basename = 'julian';

    foreach(array('events', 'events_linkage', 'category_properties') as $table) {

    /* Drop the tables */
     $result = $datadict->dropTable($xartable[$basename . '_' . $table]);
    }

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'julian',
                'blockType' => 'calevent'))) return;
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'julian',
                'blockType' => 'calmonth'))) return;

    // remove all module vars
    xarModDelAllVars('julian');

    // Remove Masks and Instances
    xarRemoveMasks('julian');
    xarRemoveInstances('julian');
    return true;
}

?>
