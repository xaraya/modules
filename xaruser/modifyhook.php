<?php
/**
 * Hook to modify a hooked event
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * enter date/time for an item that is modified - hook for ('item','modify','GUI')
 *
 * @author JornB
 * @author MichelV <michelv@xaraya.com>
 * @since May 2005
 * @param array $args an array with arguments
 * @param arrya extrainfo
 * @param id objectid
 * @return array with template data
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

    $data['event_summary'] = xarML('Not Entered');

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


   $data['event_endyear'] ='';
   $data['event_endmonth'] ='';
   $data['event_endday'] = '';
   $data['start_hour_options'] = '';
   $data['start_minute_options'] = '';
   // Duration options
   $data['dur_hour_options'] = '';
   $data['dur_minute_options'] = '';

    // start time
 //  list($hour, $minute) = explode(":",date("h:i",$event_startdate));

    $item = xarModAPIFunc('julian', 'user', 'gethooked', array('modid' => $modid, 'itemtype' => $itemtype, 'objectid' => $objectid));
    if(empty($item)) {
        return "error";
    }

    // start date
   list($data['event_year'],  $data['event_month'],   $data['event_day'])    = explode("-",date("Y-m-d",$item['ts_start']));
   list($event_endyear,$event_endmonth,$event_endday) = explode("-",date("Y-m-d",$item['ts_end']));
    //Date time from item
    //setting start date time variables
    // $item['event_starttime'] = date("g:i A",$event_startdate);
    $hour = date("h",strtotime($item['event_starttime'])); //12 hour format
    $ampm = !strcmp(date("a",strtotime($item['event_starttime'])),"am")?0:1;
    $hour24 = date("H",strtotime($item['event_starttime'])); //24 hour format
    $minute = date("i",strtotime($item['event_starttime']));

    // If there is not a duration, set dur_hours and dur_minutes.
    // list($item['event_dur_hours'], $item['event_dur_minutes']) = explode(':',$edit_obj->duration);
    // Default for both is empty string.
    $dur_hours = '';
    $dur_minutes = '';
    if(!empty($item['event_dur_hours'])){
        $dur_hours = $item['event_dur_hours'];
        $dur_minutes = $item['event_dur_minutes'];
    }
    //Checking to see which repeating rule was used so the event_repeat can be set.
    $event_repeat = $item['event_repeat'];

    //Depending on which rule was used, set the appropriate frequency field to the db value.
    $data['event_repeat_on_freq'] = '';
    $data['event_repeat_freq'] = '';
    if ($event_repeat == 1) {// event repeats every
      $data['event_repeat_freq'] = $item['recur_freq'];
    } else if ($event_repeat == 2) {// event repeats on
      $data['event_repeat_on_freq'] = $item['recur_freq'];
    }
    $data['event_summary'] = $item['event_summary'];
  //  $data['event_month'] =  $data['todays_month'];
  //  $data['event_day'] = $data['todays_day'];
  //  $data['event_year'] = $data['todays_year'];
    $data['event_allday'] = $item['event_allday'];
    $data['event_starttimeh'] = $hour;
    $data['event_starttimem'] = $minute;
    $data['event_startampm'] = $ampm;
    $data['event_dur_hours'] = $dur_hours;
    $data['event_dur_minutes'] = $dur_minutes;

    $data['event_repeat'] = $item['event_repeat'];
    $data['event_repeat_freq_type'] = $item['event_repeat_every_type'];
    $data['event_endmonth'] = $event_endmonth;
    $data['event_endday'] = $event_endday;
    $data['event_endyear'] = $event_endyear;
    $data['event_repeat_on_day'] = $item['event_repeat_on_day'];
    $data['event_repeat_on_num'] = $item['event_repeat_on_num'];

    // Determining which end date radio to check. 0 index indicates this event has an end date and 1 index means it does not
    // event_repeat tells the type of repeat
    $event_endtype_checked[0] = '';
    $event_endtype_checked[1] = 'checked';
    if (($event_endyear > 0) && ($item['event_repeat'] > 0)) {
        $event_endtype_checked[0] = 'checked';
        $event_endtype_checked[1] = '';
    }
    $data['event_endtype_checked'] = $event_endtype_checked;

    //determine if this is there is an enddate present
    $data['enddatedisabled'] = 'disabled';
    if ($item['event_repeat'] > 0) {
        $data['enddatedisabled'] = '';
    }

  //  $data['cal_date'] = $cal_date;

    //Building start hour options
    $start_hour_options = '';
    for($i = 1;$i <= 12; $i++) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $start_hour_options.='<option value="'.$i.'"';
        if ($i == $hour)
            $start_hour_options.= " SELECTED";
        $start_hour_options.='>'.$j.'</option>';
    }
    $start_hour24_options = '';
    for($i = 1;$i <= 24; $i++) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $start_hour24_options.='<option value="'.$i.'"';
        if ($i == $hour24)
            $start_hour24_options.= " SELECTED";
        $start_hour24_options.='>'.$j.'</option>';
    }
    $data['start_hour_options'] = $start_hour_options;
    $data['start_hour24_options'] = $start_hour24_options;

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

   //Building start minute options
   $start_minute_options = '';
   for($i = 0;$i < $sminend; $i = $i + $StartMinInterval) {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $start_minute_options.='<option value="'.$i.'"';
     if ($i == $minute) {
       $start_minute_options.= " selected";
    }
     $start_minute_options.='>'.$j.'</option>';
   }
   $data['start_minute_options'] = $start_minute_options;

   //Building duration hour options
   $dur_hour_options = '';
   for($i = 0;$i <= 24; $i++)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $dur_hour_options.='<option value="'.$i.'"';
     if ($i == $dur_hours)
        $dur_hour_options.= " selected";
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
    //for($i = 0;$i < 46; $i = $i + 15)
    for($i = 0;$i < $minend; $i = $i + $DurMinInterval) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $dur_minute_options.='<option value="'.$j.'"';
        if ($i == $dur_minutes) {
        $dur_minute_options.= " selected";
        }
     $dur_minute_options.='>'.$j.'</option>';
    }
    $data['dur_minute_options'] = $dur_minute_options;

   //Setting event repeat selection
   for ($i = 0; $i < 3; $i++) {
     $data['event_repeat_checked'][$i] = '';
   }
   $data['event_repeat_checked'][$event_repeat] = "checked";

   //Setting freq type selection (days,weeks,months,years)
   for ($i = 1; $i < 5; $i++) {
     $data['freq_type_selected'][$i] = '';
     if ($item['event_repeat_every_type'] == $i) {
         $data['freq_type_selected'][$i] = 'selected';
     }
   }

   //Show rrule only if the first repeating option was selected (2nd radio button) - every
   if ($event_repeat == 1) {
     $data['freq_type_selected']['rrule'] = 'selected';
   }

   //Setting repeat on num selection
   for ($i = 1; $i < 6; $i++) {
     $data['repeat_on_num_selected'][$i] = '';
     if ($item['event_repeat_on_num'] == $i) {
         $data['repeat_on_num_selected'][$i] = 'selected';
     }
   }

   //Setting repeat on day selection
   for ($i = 1; $i < 8; $i++) {
     $data['repeat_on_day_selection'][$i] = '';
     if ($item['event_repeat_on_day'] == $i) {
         $data['repeat_on_day_selection'][$i] = 'selected';
     }
   }

   //Setting allday checked
   $data['allday_checked'][0] = '';
   $data['allday_checked'][1] = 'checked';
   $data['timeddisabled'] = '';
   $item['isallday']=0;

   if ($item['event_allday'] == 1) {
     $data['allday_checked'][0] = 'checked';
     $data['allday_checked'][1] = '';
     $data['timeddisabled'] = 'disabled';
     $item['isallday']=1;
   }

   $data['item']=$item;
    return xarTplModule('julian','user','edithook',$data);
}

?>
