<?php
/**
 * Hook for create Julian
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * process date/time for the new item - hook for ('item','create','API')
 * @author JornB
 * @param array extrainfo with module name
                               itemtype
                               itemid
 * @param int objectid
 */
function julian_userapi_createhook($args)
{
    extract($args);

     // extra info as supplied by the hooking module.
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

     // Get the id of the newly created object (the id as used in the hooking module).
    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object ID', 'user', 'createhook', 'julian');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // When called via hooks, the module name may be empty, so we get it from the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

     // Convert module name into module id.
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'user', 'createhook', 'julian');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Get item type.
     if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

   // Load up database, get event linkage table.
   $dbconn =& xarDBGetConn();
   $xartable =& xarDBGetTables();
   $event_linkage_table = $xartable['julian_events_linkage'];

    // Try to find the link for the current module, item type and item id.
   $query = "SELECT event_id  FROM $event_linkage_table WHERE ( hook_modid =$modid AND  hook_itemtype =$itemtype AND  hook_iid =$objectid)";
   $result = $dbconn->Execute($query);
   $id = '';
   if (!empty($result)) {
        if (!$result->EOF) $id=$result->fields[0];
        $result->Close();
   }
   // Give the summary (aka title) of event
   if (!xarVarFetch('event_summary', 'str:1:', $event_summary, xarML('Not Entered'), XARVAR_NOT_REQUIRED)) return;
    // start date
   if (!xarVarFetch('event_month','int',$event_month,'')) return;
   if (!xarVarFetch('event_day',  'int',$event_day,  '')) return;
   if (!xarVarFetch('event_year', 'int',$event_year, '')) return;

    // all day event (otherwise timed)
   if (!xarVarFetch('event_allday','str',$event_allday,0)) return;

    // start time
   if (!xarVarFetch('event_starttimeh','int',$event_starttimeh,0)) return;
   if (!xarVarFetch('event_starttimem','int',$event_starttimem,0)) return;
   if (!xarVarFetch('event_startampm', 'str',$event_startampm,0)) return;    // 1=AM, 2=PM

    // duration
   if (!xarVarFetch('event_dur_hours',  'int',$event_dur_hours,  0)) return;
   if (!xarVarFetch('event_dur_minutes','int',$event_dur_minutes,0)) return;

    // recurrence type: 0=once, 1=every, 2=on
   if (!xarVarFetch('event_repeat','int', $event_repeat,0)) return;

    // recurrence 'every': frequency (number) and type (1=day, 2=week, 3=month, 4=year)
   if (!xarVarFetch('event_repeat_freq', 'int', $event_repeat_every_freq,0)) return;    // database field: recur_freq
   if (!xarVarFetch('event_repeat_freq_type', 'int', $event_repeat_freq_type,0)) return;    // database field: rrule

    // recurrence 'on': weekday, number in the month, interval (number of months)
   if (!xarVarFetch('event_repeat_on_day', 'int', $event_repeat_on_day,  0)) return; // database field: recur_count
   if (!xarVarFetch('event_repeat_on_num', 'int', $event_repeat_on_num,  0)) return; // database field: recur_interval
   if (!xarVarFetch('event_repeat_on_freq','int', $event_repeat_on_freq, 0)) return; // database field: recur_freq

    // end of recurrence type: 1=end date known, 0=open ended
   if (!xarVarFetch('event_endtype','int', $event_endtype,0)) return;

    // end date
   if (!xarVarFetch('event_endmonth','int', $event_endmonth,0)) return;
   if (!xarVarFetch('event_endday',  'int', $event_endday,  0)) return;
   if (!xarVarFetch('event_endyear', 'int', $event_endyear, 0)) return;

   $event_startdate = $event_year."-".$event_month."-".$event_day;

   // if this is a event recurring on specific days of the week, determine the start date based on the recur type and the
    // selected start date by the user. Otherwise, the start date is the one selected by the user.
   if($event_repeat==2) {
      //load the event class
      $e = xarModAPIFunc('julian','user','factory','event');
      //set the start date for this recurring event
      $event_startdate = $e->setRecurEventStartDate($event_startdate,$event_repeat_on_day,$event_repeat_on_num,$event_repeat_on_freq);
   }

   // End date (if available)
   $event_enddate = '';
   if ($event_repeat!=0 && $event_endtype!=0) $event_enddate = $event_endyear."-".$event_endmonth."-".$event_endday;

   // If not an all day event, eventstartdate gets a time and duration.
   $event_duration = '';
   if (!$event_allday) {
        // Add date to event start.
      $ampm = $event_startampm==1?"AM":"PM";    // AM=1, PM=2
      $event_startdate =  date("Y-m-d H:i:s",strtotime($event_startdate." ".$event_starttimeh.":".$event_starttimem.":00 ".$ampm));

        // Create duration string (hh:mm)
        if (strcmp($event_dur_hours.':'.$event_dur_minutes,'0:00')!=0) {
            $event_duration = $event_dur_hours.':'.$event_dur_minutes;
        }
   }

   /*
    * Checking which event_repeat rule is being used and setting the recur_freq to the right reoccuring frequency
    * Using this because the frequence value is being written to the same place in the database, for both 'every'
    * and 'on' recurrence.
    */
    $recur_freq = 0;
    switch($event_repeat) {
        case 0:
            // no recurrence
            break;
        case 1:
            // repeating every x time units
            $recur_freq = $event_repeat_every_freq;
            $event_repeat_on_day = 0;
            $event_repeat_on_num = 0;
            break;
        case 2:
            // repeating every xth day every yth month
            $recur_freq = $event_repeat_on_freq;
            $event_repeat_freq_type = 3;
            break;
    }

   if(!empty($id)) {
      // Link already exists; update it.
      $query = "UPDATE " .  $event_linkage_table . "
                SET hook_modid=?,
                    hook_itemtype=?,
                    hook_iid=?,
                    summary =?,
                    dtstart=?,
                    duration=?,
                    isallday=?,
                    rrule=?,
                    recur_freq=?,
                    recur_count=?,
                    recur_interval=?,
                    recur_until=?
                WHERE event_id= ?";
                $bindvars = array ($modid,                      // hooking module id
                                   $itemtype,                   // hooking module item type
                                   $objectid,                   // hooking module item id
                                   $event_summary,              // The title of the hooked event
                                   $event_startdate,            // event start date/time
                                   $event_duration,             // event duration (hh:mm)
                                   $event_allday,               // event takes all day (0 = false, 1 = true)
                                   $event_repeat_freq_type,     // unit of repetition frequency (day, week, month, year)
                                   $recur_freq,                 // repetition frequency
                                   $event_repeat_on_day,        // day of the week
                                   $event_repeat_on_num,        // month-based instance of weekday (1st, 2nd, ..., last=5)
                                   $event_enddate,              // event end date (may be '')
                                   $id);                        // Event ID
             $result = $dbconn->Execute($query, $bindvars);
   } else {
        // Link does not yet exist; create it.
      $query = "INSERT INTO " .  $event_linkage_table . "
                ( hook_modid,
                  hook_itemtype,
                  hook_iid,
                  summary,
                  dtstart,
                  duration,
                  isallday,
                  rrule,
                  recur_freq,
                  recur_count,
                  recur_interval,
                  recur_until
                ) VALUES (
                  ?,
                  ?,
                  ?,
                  ?,
                  ?,
                  ?,
                  ?,
                  ?,
                  ?,
                  ?,
                  ?,
                  ?)";
                $bindvars = array ($modid,                      // hooking module id
                                   $itemtype,                   // hooking module item type
                                   $objectid,                   // hooking module item id
                                   $summary,                    // The title of the hooked event
                                   $event_startdate,            // event start date/time
                                   $event_duration,             // event duration (hh:mm)
                                   $event_allday,               // event takes all day (0 = false, 1 = true)
                                   $event_repeat_freq_type,    // unit of repetition frequency (day, week, month, year)
                                   $recur_freq,                 // repetition frequency
                                   $event_repeat_on_day,        // day of the week
                                   $event_repeat_on_num,        // month-based instance of weekday (1st, 2nd, ..., last=5)
                                   $event_enddate);             // event end date (may be '')
                $result = $dbconn->Execute($query, $bindvars);
            $id = $dbconn->Insert_ID();
   }

    return $extrainfo;
}

?>
