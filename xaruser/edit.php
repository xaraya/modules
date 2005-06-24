<?php
   /**
      * File: $Id: edit.php,v 1.4 2005/03/27 13:52:53 michelv01 Exp $
      *
      * Generates a form for editing an existing event.
      *
      * @package Xaraya eXtensible Management System
      * @copyright (C) 2004 by Metrostat Technologies, Inc.
      * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
      * @link http://www.metrostat.net
      *
      * @subpackage julian
      * initial template: Roger Raymond
      * @author Jodie Razdrh/John Kevlin/David St.Clair
   */
  function julian_user_edit()
  {  
   //This prevents users from viewing something they are not suppose to.
   if (!xarSecurityCheck('Editjulian')) return;  
   //get post/get vars
   if(!xarVarFetch('id','isset',$id)) return;
   if(!xarVarFetch('cal_sdow','int:0:6',$cal_sdow,0)) return;
   if(!xarVarFetch('cal_date','int::',$cal_date,0)) return;
   //load the calendar class
   $c = xarModAPIFunc('julian','user','factory','calendar');
   $c->setStartDayOfWeek($cal_sdow);
    //set the selected date parts and timestamp in the data array
   $bl_data = xarModAPIFunc('julian','user','getUserDateTimeInfo');
   
   // establish db connection      
   $dbconn =& xarDBGetConn();
   //get db tables
   $xartable = xarDBGetTables();
   //set events table
   $event_table = $xartable['julian_events'];
   //retrieve the data from the db for a specific event
   $query = "SELECT *,if(recur_until LIKE '0000%',0,1) as hasRecurDate FROM " . $event_table . " WHERE `event_id`='".$id."'";
   $result = $dbconn->Execute($query);
   $edit_obj = $result->FetchObject(false);
   //determine the end date for a recurring event
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
 
   //If there is not a duration, set dur_hours and dur_minutes. Default for both is empty string.
   $dur_hours = '';
   $dur_minutes = '';
   if (strcmp($edit_obj->duration,''))
     list($dur_hours,$dur_minutes) = explode(":",$edit_obj->duration);
   
   //Checking to see which repeating rule was used so the event_repeat can be set.
   $event_repeat=0;
   if ($edit_obj->recur_count && $edit_obj->recur_freq)
      $event_repeat = 2;
   else if (!$edit_obj->recur_count && $edit_obj->recur_freq) 
     $event_repeat = 1;
   //Depending on which rule was used, set the appropriate frequency field to the db value.
   $bl_data['event_repeat_on_freq'] = '';
   $bl_data['event_repeat_freq'] = '';
   if ($event_repeat == 1) // event repeats every
      $bl_data['event_repeat_freq'] = $edit_obj->recur_freq;
   else if ($event_repeat == 2) // event repeats on
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
   $bl_data['allday_checked'][0] = "";
   $bl_data['allday_checked'][1] = "checked";
   if ($edit_obj->isallday)
   {
     $bl_data['allday_checked'][0] = "checked";
     $bl_data['allday_checked'][1] = "";
   } 
   //determine if this is a public or private event
   $bl_data['class'][0] = "checked";
   $bl_data['class'][1] = "";
   if ($edit_obj->class)
   {
     $bl_data['class'][0] = "";
     $bl_data['class'][1] = "checked";
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
  }  
?>
