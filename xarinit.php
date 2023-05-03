<?php 
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function calendar_init()
{
# --------------------------------------------------------
#
# Set up tables
#
    sys::import('xaraya.structures.query');
    $q = new Query();
    $prefix = xarDB::getPrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_calendar_calendar";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_calendar_calendar (
      id          integer unsigned NOT NULL auto_increment,
      name        varchar(60) default '' NOT NULL,
      description text,
      module_id   integer unsigned default null,
      itemtype    integer unsigned default null,
      item_id     integer unsigned default null,
    PRIMARY KEY  (id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_calendar_event";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_calendar_event (
      id                   integer unsigned NOT NULL auto_increment,
      name                 varchar(64) NULL,
      description          text,
      start_time           integer NULL,
      duration             integer NULL,
      end_time             integer NULL,
      recurring_code       integer unsigned NULL,
      recurring_span       integer unsigned NULL,
      start_location       varchar(254) NULL,
      end_location         varchar(254) NULL,
      object_id            integer unsigned NULL,
      module_id            integer unsigned NULL,
      itemtype             integer unsigned NULL,
      item_id              integer unsigned NULL,
      role_id              integer unsigned NULL,
      return_link          varchar(254) NULL,
      state                tinyint unsigned default 0 NOT NULL,
      timestamp            integer default 0 NOT NULL,
      PRIMARY KEY (id),
      KEY i_start (start_time),
      KEY i_end   (end_time)
    )";
    if (!$q->run($query)) return;

/*    $query = "DROP TABLE IF EXISTS " . $prefix . "_bookings_repeat";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_bookings_repeat (
      id          integer unsigned NOT NULL auto_increment,
      start_time  int DEFAULT '0' NOT NULL,
      end_time    int DEFAULT '0' NOT NULL,
      rep_type    int DEFAULT '0' NOT NULL,
      end_date    int DEFAULT '0' NOT NULL,
      rep_opt     varchar(32) DEFAULT '' NOT NULL,
      objectid     int DEFAULT '1' NOT NULL,
      timestamp integer default 0 NOT NULL,
      owner integer default 0 NOT NULL,
      name        varchar(80) DEFAULT '' NOT NULL,
      status integer default 0 NOT NULL,
      description text,
      rep_num_weeks smallint NULL,

      PRIMARY KEY (id)
    )";
    if (!$q->run($query)) return;
*/

# --------------------------------------------------------
#
# Set up masks
#
    xarMasks::register('ViewCalendar','All','calendar','All','All','ACCESS_OVERVIEW');
    xarMasks::register('ReadCalendar','All','calendar','All','All','ACCESS_READ');
    xarMasks::register('CommentCalendar','All','calendar','All','All','ACCESS_COMMENT');
    xarMasks::register('ModerateCalendar','All','calendar','All','All','ACCESS_MODERATE');
    xarMasks::register('EditCalendar','All','calendar','All','All','ACCESS_EDIT');
    xarMasks::register('AddCalendar','All','calendar','All','All','ACCESS_ADD');
    xarMasks::register('ManageCalendar','All','calendar','All','All','ACCESS_DELETE');
    xarMasks::register('AdminCalendar','All','calendar','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarPrivileges::register('ViewCalendar','All','calendar','All','All','ACCESS_OVERVIEW');
    xarPrivileges::register('ReadCalendar','All','calendar','All','All','ACCESS_READ');
    xarPrivileges::register('CommentCalendar','All','calendar','All','All','ACCESS_COMMENT');
    xarPrivileges::register('ModerateCalendar','All','calendar','All','All','ACCESS_MODERATE');
    xarPrivileges::register('EditCalendar','All','calendar','All','All','ACCESS_EDIT');
    xarPrivileges::register('AddCalendar','All','calendar','All','All','ACCESS_ADD');
    xarPrivileges::register('ManageCalendar','All','calendar','All','All','ACCESS_DELETE');
    xarPrivileges::register('AdminCalendar','All','calendar','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up modvars
#

    // Location of the PEAR Calendar Classes
    // Use the PHP Include path for now
    xarModVars::set('calendar','pearcalendar_root',sys::code() . 'modules/calendar/pear/Calendar/');

    // get list of calendar ics files
    $data = xarMod::apiFunc('calendar', 'admin', 'get_calendars');
    xarModVars::set('calendar','default_cal',serialize($data['icsfiles']));

    // Other variables from phpIcalendar config.inc.php
    xarModVars::set('calendar','minical_view'           , 'week');
//    xarModVars::set('calendar','cal_sdow'               , 0);   // 0=sunday $week_start_day in phpIcalendar
//    xarModVars::set('calendar','day_start'              , '0700');
//    xarModVars::set('calendar','day_end'                , '2300');
//    xarModVars::set('calendar','gridLength'             , 15);
    xarModVars::set('calendar','num_years'              , 1);
    xarModVars::set('calendar','month_event_lines'      , 1);
    xarModVars::set('calendar','tomorrows_events_lines' , 1);
    xarModVars::set('calendar','allday_week_lines'      , 1);
    xarModVars::set('calendar','week_events_lines'      , 1);
    xarModVars::set('calendar','second_offset'          , 0);
    xarModVars::set('calendar','bleed_time'             , 0);
    xarModVars::set('calendar','display_custom_goto'    , 0);
    xarModVars::set('calendar','display_ical_list'      , 1);
    xarModVars::set('calendar','allow_webcals'          , 0);
    xarModVars::set('calendar','this_months_events'     , 1);
    xarModVars::set('calendar','use_color_cals'         , 1);
    xarModVars::set('calendar','daysofweek_dayview'     , 0);
    xarModVars::set('calendar','enable_rss'             , 1);
    xarModVars::set('calendar','show_search'            , 1);
    xarModVars::set('calendar','allow_preferences'      , 1);
    xarModVars::set('calendar','printview_default'      , 0);
    xarModVars::set('calendar','show_todos'             , 1);
    xarModVars::set('calendar','show_completed'         , 0);
    xarModVars::set('calendar','allow_login'            , 0);

    // Regulate display in day view
    xarModVars::set('calendar','windowwidth', 902);
    xarModVars::set('calendar','minutesperunit', 15);
    xarModVars::set('calendar','unitheight', 12);

    xarModVars::set('calendar','event_duration', 60*60);
    xarModVars::set('calendar','cal_sdow', 0);
    xarModVars::set('calendar','day_start', 25200);
    xarModVars::set('calendar','day_end', 82800);

//TODO::Register the Module Variables
    //
    //xarModVars::set('calendar','allowUserCalendars',false);
    //xarModVars::set('calendar','eventsOpenNewWindow',false);
    //xarModVars::set('calendar','adminNotify',false);
    //xarModVars::set('calendar','adminEmail','none@none.org');

# --------------------------------------------------------
#  Register block types
#
    xarMod::apiFunc('blocks', 'admin','register_block_type', array('modName' => 'calendar','blockType' => 'calnav'));
    xarMod::apiFunc('blocks', 'admin','register_block_type', array('modName' => 'calendar','blockType' => 'month'));

//TODO::Register our blocklayout tags to allow using Objects in the templates
//<xar:calendar-decorator object="$Month" decorator="Xaraya" name="$MonthURI"/>
//<xar:calendar-build object="$Month"/>
//<xar:set name="Month">& $Year->fetch()</xar:set>

    xarModVars::set('calendar', 'SupportShortURLs', true);

/*    xarTplRegisterTag(
        'calendar', 'calendar-decorator', array(),
        'calendar_userapi_handledecoratortag'
    );
    */

# --------------------------------------------------------
#
# Set up hooks
#

    xarModHooks::register('item', 'create', 'API','calendar', 'admin', 'hookcreate');
    xarModHooks::register('item', 'update', 'API','calendar', 'admin', 'hookupdate');
//    xarModHooks::register('item', 'delete', 'API','calendar', 'admin', 'hookdelete');

# --------------------------------------------------------
#
# Create DD objects
#
    $module = 'calendar';
    $objects = array(
                   'calendar_calendar',
                   'calendar_event',
                     );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

    return true;
}

/**
 *  Module Upgrade Function
 */
function calendar_upgrade($oldversion)
{

    switch ($oldversion) {
        case '0.1.0':
            // Start creating the tables

            $dbconn = xarDB::getConn();
            $xartable =& xarDB::getTables();
            $calfilestable = $xartable['calendars_files'];
            sys::import('xaraya.tableddl');
            $fields = array(
                'xar_calendars_id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'primary_key' => true),
                'xar_files_id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'primary_key' => true)
                );
            $query = xarTableDDL::createTable($calfilestable, $fields);
            if (empty($query)) return;
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            $filestable = $xartable['calfiles'];
            sys::import('xaraya.tableddl');
            $fields = array(
                'xar_id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
                'xar_path' => array('type' => 'varchar', 'size' => '255', 'null' => true)
                );
            $query = xarTableDDL::createTable($filestable, $fields);
            if (empty($query)) return;
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            $index = array(
                'name'      => 'i_' . xarDB::getPrefix() . '_calendars_files_calendars_id',
                'fields'    => array('xar_calendars_id'),
                'unique'    => false
            );
            $query = xarTableDDL::createIndex($calfilestable,$index);
            $result = $dbconn->Execute($query);
            if (!$result) return;

            $index = array(
                'name'      => 'i_' . xarDB::getPrefix() . '_calendars_files_files_id',
                'fields'    => array('xar_files_id'),
                'unique'    => false
            );
            $query = xarTableDDL::createIndex($calfilestable,$index);
            $result = $dbconn->Execute($query);
            if (!$result) return;

            return calendar_upgrade('0.1.1');
    }
    return true;
}

/**
 *  Module Delete Function
 */
function calendar_delete()
{

    # --------------------------------------------------------
    #
    # Remove block types
    #
        if (!xarMod::apiFunc('blocks', 'admin', 'unregister_block_type', array('modName'  => 'calendar', 'blockType'=> 'month'))) return;

    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => 'calendar'));
}

?>
