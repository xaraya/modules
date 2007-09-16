<?php
/**
 * Generates a form for editing an existing event.
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * @subpackage Julian module
 * @author Julian development Team
 * initial template: Roger Raymond
 */
/**
 * Edit a single event
 *
 * Get a single event from the events table
 * Then offer a form that will allow the user to edit the event
 *
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @author  Jodie Razdrh/John Kevlin/David St.Clair
 * @author  MichelV <Michelv@xaraya.com>
 * @access  public
 * @param   int id ID of the event to get or
 * @param   int objectid ID of standard object to get OPTIONAL
 * @return  array $item
 * @throws  BAD_PARAM list of exception identifiers which can be thrown
 * @todo    Michel V. <1> Clean up
 */
function julian_user_edit()
{
    if (!xarVarFetch('event_id', 'id',   $event_id)) return;
    if (!xarVarFetch('objectid', 'id',   $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    // This is the var to set the first day of the week
    if (!xarVarFetch('cal_date','int::', $cal_date, 0, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $event_id = $objectid;
    }
    // Get event the decent way
    $item = xarModAPIFunc('julian', 'user', 'get', array('event_id' => $event_id));
    // Security check
    if (!xarSecurityCheck('EditJulian', 1, 'Item', "$event_id:$item[organizer]:$item[calendar_id]:All")) {
        return;
    }

    $event_endyear='';
    $event_endmonth='';
    $event_endday='';
    if($item['eRecur']['timestamp']) {
       // End date and time
       // determine the end date for a recurring event
       // TODO: With the new get.php this should be rewritten
       list($event_endyear,$event_endmonth,$event_endday) = explode("-",$item['eRecur']['timestamp']);
    }
    $data['Bullet'] = '&'.xarModGetVar('julian', 'BulletForm').';';

    // Date time from item
    // setting start date time variables
    $hour = date("h", $item['dtstart']['unixtime']); //12 hour format
    $ampm = !strcmp(date("a", $item['dtstart']['unixtime']),"am") ? 0:1;
    $hour24 = date("H", $item['dtstart']['unixtime']); //24 hour format
    $minute = date("i", $item['dtstart']['unixtime']);
    $year = date("Y", $item['dtstart']['unixtime']);
    $month = date("m", $item['dtstart']['unixtime']);
    $day = date("d", $item['dtstart']['unixtime']);
    //set the start date parts in the data array
    $data['todays_month'] = $month;
    $data['todays_year'] = $year;
    $data['todays_day'] = $day;

    // If there is not a duration, set dur_hours and dur_minutes.
    // Default for both is empty string.
    if (strcmp($item['duration'],'')) {
        list($dur_hours,$dur_minutes) = explode(":",$item['duration']);
    } else {
        $dur_hours = '';
        $dur_minutes = '';
    }

    //Checking to see which repeating rule was used so the event_repeat can be set.
    if ($item['recur_count'] && $item['recur_freq']) {
        $event_repeat = 2;
    } else if (!$item['recur_count'] && $item['recur_freq']) {
        $event_repeat = 1;
    } else {
        $event_repeat = 0;
    }

    //Depending on which rule was used, set the appropriate frequency field to the db value.
    $data['event_repeat_on_freq'] = '';
    $data['event_repeat_freq'] = '';
    if ($event_repeat == 1) {// event repeats every
      $data['event_repeat_freq'] = $item['recur_freq'];
    } else if ($event_repeat == 2) {// event repeats on
      $data['event_repeat_on_freq'] = $item['recur_freq'];
    }

    $data['item'] = $item;

    $data['event_id'] = $item['event_id'];
    $data['title'] = xarVarPrepForDisplay($item['summary']);
    $data['event_month'] =  $month;
    $data['event_day'] = $day;
    $data['event_year'] = $year;
    $data['event_desc'] = xarVarPrepForDisplay($item['description']);
    $data['event_allday'] = $item['eIsallday'];
    $data['event_starttimeh'] = $hour;
    $data['event_starttimeh24'] = $hour24;
    $data['event_starttimem'] = $minute;
    $data['event_startampm'] = $ampm;
    $data['event_dur_hours'] = $dur_hours;
    $data['event_dur_minutes'] = $dur_minutes;

    $data['location'] = xarVarPrepForDisplay($item['location']);
    $data['street1'] = xarVarPrepForDisplay($item['street1']);
    $data['street2'] = xarVarPrepForDisplay($item['street2']);
    $data['city'] = xarVarPrepForDisplay($item['city']);
    $data['state'] = xarVarPrepForDisplay($item['state']);
    $data['postal'] = xarVarPrepForDisplay($item['zip']);
    $data['event_repeat'] = $event_repeat;

    // The phone fields
    $data['phone1'] = '';
    $data['phone2'] = '';
    $data['phone3'] = '';
    //Breaking the phone number into 3 parts
    //TODO: what if the field definition changes? Causes errors when there are less field available.
    if (strcmp($item['phone'],'')!=0) {
        $TelFieldType = xarModGetVar('julian', 'TelFieldType');
        $phoneArray = explode("-",$item['phone']);
        if (strcmp($TelFieldType,'US')==0) {
          $data['phone1'] = $phoneArray[0];
          $data['phone2'] = $phoneArray[1];
          $data['phone3'] = $phoneArray[2];
        } elseif (strcmp($TelFieldType, 'EU')==0) {
          $data['phone1'] = $phoneArray[0];
          $data['phone2'] = $phoneArray[1];
          $data['phone3'] = $phoneArray[2];
        } elseif (strcmp($TelFieldType, 'EUC')==0) {
          $data['phone1'] = $phoneArray[0];
          $data['phone2'] = $phoneArray[1];
        } elseif (strcmp($TelFieldType, 'OPEN')==0) {
          $data['phone1'] = $phoneArray[0];
        }
    }
    $data['email'] = $item['email'];
    $data['fee'] = $item['fee'];
    $data['website'] = $item['url'];
    $data['contact'] = $item['contact'];
    $data['event_repeat_freq_type'] = $item['rrule'];
    $data['event_endmonth'] = $event_endmonth;
    $data['event_endday'] = $event_endday;
    $data['event_endyear'] = $event_endyear;
    $data['event_repeat_on_day'] = $item['recur_count'];
    $data['event_repeat_on_num'] = $item['recur_interval'];

    //building share options
    $data['share_uids'] = $item['share_uids'];
    $data['share_group'] = xarModGetVar('julian', 'share_group');
    // Build the group name. Type 1 is a group
    $group = xarModAPIFunc ('roles', 'user', 'get', array('uid'=> $data['share_group'], 'type' =>1));
    $data['group_validation']= 'group:'.$group['name'];

    // Determining which end date radio to check. 0 index indicates this event has an end date and 1 index means it does not
    // event_repeat tells the type of repeat
    $event_endtype_checked[0] = '';
    $event_endtype_checked[1] = 'checked';
    if (($event_endyear > 0) && ($event_repeat > 0)) {
        $event_endtype_checked[0] = 'checked';
        $event_endtype_checked[1] = '';
    }
    $data['event_endtype_checked'] = $event_endtype_checked;

    //determine if this is there is an enddate present
    $data['enddatedisabled'] = 'disabled';
    if ($event_repeat > 0) {
        $data['enddatedisabled'] = '';
    }

    $data['cal_date'] = $cal_date;

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
     if ($item['rrule'] == $i) {
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
        if ($item['recur_interval'] == $i) {
            $data['repeat_on_num_selected'][$i] = 'selected';
        }
    }

    //Setting repeat on day selection
    for ($i = 1; $i < 8; $i++) {
        $data['repeat_on_day_selection'][$i] = '';
        if ($item['recur_count'] == $i) {
            $data['repeat_on_day_selection'][$i] = 'selected';
        }
    }

    // DEPRECATED - tag attributes are set in the template
    // Setting allday checked
    $data['allday_checked'][0] = '';
    $data['allday_checked'][1] = 'checked';
    $data['timeddisabled'] = '';
    if ($item['eIsallday'] == 1) {
        $data['allday_checked'][0] = 'checked';
        $data['allday_checked'][1] = '';
        $data['timeddisabled'] = 'disabled';
    }

    // DEPRECATED - tag attributes are set in the template
    // 0 = CAL_CLASS_PUBLIC
    // 1 = CAL_CLASS_PRIVATE
    //determine if this is a public or private event
    $data['class'][0] = 'checked ';
    $data['class'][1] = '';
    if ($item['class'] == 1) {
        $data['class'][0] = '';
        $data['class'][1] = 'checked ';
    }

    // Get hook information for the event that we will edit.
    // Build description for the item we want the hooks (i.e. category) for.
    $item = array();
    $item['module'] = 'julian';
    $hooks = xarModCallHooks('item', 'modify', $event_id, $item);

    // Deal with no-hook scenario (the template then must get an empty hook-array)
     if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }

    $data['authid']=xarSecGenAuthKey();
    return $data;
}
?>
