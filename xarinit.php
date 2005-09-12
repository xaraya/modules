<?php
/**
 *  Module Initialisation Function
 *  @version $Id: xarinit.php,v 1.21 2005/06/24 10:10:40 michelv01 Exp $
 */
function julian_init()
{
    // check if categories module is available
    if (!xarModIsAvailable('categories')) {
        $msg = xarML('The module [#(1)] should be activated first.', 'categories');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MODULE_DEPENDENCY', new SystemException($msg));
        return;
    }

    xarDBLoadTableMaintenanceAPI();
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    // draw up the design for the events table
    // this table will hold the required event information
    // this includes the event owner, start and end datetimes
    // recurrence rules and exception dates.  Most of the data
    // will be held in the icalendar format with the exception
    // of CLASS, STATUS and TRANSP.
    // 
    $event_table = $xartable['julian_events'];
    $event_fields = array(
        // UID::the event id, auto-increment
        'event_id'=>array('type'=>'integer','size'=>'medium','unsigned'=>TRUE,'null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        
        // the calendar this event belongs to
        'calendar_id'=>array('type'=>'integer','unsigned'=>TRUE,'size'=>'medium','null'=>FALSE,'default'=>'0'),
        
        // what type of calendar object is this 
        // 0 = CAL_TYPE_VEVENT
        // 1 = CAL_TYPE_VTODO
        // 2 = CAL_TYPE_VJOURNAL
        // 3 = CAL_TYPE_VFREEBUSY
        'type'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        
        // ORGANIZER::the user-id of the event's owner
        'organizer'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        
        // CONTACT::event contact
        'contact'=>array('type'=>'text','null'=>TRUE),
        
        // URL::event url
        'url'=>array('type'=>'text','size'=>'tiny','null'=>TRUE),
        
        // SUMMARY::the event title / one line summary
        'summary'=>array('type'=>'varchar','size'=>'80','null'=>TRUE),
        
        // DESCRIPTION::the event description
        'description'=>array('type'=>'text','null'=>TRUE),
        
        // RELATED-TO::this event is related to another (event_id)
        'related_to'=>array('type'=>'integer','null'=>TRUE),
            
        // RELTYPE::the type of relationship
        // 0 = CAL_RELTYPE_PARENT
        // 1 = CAL_RELTYPE_CHILD
        // 2 = CAL_RELTYPE_SIBLING
        'reltype'=>array('type'=>'integer','null'=>TRUE),
        
        // CLASS::the event class
        // 0 = CAL_CLASS_PUBLIC
        // 1 = CAL_CLASS_PRIVATE
        // 2 = CAL_CLASS_CONFIDENTIAL
        'class'=>array('type'=>'integer','null'=>FALSE),
        
        // SHARE_UIDS::ids to restrict shared view of an event
        //stored as a comma delimitted string
        'share_uids'=>array('type'=>'varchar','size'=>'255'),
        
        // PRIORITY::the priority of this event [0-9]
        'priority'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        
        // STATUS::the status of this event 
        // 0 = CAL_STATUS_TENTATIVE
        // 1 = CAL_STATUS_CONFIRMED
        // 2 = CAL_STATUS_CANCELLED
        // 3 = CAL_STATUS_NEEDS_ACTION (default)
        // 4 = CAL_STATUS_COMPLETED
        // 5 = CAL_STATUS_IN-PROCESS
        // 6 = CAL_STATUS_DRAFT   
        // 7 = CAL_STATUS_FINAL    
        'status'=>array('type'=>'integer','null'=>FALSE, 'default'=>'1'),
        
        // LOCATION::the event location
        'location'=>array('type'=>'text','null'=>TRUE),
        
        // STREET1::the street of the location
        'street1'=>array('type'=>'varchar','size'=>'30','null'=>TRUE),
        
        // STREET2::the street of the location
        'street2'=>array('type'=>'varchar','size'=>'30','null'=>TRUE),
        
        // CITY::the city of the location
        'city'=>array('type'=>'varchar','size'=>'30','null'=>TRUE),
        
        // STATE::the state of the location
        'state'=>array('type'=>'char','size'=>'2','null'=>TRUE),
        
        // ZIP::the zipcode of the location
        'zip'=>array('type'=>'varchar','size'=>'10','null'=>TRUE),
        
        // PHONE::the phone number of the contact
                // Version 0.1.5 enlarged to 25, was 14
        'phone'=>array('type'=>'char','size'=>'25','null'=>TRUE),
        
        // EMAIL::the email address of the contact
        'email'=>array('type'=>'varchar','size'=>'70','null'=>TRUE), 
        
        // FEE::the fee for the event
        'fee'=>array('type'=>'varchar','size'=>'10','null'=>TRUE),       
        
        // EXDATE::event exceptions YYYYMMDDHHMMSS,YYYYMMDDHHMMSS,etc.
        'exdate'=>array('type'=>'text','null'=>TRUE),
        
        // CATEGORIES::event category
        'categories'=>array('type'=>'text','null'=>TRUE),
        
        // RRULE::event recurrence rule
        // TODO::Break this out into its components
        // FREQ::Recurrence Frequency
        // 0 = NO REPEATING
        // 1 = CAL_RECUR_FREQ_DAILY
        // 2 = CAL_RECUR_FREQ_WEEKLY
        // 3 = CAL_RECUR_FREQ_MONTHLY
        // 4 = CAL_RECUR_FREQ_YEARLY
        'rrule'=>array('type'=>'text','null'=>TRUE),
        
        // RECUR_FREQ::how often to repeat rule
        'recur_freq'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
        
        // UNTIL::Recurrence End Date (YYYYMMDDHHMMSS)
        // This should always be stored as UTC
        // Can not exist if COUNT is not null
        // MODIFIED: Changed type from varchar(14) to datetime ~DS
        'recur_until'=>array('type'=>'datetime','size'=>'','null'=>FALSE),// Bug 4942 removed ,'default'=>''
        
        // COUNT::Recurrence Count
        // Can not exist if UNTIL is not null
        
        // ADDED ~DS
        // 0 = NO REPEATING
        // 1 = SUNDAY
        // ...
        // 7 = SATURDAY
        'recur_count'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
        
        // INTERVAL::Recurrence Interval
        
        // ADDED ~DS
        // 0 = NO REPEATING
        // 1 = FIRST
        // 2 = SECOND
        // 3 = THIRD
        // 4 = FOURTH
        // 5 = LAST
        'recur_interval'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
        
        // DTSTART::event start date/time
        // MODIFIED: Changed type from varchar(14) to datetime ~DS
        'dtstart'=>array('type'=>'datetime','size'=>'','null'=>FALSE),// Bug 4942 removed ,'default'=>''
        
        // DTEND::event end date/time
        // MODIFIED: Changed type from varchar(14) to datetime ~DS
        'dtend'=>array('type'=>'datetime','size'=>'','null'=>FALSE),// Bug 4942 removed ,'default'=>''
        
        // DURATION::how long the event lasts
        'duration'=>array('type'=>'varchar','size'=>'50','null'=>TRUE),
        
        // ISALLDAY::boolean flag indicating if event is all day
        'isallday'=>array('type'=>'integer','size'=>'tiny','default'=>'0'),
        
        // FREEBUSY::freebusy information
        'freebusy'=>array('type'=>'text','null'=>TRUE),
        
        // DUE::This property defines the date and time that a to-do is expected to be completed.
        // MODIFIED: Changed type from varchar(14) to datetime ~DS
        'due'=>array('type'=>'datetime','size'=>'','null'=>FALSE),// Bug 4942 removed ,'default'=>''
        
        // TRANSP::event transparency 
        // 0 = CAL_TRANSP_OPAQUE
        // 1 = CAL_TRANSP_TRANSPARENT (DEFAULT)
        'transp'=>array('type'=>'integer','null'=>FALSE,'default'=>'1'),
        
        // CREATED::the date/time the event was created
        // MODIFIED: Changed type from varchar(20) to datetime ~DS
        'created'=>array('type'=>'varchar','size'=>'20','null'=>FALSE,'default'=>''),
        
        // LAST-MODIFED::the date/time the event was last modified
        // MODIFIED: Changed type from varchar(14) to datetime ~DS
        'last_modified'=>array('type'=>'datetime','size'=>'','null'=>FALSE)// Bug 4942 removed ,'default'=>''
    );
    $sql = xarDBCreateTable($event_table,$event_fields);
    if (empty($sql)) return; // throw back
    if (!$dbconn->Execute($sql)) return;

    // The ATTENDEE table
    // this table is used to link up attendees and resources to an event
    /*$attendee_table = $xartable['julian_attendees'];
    $attendee_fields = array(
        // The attendee id
        'attendee_id'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        
        // The user's id on this site if available
        'user_id'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
        
        // the event id this attendee belongs to
        'event_id'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
        
        // CUTYPE::indicates the type of calendar user
        // 0 = CAL_CUTYPE_INDIVIDUAL
        // 1 = CAL_CUTYPE_GROUP
        // 2 = CAL_CUTYPE_RESOURCE
        // 3 = CAL_CUTYPE_ROOM
        // 4 = CAL_CUTYPE_UNKNOWN
        'cutype'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        
        // MEMBER::indicates the groups that the attendee belongs to
        // To specify the group or list membership of the calendar user
        // specified by the property.
        'member'=>array('type'=>'text','null'=>TRUE),
        
        // ROLE::the intended role that the attendee will have in the calendar component
        // 0 = CAL_ROLE_CHAIR
        // 1 = CAL_ROLE_REQ_PARTICIPANT (default)
        // 2 = CAL_ROLE_OPT_PARTICIPANT
        // 3 = CAL_ROLE_NON_PARTICIPANT
        'role'=>array('type'=>'integer','null'=>FALSE,'default'=>'1'),
        
        // PARTSTAT::status of the attendee's participation
        // 0 = CAL_PARTSTAT_NEEDS_ACTION   [ vevent, vtodo, vjournal ] (default)
        // 1 = CAL_PARTSTAT_ACCEPTED       [ vevent, vtodo, vjournal ]
        // 2 = CAL_PARTSTAT_DECLINED       [ vevent, vtodo, vjournal ]
        // 3 = CAL_PARTSTAT_TENTATIVE      [ vevent, vtodo ]
        // 4 = CAL_PARTSTAT_DELEGATED      [ vevent, vtodo ]
        // 5 = CAL_PARTSTAT_COMPLETED      [ vtodo ]
        // 6 = CAL_PARTSTAT_IN_PROCESS     [ vtodo ]
        'partstat'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        
        // RSVP::indicating whether the favor of a reply is requested
        'rsvp'=>array('type'=>'boolean'),
        
        // DELEGATED-TO::indicates the calendar users that the original request was delegated to
        'delegated_to'=>array('type'=>'text','null'=>TRUE),
        
        // DELEGATED-FROM::indicates whom the request was delegated from
        'delegated_from'=>array('type'=>'text','null'=>TRUE),
        
        // SENT-BY::indicates whom is acting on behalf of the ATTENDEE
        'sent_by'=>array('type'=>'varchar','size'=>'80','null'=>TRUE),
        
        // CN::the common or displayable name associated with the calendar address
        'cn'=>array('type'=>'varchar','size'=>'80','null'=>TRUE),
        
        // DIR::indicates the URI that points to the directory information 
        // corresponding to the attendee
        'dir'=>array('type'=>'text','null'=>TRUE)
    );
    $sql = xarDBCreateTable($attendee_table,$attendee_fields);
    if (empty($sql)) return; // throw back
    if (!$dbconn->Execute($sql)) return;*/
    
    // VALARM table will hold data for event alarm triggers
    /*$alarms_table = $xartable['julian_alarms'];
    $alarms_fields = array(
        // The alarm id
        'alarm_id'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        
        // The user's id on this site if available
        'user_id'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
        
        // the event id this alarm belongs to if available
        'event_id'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
        
        // TRIGGER::This property specifies when an alarm will trigger.
        // some valid examples of a TRIGGER
        // TRIGGER:-P15M (15 minutes prior to the start of the event)
        // TRIGGER;RELATED=END:P5M (5 minutes after the end of the event)
        // TRIGGER;VALUE=DATE-TIME:19980101T050000Z (absolute time)
        'trigger'=>array('type'=>'text','null'=>TRUE),
        
        // ACTION::This property defines the action to be invoked when an alarm is triggered.
        // 0 = CAL_ALARM_ACTION_AUDIO
        // 1 = CAL_ALARM_ACTION_DISPLAY
        // 2 = CAL_ALARM_ACTION_EMAIL (default)
        // 3 = CAL_ALARM_ACTION_PROCEDURE
        'action'=>array('type'=>'integer','null'=>FALSE,'default'=>'2'),
        
        // REPEAT::This property defines the number of time the alarm should be 
        // repeated, after the initial trigger.
        'repeat'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        
        // DURATION::Corresponds to the repeat value to set the duration between repeats
        // example (repeat 4 times every 5 minutes after initial trigger)
        // REPEAT:4
        // DURATION:PT5M
        'duration'=>array('type'=>'text','null'=>TRUE)
    );
    $sql = xarDBCreateTable($alarms_table,$alarms_fields);
    if (empty($sql)) return; // throw back
    if (!$dbconn->Execute($sql)) return;*/
    
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

         // Create separate table to store event information (date, time, etc.) for external (hooked) items.
    $event_linkage_table = $xartable['julian_events_linkage'];
    $event_linkage_fields = array(
        // UID::the linked-event id, auto-increment
        'event_id'=>array('type'=>'integer','size'=>'medium','unsigned'=>TRUE,'null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        
                  // Hooked item details:
                  // - ID of external module
                  // - type of external item (one module can harbour different item types)
                  // - ID of external item
                  'hook_modid'   =>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
                  'hook_itemtype'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
                  'hook_iid'     =>array('type'=>'integer','null'=>FALSE,'default'=>'0'),

        // DTSTART::event start date/time
        'dtstart'=>array('type'=>'datetime','size'=>'','null'=>FALSE,'default'=>''),

        // DURATION::how long the event lasts
        'duration'=>array('type'=>'varchar','size'=>'50','null'=>TRUE),
        
        // ISALLDAY::boolean flag indicating if event is all day
        'isallday'=>array('type'=>'integer','size'=>'tiny','default'=>'0'),

        // RRULE::event recurrence rule
        // 0 = NO REPEATING
        // 1 = CAL_RECUR_FREQ_DAILY
        // 2 = CAL_RECUR_FREQ_WEEKLY
        // 3 = CAL_RECUR_FREQ_MONTHLY
        // 4 = CAL_RECUR_FREQ_YEARLY
        'rrule'=>array('type'=>'text','null'=>TRUE),
        
        // RECUR_FREQ::how often to repeat rule
        'recur_freq'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
        
        // COUNT::Recurrence Count
        // Can not exist if UNTIL is not null
        // 0 = NO REPEATING
        // 1 = SUNDAY
        // ...
        // 7 = SATURDAY
        'recur_count'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
        
        // INTERVAL::Recurrence Interval
        // 0 = NO REPEATING
        // 1 = FIRST
        // 2 = SECOND
        // 3 = THIRD
        // 4 = FOURTH
        // 5 = LAST
        'recur_interval'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
        
        // UNTIL::Recurrence End Date (YYYYMMDDHHMMSS)
        // This should always be stored as UTC
        // Can not exist if COUNT is not null
        'recur_until'=>array('type'=>'datetime','size'=>'','null'=>FALSE),// Bug 4942 removed ,'default'=>''
    );
    $sql = xarDBCreateTable($event_linkage_table,$event_linkage_fields);
    if (empty($sql)) return; // throw back
    if (!$dbconn->Execute($sql)) return;

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


//TODO::Figure out all the permissions stuff
// Should be based in event id , catid, and class? For cat_id we will probably need a wizard.

    // allow users to see the calendar w/ events
    xarRegisterMask('Viewjulian','All','julian','All','All','ACCESS_READ');
    // allows users to add events, but not categories
    xarRegisterMask('Editjulian','All','julian','All','All','ACCESS_EDIT');
    // allow full admin of the calendar
    xarRegisterMask('Adminjulian','All','julian','All','All','ACCESS_ADMIN');

     // Register hooks: julian can couple a date+time to any item from any module, provided
     // the module in question calls (Julian's) hooks when editing items.
    if (!xarModRegisterHook('item', 'new',    'GUI', 'julian', 'user', 'newhook'))     return false;
    if (!xarModRegisterHook('item', 'create', 'API', 'julian', 'user', 'createhook'))  return false;
    if (!xarModRegisterHook('item', 'modify', 'GUI', 'julian', 'user', 'modifyhook'))  return false;
    if (!xarModRegisterHook('item', 'update', 'API', 'julian', 'user', 'updatehook'))  return false;
    if (!xarModRegisterHook('item', 'delete', 'API', 'julian', 'user', 'deletehook'))  return false;
    if (!xarModRegisterHook('item', 'display','GUI', 'julian', 'user', 'displayhook')) return false;
         
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

            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
                        // Using the Datadict method to be up to date ;)
                        $datadict =& xarDBNewDataDict($dbconn, 'CREATE');

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
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
                        // Using the Datadict method to be up to date ;)
                        $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
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
                        $dbconn =& xarDBGetConn();
                        $xartable =& xarDBGetTables();
                        // Using the Datadict method to be up to date ;)
                        $datadict =& xarDBNewDataDict($dbconn, 'CREATE');
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
            $query = "SELECT `cat_id`,`cat_name`,`color` FROM " . $categories_table;
            $result =& $dbconn->Execute($query);
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
                $query = "INSERT INTO $category_properties_table (`cid`,`color`) VALUES ($newcid,'$color')";
                $result_catprop =& $dbconn->Execute($query);
                if (!$result_catprop) {
                    return;
                }

                // Get ids of the events that belong to this category.
                $query_events = "SELECT `event_id` FROM $events_table WHERE `categories`='$oldcid'";
                $result_events =& $dbconn->Execute($query_events);
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
                          
                          // Hooked item details:
                          // - ID of external module
                          // - type of external item (one module can harbour different item types)
                          // - ID of external item
                          'hook_modid'   =>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
                          'hook_itemtype'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
                          'hook_iid'     =>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
                          
                          // DTSTART::event start date/time
                          'dtstart'=>array('type'=>'datetime','size'=>'','null'=>FALSE,'default'=>''),
        
                          // DURATION::how long the event lasts
                          'duration'=>array('type'=>'varchar','size'=>'50','null'=>TRUE),
                          
                          // ISALLDAY::boolean flag indicating if event is all day
                          'isallday'=>array('type'=>'integer','size'=>'tiny','default'=>'0'),
        
                          // RRULE::event recurrence rule
                          // 0 = NO REPEATING
                          // 1 = CAL_RECUR_FREQ_DAILY
                          // 2 = CAL_RECUR_FREQ_WEEKLY
                          // 3 = CAL_RECUR_FREQ_MONTHLY
                          // 4 = CAL_RECUR_FREQ_YEARLY
                          'rrule'=>array('type'=>'text','null'=>TRUE),
                          
                          // RECUR_FREQ::how often to repeat rule
                          'recur_freq'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
                          
                          // COUNT::Recurrence Count
                          // Can not exist if UNTIL is not null
                          // 0 = NO REPEATING
                          // 1 = SUNDAY
                          // ...
                          // 7 = SATURDAY
                          'recur_count'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
                          
                          // INTERVAL::Recurrence Interval
                          // 0 = NO REPEATING
                          // 1 = FIRST
                          // 2 = SECOND
                          // 3 = THIRD
                          // 4 = FOURTH
                          // 5 = LAST
                          'recur_interval'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
                          
                          // UNTIL::Recurrence End Date (YYYYMMDDHHMMSS)
                          // This should always be stored as UTC
                          // Can not exist if COUNT is not null
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
            // Nothing yet
            // Remove masks and make new ones...?
            break;
        case '1.0.0':
            // Code to upgrade from version 1.0 goes here
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
    xarDBLoadTableMaintenanceAPI();
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $query = xarDBDropTable($xartable['julian_events']);
    if(empty($query)) return; // throw back
    if(!$dbconn->Execute($query)) return;
    
    $query = xarDBDropTable($xartable['julian_events_linkage']);
    if(empty($query)) return; // throw back
    if(!$dbconn->Execute($query)) return;

    $query = xarDBDropTable($xartable['julian_category_properties']);
    if(empty($query)) return; //throw back
    if(!$dbconn->Execute($query)) return;


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
