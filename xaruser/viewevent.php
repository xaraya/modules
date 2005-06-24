<?php

/**
* File: $Id: viewevent.php,v 1.2 2005/06/24 09:28:25 michelv01 Exp $
*
* Views an event.
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

function julian_user_viewevent()
{
   //get post/get vars
   if (!xarVarFetch('event_id','str',$event_id)) return;
   if (!xarVarFetch('cal_date','int',$cal_date)) return;
   
   // Security check - important to do this as early as possible to avoid
   // potential security holes or just too much wasted processing
   if (!xarSecurityCheck('Viewjulian')) return; 
    
   //establish a db connection
    $dbconn =& xarDBGetConn();
   //get db tables
   $xartable = xarDBGetTables();
   
    $matches = array();
    if (preg_match("/^(\d+)_link$/",$event_id,$matches)) {
        $linkid = $matches[1];
       $query = "SELECT `hook_modid`,`hook_itemtype`,`hook_iid` FROM `".$xartable['julian_events_linkage']."` WHERE `event_id`='$linkid'";
        $result = $dbconn->Execute($query);
        $event_obj = $result->FetchObject(false);
        $event = array();
        $event = xarModAPIFunc('julian', 'user', 'geteventinfo', array('event'=>$event,
                                                                                            'modid'=>$event_obj->hook_modid,
                                                                                            'iid'=>$event_obj->hook_iid,
                                                                                            'itemtype'=>$event_obj->hook_itemtype));
        return xarResponseRedirect($event['viewURL']);
    }

   //set events table
   $event_table = $xartable['julian_events'];

   //Get all the info for the event
   $query = "SELECT *,if(recur_until LIKE '0000%','',recur_until) AS recur_until FROM " . $event_table . "
             WHERE event_id = ".$event_id.";";
   $result = $dbconn->Execute($query);
   $event_obj = $result->FetchObject(false);
   //Make an admin adjustable time format
   $dateformat=xarModGetVar('julian', 'dateformat');
   $timeformat=xarModGetVar('julian', 'timeformat');  
   $dateformat_created="$dateformat $timeformat";

   $bl_data['id'] = $event_obj->event_id;
   $bl_data['summary'] = $event_obj->summary;
   $bl_data['deletesummary']=addslashes($event_obj->summary);
   $bl_data['description'] = $event_obj->description;
   $bl_data['duration'] = $event_obj->duration;
   $bl_data['street1'] = $event_obj->street1;
   $bl_data['street2'] = $event_obj->street2;
   $bl_data['city'] = $event_obj->city;
   $bl_data['state'] = $event_obj->state;
   $bl_data['zip'] = $event_obj->zip;
   $bl_data['email'] = $event_obj->email;
   $bl_data['phone'] = $event_obj->phone;
   $bl_data['fee'] = $event_obj->fee;
   $bl_data['location'] = $event_obj->location;
   $bl_data['URL'] = $event_obj->url;
   $bl_data['contact'] = $event_obj->contact;
   $bl_data['organizer'] = xarUserGetVar('name',$event_obj->organizer);
   $bl_data['datecreated'] = date("$dateformat_created",strtotime($event_obj->created));
   $bl_data['isallday'] = $event_obj->isallday;
   $bl_data['fee'] = strcmp($event_obj->fee,"")?xarLocaleFormatCurrency($event_obj->fee):'Unknown';
    //get the event category color
   $bl_data['color'] = xarModAPIFunc('julian','user','getcolor',array('category'=>$event_obj->categories));    
   
   $recur_freq = $event_obj->recur_freq;


   //if there was a duration set for this event, format a string indicating the from and to times
   $duration='';
   if(strcmp($event_obj->duration,""))
   {
     list($hours,$minutes) = explode(":",$event_obj->duration);
     $duration=" from ".date("g:i A",strtotime($event_obj->dtstart))." to ".date("g:i A",strtotime("+".$hours." hours ".$minutes." minutes",strtotime($event_obj->dtstart)));
   }
   //Checking if we are viewing a reoccuring event
   if ($recur_freq)
   {
      $recur_count = $event_obj->recur_count;
      $rrule = $event_obj->rrule;
      $recur_interval = $event_obj->recur_interval;
      $intervals = array("1"=>"Day(s)","2"=>"Week(s)","3"=>"Month(s)","4"=>"Year(s)");
      $day_array = array("1"=>"Sunday","2"=>"Monday","3"=>"Tuesday","4"=>"Wednesday","5"=>"Thursday","6"=>"Friday","7"=>"Saturday");
      //build the effective date string
      $eff = " effective ".date("$dateformat",strtotime($event_obj->dtstart));
      //start the time string
      $time = "Occurs ";
      //Build the strings to describe the repeating event.
      if (!$recur_count)
      {
         //this is for the 'every' recurring event type
         $time .= "every ".$recur_freq." ".$intervals[$rrule] . " on " . date('l',strtotime($event_obj->dtstart)) . $eff;
      }
      else
      {
        //build a day array
        $weektimes = array("1"=>"First","2"=>"Second","3"=>"Third","4"=>"Fourth","5"=>"Last");
        //this is for the 'on every' recurring event type
        $time .= "the ".$weektimes[$recur_interval] ." ".$day_array[$recur_count]." every ".$recur_freq." ". $intervals[$rrule] . $eff;
      }
      
      //add the end date if one exists
      
      if (strcmp($event_obj->recur_until,""))
         $time .= " until ".date("$dateformat",strtotime($event_obj->recur_until));
     //if the duration has not been set and this is not an all day event, add the start time to the string
     $duration=strcmp($duration,"")?$duration:($event_obj->isallday?'':' at '.date("g:i A",strtotime($event_obj->dtstart)));
     $bl_data['time'] = $time.$duration .".";
   }
   //If there is no duration and this is not an all day event, show the time at the front.
   else if (!$event_obj->isallday && !strcmp($duration,''))
    $bl_data['time'] = date("g:i A l, $dateformat",strtotime($event_obj->dtstart));
   else
    $bl_data['time'] = date("l, $dateformat",strtotime($event_obj->dtstart)).$duration;
   $bl_data['cal_date']=$cal_date;
   //set the url to this page in session as the last page viewed
   $lastview=xarModURL('julian','user','viewevent',array('cal_date'=>$cal_date,'event_id'=>$event_id));
   xarSessionSetVar('lastview',$lastview);
   return $bl_data;
}
?>
