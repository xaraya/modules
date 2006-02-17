<?php
/**
 * Hook to modify a hooked event
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */

/**
 * enter date/time for an item that is modified - hook for ('item','modify','GUI')
 */
function julian_user_modifyhook($args)
{
    extract($args);

     // extra info as supplied by the hooking module.
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

     // Get the id of the object to display (the id as used in the hooking module).
    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object ID', 'user', 'modifyhook', 'julian');
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'user', 'modifyhook', 'julian');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Get item type.
     if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    $data = array();

    $data['summary'] = xarML('Not Entered');

    // Date/time defaults
    $event_startdate = time();
    $event_enddate = time();

    // All day default (as opposed to timed)
    $data['event_allday'] = true;

    // Duration defaults
    $data['event_dur_hours'] = 1;
    $data['event_dur_minutes'] = 0;

   // Type of recurrence (0=none, 1=every, 2=on)
    $data['event_repeat'] = 0;

    // Repeat-every defaults.
    $data['event_repeat_every_type'] = 0;    // frequency unit (day=1, week=2, month=3, year=4)
    $data['event_repeat_every_freq'] = '';    // frequency (every x time units)

    // Repeat-on defaults
    $data['event_repeat_on_day'] = 0;    // day of the week
    $data['event_repeat_on_num'] = 0;    // instance within month (1st, 2nd, ..., last=5)
    $data['event_repeat_on_freq'] = '';  // frequency (every x months)

    // start date and time
   $data['event_year'] ='';
   $data['event_month'] ='';
   $data['event_day'] = '';
   $data['event_endyear'] ='';
   $data['event_endmonth'] ='';
   $data['event_endday'] = '';
   $data['start_hour_options'] = '';
   $data['start_minute_options'] = '';
   // Duration options
   $data['dur_hour_options'] = '';
   $data['dur_minute_options'] = '';

    // start time
   list($hour, $minute) = explode(":",date("h:i",$event_startdate));


   // Load up database
   $dbconn =& xarDBGetConn();
   $xartable =& xarDBGetTables();
   $event_linkage_table = $xartable['julian_events_linkage'];

    // Try to find the link for the current module, item type and item id.
   $query = "SELECT * FROM " .  $event_linkage_table . " WHERE ( hook_modid =$modid AND  hook_itemtype =$itemtype AND  hook_iid =$objectid)";
   $result = $dbconn->Execute($query);
   if (!empty($result)) {
        if (!$result->EOF) {
            $edit_obj = $result->FetchObject(false);
            // Summary aka Title
            $data['summary'] = $edit_obj->summary;
            // Start/end date (and time)
            $event_startdate = strtotime($edit_obj->dtstart);
            $event_enddate   = strtotime($edit_obj->recur_until);

            // All day or not
            $data['event_allday'] = ($edit_obj->isallday==1);

            // Event duration
            if (strcmp($edit_obj->duration,'')!=0) {
                list($data['event_dur_hours'], $data['event_dur_minutes']) = explode(':',$edit_obj->duration);
            }

            //Checking to see which repeating rule was used so the event_repeat can be set.
            if ($edit_obj->rrule==3 && $edit_obj->recur_count && $edit_obj->recur_interval && $edit_obj->recur_freq)
                $data['event_repeat'] = 2;
            else if ($edit_obj->rrule && $edit_obj->recur_freq)
               $data['event_repeat'] = 1;

            //Depending on which recurrence rule was used, set the appropriate form fields.
            switch ($data['event_repeat']) {
                case 1:
                    $data['event_repeat_every_freq'] = $edit_obj->recur_freq;    // time unit (1=day, 2=week, 3=month, 4=year)
                    $data['event_repeat_every_type']  = $edit_obj->rrule;            // every n time units
                    break;
                case 2:
                    $data['event_repeat_on_day'] = $edit_obj->recur_count;        // day of the week (mon-sun)
                    $data['event_repeat_on_num'] = $edit_obj->recur_interval;    // instance within month (1=1st, 2=2nd, ..., 5=last)
                    $data['event_repeat_on_freq'] = $edit_obj->recur_freq;        // every n months
                    break;
            }

            $result->Close();
        }
        else {
            return xarTplModule('julian','user','edithook',$data);
            // ERROR: no link to this object was found!!!
        }
    }
    else {
        return xarTplModule('julian','user','edithook',$data);
        // ERROR: no link to this object was found!!!
    }

    // start date
   list($data['event_year'],   $data['event_month'],   $data['event_day'])    = explode("-",date("Y-m-d",$event_startdate));
   list($data['event_endyear'],$data['event_endmonth'],$data['event_endday']) = explode("-",date("Y-m-d",$event_enddate));

    // start time
   list($hour, $minute) = explode(":",date("h:i",$event_startdate));

   //Building start hour options
   $start_hour_options = '';
   for($i = 1;$i <= 12; $i++)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $start_hour_options.='<option value="'.$i.'"';
     if ($i == $hour) $start_hour_options.= " SELECTED";
      $start_hour_options.='>'.$j.'</option>';
   }
   $data['start_hour_options'] = $start_hour_options;

    // Building duration minute options
    // Get the interval
    $StartMinInterval = xarModGetVar('julian', 'StartMinInterval');
    if ($StartMinInterval == 1) {
        $sminend = 60;
    } elseif ($StartMinInterval == 5) {
        $sminend = 56;
    } elseif ($StartMinInterval == 10) {
        $sminend = 51;
    } elseif ($StartMinInterval == 15) {
        $sminend = 46;
    }


    for($i = 0;$i < $sminend; $i = $i + $StartMinInterval) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $start_minute_options.='<option value="'.$j.'"';
        $start_minute_options.='>'.$j.'</option>';
    }
    $data['start_minute_options'] = $start_minute_options;

    // start AM/PM
    $data['event_startampm'] = (strcmp(date("a",$event_startdate),"am")!=0);    // true=PM, false=AM

   //Building duration hour options
   $dur_hour_options = '';
   for($i = 0;$i <= 24; $i++)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $dur_hour_options.='<option value="'.$i.'"';
     if ($i == $data['event_dur_hours']) $dur_hour_options.= " selected";
     $dur_hour_options.='>'.$j.'</option>';
   }
   $data['dur_hour_options'] = $dur_hour_options;

    // Building duration minute options
    // Get the interval
    $DurMinInterval = xarModGetVar('julian', 'DurMinInterval');
    if ($DurMinInterval == 1) {
        $minend = 60;
    } elseif ($DurMinInterval == 5) {
        $minend = 56;
    } elseif ($DurMinInterval == 10) {
        $minend = 51;
    } elseif ($DurMinInterval == 15) {
        $minend = 46;
    }

    $dur_minute_options = '';
    for($i = 0;$i < $minend; $i = $i + $DurMinInterval) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $dur_minute_options.='<option value="'.$j.'"';
        $dur_minute_options.='>'.$j.'</option>';
    }
    $data['dur_minute_options'] = $dur_minute_options;

    $data['summary'] = $summary;

    return xarTplModule('julian','user','edithook',$data);
}

?>
