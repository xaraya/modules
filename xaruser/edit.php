<?php
/**
 * Generates a form for editing an existing event.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by Metrostat Technologies, Inc.
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
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @author  MichelV (Michelv@xarayahosting.nl)
 * @access  public
 * @param   int $event_id ID of the event to get
 * @return  array $item
 * @throws  BAD_PARAM list of exception identifiers which can be thrown
 * @todo    Michel V. <1> Clean up
 */
function julian_user_edit()
  {

    if (!xarVarFetch('id',      'id',    $id)) return;
    if (!xarVarFetch('objectid', 'id',   $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    // This is the var to set the first day of the week
    if (!xarVarFetch('cal_date','int::', $cal_date, 0, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $id = $objectid;
    }
/*

   //load the calendar class
   // TODO: get rid of this call
   $c = xarModAPIFunc('julian','user','factory','calendar');
    //set the selected date parts and timestamp in the data array
   $bl_data = xarModAPIFunc('julian','user','getUserDateTimeInfo');

   // TODO: make sure we don't need this
   // establish db connection
   $dbconn = xarDBGetConn();
   //get db tables
   $xartable = xarDBGetTables();
   //set events table
   $event_table = $xartable['julian_events'];
   //retrieve the data from the db for a specific event
   $query = "SELECT *,if(recur_until LIKE '0000%',0,1) as hasRecurDate FROM " . $event_table . " WHERE  event_id ='".$id."'";
   $result = $dbconn->Execute($query);
   $edit_obj = $result->FetchObject(false);
   // determine the end date for a recurring event
   // TODO: With the new get.php this should be rewritten
   list($event_endyear,$event_endmonth,$event_endday) = explode("-",$edit_obj->recur_until);

   //setting start date time variables
   $hour = date("h",strtotime($edit_obj->dtstart)); //12 hour format
   $ampm = !strcmp(date("a",strtotime($edit_obj->dtstart)),"am")?0:1;
   $minute = date("i",strtotime($edit_obj->dtstart));
   list($year,$month,$day) = explode("-",date("Y-m-d",strtotime($edit_obj->dtstart)));
   //set the start date parts in the data array
   $bl_data['todays_month'] = $month;
   $bl_data['todays_year'] = $year;
   $bl_data['todays_day'] = $day;
*/
   // Get event the decent way
   $item = xarModAPIFunc('julian', 'user', 'get', array('event_id' => $id));
   // Security check
   if (!xarSecurityCheck('EditJulian', 1, 'Item', "$id:$item[organizer]:$item[class]:$item[calendar_id]:All")) {
       return;
   }

   $event_endyear='';
   $event_endmonth='';
   $event_endday='';
   if($item['recur_until']) {
       // End date and time
       // determine the end date for a recurring event
       // TODO: With the new get.php this should be rewritten
       list($event_endyear,$event_endmonth,$event_endday) = explode("-",$item['recur_until']);
   }

    //Date time from item
    //setting start date time variables
    $hour = date("h",strtotime($item['dtstart'])); //12 hour format
    $ampm = !strcmp(date("a",strtotime($item['dtstart'])),"am")?0:1;
    $minute = date("i",strtotime($item['dtstart']));
    list($year,$month,$day) = explode("-",date("Y-m-d",strtotime($item['dtstart'])));
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

    $data['event_id'] = $item['event_id'];
    $data['title'] = xarVarPrepForDisplay($item['summary']);
    $data['month'] =  $month;
    $data['day'] = $day;
    $data['event_year'] = $year;
    $data['event_desc'] = xarVarPrepForDisplay($item['description']);
    $data['event_allday'] = $item['isallday'];
    $data['event_starttimeh'] = $hour;
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
    $data['share_options'] = xarModAPIFunc('julian','user','getuseroptions',array('uids'=>$item['share_uids']));
    $data['share_uids'] = $item['share_uids'];
    $data['share_group'] = xarModGetVar('julian', 'share_group');
    // Build the group name. Type 1 is a group
    $group = xarModAPIFunc ('roles', 'user', 'get', array('uid'=> $data['share_group'], 'type' =>1));
    $data['share_group_name'] = $group['name'];

    //Determining which end date radio to check. 0 index indicates this event has an end date and 1 index means it does not
    $event_endtype_checked[0] = '';
    $event_endtype_checked[1] = 'checked';
    if (strrchr($item['recur_until'], '0000') !== false) {
    //if ($item['recur_until'] == 0000) {
        $event_endtype_checked[0] = 'checked';
        $event_endtype_checked[1] = '';
    }
    $data['event_endtype_checked'] = $event_endtype_checked;

    //Building start hour options
    $start_hour_options = '';
    for($i = 1;$i <= 12; $i++) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $start_hour_options.='<option value="'.$i.'"';
        if ($i == $hour)
            $start_hour_options.= " SELECTED";
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
   for ($i = 0; $i < 3; $i++)
     $data['event_repeat_checked'][$i] = '';
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

   //Setting allday checked
   $data['allday_checked'][0] = '';
   $data['allday_checked'][1] = 'checked';
   if ($item['isallday'] == 1) {
     $data['allday_checked'][0] = 'checked';
     $data['allday_checked'][1] = '';
   }
   // 0 = CAL_CLASS_PUBLIC
   // 1 = CAL_CLASS_PRIVATE
   //determine if this is a public or private event
   $data['class'][0] = 'checked ';
   $data['class'][1] = '';
   if ($item['class'] == 1) {
     $data['class'][0] = '';
     $data['class'][1] = 'checked ';
   }
   //determine if this is there is an enddate present
   $data['enddatedisabled'] = 'disabled';
   if (isset($event_endmonth) || isset($event_endday) || isset($event_endyear)) {
     $data['enddatedisabled'] = '';
   }

   $data['cal_date'] = $cal_date;

    // Get hook information for the event that we will edit.
    // Build description for the item we want the hooks (i.e. category) for.
    $item = array();
    $item['module'] = 'julian';
    $hooks = xarModCallHooks('item', 'modify', $id, $item);

    // Deal with no-hook scenario (the template then must get an empty hook-array)
     if (empty($hooks)) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    }
    return $data;

/*


   //If there is not a duration, set dur_hours and dur_minutes. Default for both is empty string.
   $dur_hours = '';
   $dur_minutes = '';
   if (strcmp($edit_obj->duration,''))
     list($dur_hours,$dur_minutes) = explode(":",$edit_obj->duration);

   //Checking to see which repeating rule was used so the event_repeat can be set.
   $event_repeat=0;
   if ($edit_obj->recur_count && $edit_obj->recur_freq)
      $event_repeat = 2;
   elseif (!$edit_obj->recur_count && $edit_obj->recur_freq)
     $event_repeat = 1;
   //Depending on which rule was used, set the appropriate frequency field to the db value.
   $bl_data['event_repeat_on_freq'] = '';
   $bl_data['event_repeat_freq'] = '';
   if ($event_repeat == 1) // event repeats every
      $bl_data['event_repeat_freq'] = $edit_obj->recur_freq;
   elseif ($event_repeat == 2) // event repeats on
      $bl_data['event_repeat_on_freq'] = $edit_obj->recur_freq;

   $bl_data['event_id'] = $edit_obj->event_id;
   $bl_data['title'] = $edit_obj->summary;
   $bl_data['month'] =  $month;
   $bl_data['day'] = $day;
   $bl_data['event_year'] = $year;
   $bl_data['event_desc'] = $edit_obj->description;
   $bl_data['event_allday'] = $edit_obj->isallday;
   $bl_data['event_starttimeh'] = $hour;
   $bl_data['event_starttimem'] = $minute;
   $bl_data['event_startampm'] = $ampm;
   $bl_data['event_dur_hours'] = $dur_hours;
   $bl_data['event_dur_minutes'] = $dur_minutes;
   $bl_data['category'] = $edit_obj->categories;
   $bl_data['location'] = $edit_obj->location;
   $bl_data['street1'] = $edit_obj->street1;
   $bl_data['street2'] = $edit_obj->street2;
   $bl_data['city'] = $edit_obj->city;
   $bl_data['state'] = $edit_obj->state;
   $bl_data['postal'] = $edit_obj->zip;
   $bl_data['event_repeat'] = $event_repeat;


   $bl_data['phone1'] = '';
   $bl_data['phone2'] = '';
   $bl_data['phone3'] = '';
   //Breaking the phone number into 3 parts
   //TODO: what if the field definition changes? Causes errors when there are less field available.
   if (strcmp($edit_obj->phone,'')!=0)
   {
   $TelFieldType = xarModGetVar('julian', 'TelFieldType');
      $phoneArray = explode("-",$edit_obj->phone);
     if (strcmp($TelFieldType,'US')==0) {
      $bl_data['phone1'] = $phoneArray[0];
      $bl_data['phone2'] = $phoneArray[1];
      $bl_data['phone3'] = $phoneArray[2];
      }
     elseif (strcmp($TelFieldType, 'EU')==0) {
      $bl_data['phone1'] = $phoneArray[0];
      $bl_data['phone2'] = $phoneArray[1];
      $bl_data['phone3'] = $phoneArray[2];
      }
     elseif (strcmp($TelFieldType, 'EUC')==0) {
      $bl_data['phone1'] = $phoneArray[0];
      $bl_data['phone2'] = $phoneArray[1];
      }
     elseif (strcmp($TelFieldType, 'OPEN')==0) {
      $bl_data['phone1'] = $phoneArray[0];
      }
   }
   $bl_data['email'] = $edit_obj->email;
   $bl_data['fee'] = $edit_obj->fee;
   $bl_data['website'] = $edit_obj->url;
   $bl_data['contact'] = $edit_obj->contact;
   $bl_data['event_repeat_freq_type'] = $edit_obj->rrule;
   $bl_data['event_endmonth'] = $event_endmonth;
   $bl_data['event_endday'] = $event_endday;
   $bl_data['event_endyear'] = $event_endyear;
   $bl_data['event_repeat_on_day'] = $edit_obj->recur_count;
   $bl_data['event_repeat_on_num'] = $edit_obj->recur_interval;

   //building share options
   $bl_data['share_options'] = xarModAPIFunc('julian','user','getuseroptions',array('uids'=>$edit_obj->share_uids));
   $bl_data['share_uids'] = $item['share_uids'];
   $bl_data['share_group'] = xarModGetVar('julian', 'share_group');
   // Build the group name. Type 1 is a group
   $group = xarModAPIFunc ('roles', 'user', 'get', array('uid'=> $bl_data['share_group'], 'type' =>1));
   $bl_data['share_group_name'] = $group['name'];

   //Determining which end date radio to check. 0 index indicates this event as an end date and 1 index means it does not
   $event_endtype_checked[0] = '';
   $event_endtype_checked[1] = 'checked';
   if ($edit_obj->hasRecurDate)
   {
     $event_endtype_checked[0] = 'checked';
     $event_endtype_checked[1] = '';
   }
   $bl_data['event_endtype_checked'] = $event_endtype_checked;

   //Building start hour options
   $start_hour_options = '';
   for($i = 1;$i <= 12; $i++)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $start_hour_options.='<option value="'.$i.'"';
     if ($i == $hour)
        $start_hour_options.= " SELECTED";
      $start_hour_options.='>'.$j.'</option>';
   }
   $bl_data['start_hour_options'] = $start_hour_options;

   //Building start minute options
   $start_minute_options = '';
   for($i = 0;$i < 46; $i = $i + 15)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $start_minute_options.='<option value="'.$i.'"';
     if ($i == $minute)
       $start_minute_options.= " selected";
     $start_minute_options.='>'.$j.'</option>';
   }
   $bl_data['start_minute_options'] = $start_minute_options;

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
   $bl_data['dur_hour_options'] = $dur_hour_options;

   //Building duration minute options
   $dur_minute_options = '';
   for($i = 0;$i < 46; $i = $i + 15)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $dur_minute_options.='<option value="'.$j.'"';
     if ($i == $dur_minutes)
        $dur_minute_options.= " selected";
     $dur_minute_options.='>'.$j.'</option>';
   }
   $bl_data['dur_minute_options'] = $dur_minute_options;

   //Setting event repeat selection
   for ($i = 0; $i < 3; $i++)
     $bl_data['event_repeat_checked'][$i] = '';
   $bl_data['event_repeat_checked'][$event_repeat] = "checked";

   //Setting freq type selection (days,weeks,months,years)
   for ($i = 1; $i < 5; $i++)
     $bl_data['freq_type_selected'][$i] = '';

   //Show rrule only if the first repeating option was selected (2nd radio button) - every
   if ($event_repeat == 1)
     $bl_data['freq_type_selected'][$edit_obj->rrule] = 'selected';

   //Setting repeat on num selection
   for ($i = 1; $i < 6; $i++)
     $bl_data['repeat_on_num_selected'][$i] = '';
   $bl_data['repeat_on_num_selected'][$edit_obj->recur_interval] = 'selected';

   //Setting repeat on day selection
   for ($i = 1; $i < 8; $i++)
     $bl_data['repeat_on_day_selection'][$i] = '';
   $bl_data['repeat_on_day_selection'][$edit_obj->recur_count] = 'selected';


   //Setting allday checked
   $bl_data['allday_checked'][0] = '';
   $bl_data['allday_checked'][1] = 'checked';
   if ($item['isallday'] == 1) {
     $bl_data['allday_checked'][0] = 'checked';
     $bl_data['allday_checked'][1] = '';
   }
   //determine if this is a public or private event
   $bl_data['class'][0] = 'checked';
   $bl_data['class'][1] = '';
   if ($edit_obj->class) {
     $bl_data['class'][0] = '';
     $bl_data['class'][1] = 'checked';
   }

   // 0 = CAL_CLASS_PUBLIC
   // 1 = CAL_CLASS_PRIVATE
   //determine if this is a public or private event
   $bl_data['class'][0] = 'checked ';
   $bl_data['class'][1] = '';
   if ($item['class'] == 1) {
     $bl_data['class'][0] = '';
     $bl_data['class'][1] = 'checked ';
   }

   //determine if this is there is an enddate present
   $bl_data['enddatedisabled'] = 'disabled';
   if (isset($event_endmonth) || isset($event_endday) || isset($event_endyear)) {
     $bl_data['enddatedisabled'] = '';
   }

    // Get hook information for the event that we will edit.
    // Build description for the item we want the hooks (i.e. category) for.
    $item = array();
    $item['module'] = 'julian';
    $item['multiple'] = false;    // Doesn't function yet, requires change in  categories_admin_newhook
     //$item['itemtype'] = empty, because no item type is needed (we have on one type of object;
     //     module variable number_of_categories.itemtype should be made if we set itemtype)

     // Get the hooks for this item.
     //    xarModCallHooks parameters:
     //        hookObject (string) - what object are we working on
     //       hookAction (string) - what are we doing with the object?
     //        hookId (integer) - id of the object we are working on
     //        extraInfo (dictionary) - additional info on the current object
     //       callerModName (string) - name of the calling module (deprecated, specify in extraInfo instead)
     //        callerItemType (string) - the type of item (deprecated, specify in extraInfo instead)
    $hooks = xarModCallHooks('item', 'modify', $id, $item);

    // Deal with no-hook scenario (the template then must get an empty hook-array)
     if (empty($hooks)) {
        $bl_data['hooks'] = array();
    } else {
        $bl_data['hooks'] = $hooks;
    }
   return $bl_data;
*/
}
?>
