<?php

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

    $bl_data = array();
    
    // Date/time defaults
    $event_startdate = time();
    $event_enddate = time();
    
    // All day default (as opposed to timed)
    $bl_data['event_allday'] = true;
    
    // Duration defaults
    $bl_data['event_dur_hours'] = 1;
    $bl_data['event_dur_minutes'] = 0;
    
   // Type of recurrence (0=none, 1=every, 2=on)
    $bl_data['event_repeat'] = 0;
    
    // Repeat-every defaults.
    $bl_data['event_repeat_every_type'] = 0;    // frequency unit (day=1, week=2, month=3, year=4)
    $bl_data['event_repeat_every_freq'] = '';    // frequency (every x time units)

    // Repeat-on defaults
    $bl_data['event_repeat_on_day'] = 0;    // day of the week
    $bl_data['event_repeat_on_num'] = 0;    // instance within month (1st, 2nd, ..., last=5)
    $bl_data['event_repeat_on_freq'] = '';    // frequency (every x months)

   // Load up database
   $dbconn = xarDBGetConn();
   $xartable = xarDBGetTables();
   $event_linkage_table = $xartable['julian_events_linkage'];

    // Try to find the link for the current module, item type and item id.
   $query = "SELECT * FROM " .  $event_linkage_table . " WHERE (`hook_modid`=$modid AND `hook_itemtype`=$itemtype AND `hook_iid`=$objectid)";
   $result = $dbconn->Execute($query);
   if (!empty($result)) {
        if (!$result->EOF) {
            $edit_obj = $result->FetchObject(false);
            
            // Start/end date (and time)
            $event_startdate = strtotime($edit_obj->dtstart);
            $event_enddate   = strtotime($edit_obj->recur_until);
            
            // All day or not
            $bl_data['event_allday'] = ($edit_obj->isallday==1);

            // Event duration
            if (strcmp($edit_obj->duration,'')!=0) {
                list($bl_data['event_dur_hours'], $bl_data['event_dur_minutes']) = explode(':',$edit_obj->duration);
            }

            //Checking to see which repeating rule was used so the event_repeat can be set.
            if ($edit_obj->rrule==3 && $edit_obj->recur_count && $edit_obj->recur_interval && $edit_obj->recur_freq)
                $bl_data['event_repeat'] = 2;
            else if ($edit_obj->rrule && $edit_obj->recur_freq) 
               $bl_data['event_repeat'] = 1;

            //Depending on which recurrence rule was used, set the appropriate form fields.
            switch ($bl_data['event_repeat']) {
                case 1:
                    $bl_data['event_repeat_every_freq'] = $edit_obj->recur_freq;    // time unit (1=day, 2=week, 3=month, 4=year)
                    $bl_data['event_repeat_every_type']  = $edit_obj->rrule;            // every n time units
                    break;
                case 2:
                    $bl_data['event_repeat_on_day'] = $edit_obj->recur_count;        // day of the week (mon-sun)
                    $bl_data['event_repeat_on_num'] = $edit_obj->recur_interval;    // instance within month (1=1st, 2=2nd, ..., 5=last)
                    $bl_data['event_repeat_on_freq'] = $edit_obj->recur_freq;        // every n months
                    break;
            }
            
            $result->Close();
        }
        else {
            // ERROR: no link to this object was found!!!
        }
    }
    else {
        // ERROR: no link to this object was found!!!
    }

    // start date
   list($bl_data['event_year'],   $bl_data['event_month'],   $bl_data['event_day'])    = explode("-",date("Y-m-d",$event_startdate));
   list($bl_data['event_endyear'],$bl_data['event_endmonth'],$bl_data['event_endday']) = explode("-",date("Y-m-d",$event_enddate));

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
   $bl_data['start_hour_options'] = $start_hour_options;
   
   //Building start minute options
   $start_minute_options = '';
   for($i = 0;$i < 46; $i = $i + 15)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $start_minute_options.='<option value="'.$i.'"';
     if ($i == $minute) $start_minute_options.= " selected";
     $start_minute_options.='>'.$j.'</option>';
   }
   $bl_data['start_minute_options'] = $start_minute_options;

    // start AM/PM
    $bl_data['event_startampm'] = (strcmp(date("a",$event_startdate),"am")!=0);    // true=PM, false=AM
   
   //Building duration hour options
   $dur_hour_options = '';
   for($i = 0;$i <= 24; $i++)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $dur_hour_options.='<option value="'.$i.'"';
     if ($i == $bl_data['event_dur_hours']) $dur_hour_options.= " selected";
     $dur_hour_options.='>'.$j.'</option>';
   }
   $bl_data['dur_hour_options'] = $dur_hour_options;
   
   //Building duration minute options
   $dur_minute_options = '';
   for($i = 0;$i < 46; $i = $i + 15)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $dur_minute_options.='<option value="'.$j.'"';
     if ($i == $bl_data['event_dur_minutes']) $dur_minute_options.= " selected";
     $dur_minute_options.='>'.$j.'</option>';
   }
   $bl_data['dur_minute_options'] = $dur_minute_options;
    
    return xarTplModule('julian','user','edithook',$bl_data);
}

?>
