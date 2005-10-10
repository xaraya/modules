<?php
/**
 * Views an event.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage julian
 * initial template: Roger Raymond
 * @link http://www.metrostat.net 
 * 
 */
/**
 * View an event
 *
 * @copyright (C) 2005 by Metrostat Technologies, Inc.
 * @author  Jodie Razdrh/John Kevlin/David St.Clair
 * @author  Julian Development Team, MichelV. <michelv@xarayahosting.nl>
 * @access  public 
 * @param   ID $event_id for the event to display
 * @param   $cal_date
 * @return  array
 * @todo    MichelV. <#> Make this the display function and move queries to API
 */
function julian_user_viewevent()
{
    //get post/get vars
    if (!xarVarFetch('event_id','str',$event_id)) return; // INT here?
    if (!xarVarFetch('cal_date','int',$cal_date)) return; // str here?
    
    // Security check
    if (!xarSecurityCheck('ViewJulian')) return; 
    // TODO: make this an API for linked event
    // establish a db connection
    $dbconn =& xarDBGetConn();
    // get db tables
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

   // Get the event, as it is not in the linked table
   // We use event_id here
   
   $bl_data = array();
   $bl_data = xarModAPIFunc('julian','user','get',array('event_id'=>$event_id));
   
   // Make an admin adjustable time format
   $dateformat=xarModGetVar('julian', 'dateformat');
   $timeformat=xarModGetVar('julian', 'timeformat');  
   $dateformat_created="$dateformat $timeformat";
   
    // Don't like this here
    if (!isset($bl_data['recur_until']) || is_numeric($bl_data['recur_until']) || strrchr($bl_data['recur_until'], '0000')) {
        $bl_data['recur_until'] = 'recur_until';
    }

   $bl_data['id'] = $bl_data['event_id'];
   $bl_data['deletesummary'] = xarVarPrepForDisplay($bl_data['summary']);

   $bl_data['organizer'] = xarUserGetVar('name',$bl_data['organizer']);
   $bl_data['datecreated'] = date("$dateformat_created",strtotime($bl_data['created']));
   $bl_data['fee'] = strcmp($bl_data['fee'],"")?xarLocaleFormatCurrency($bl_data['fee']):xarML('Unknown');
   $bl_data['authid'] = xarSecGenAuthKey();
   // Add obfuscator: for later Bug 4971
   // $bl_data['email'] = xarModAPIFunc('sitecontact', 'user', 'obfuemail', array('email'=>$bl_data['email']));
   
   /* Get rid of the NULLs */
   
    if (isset($bl_data['phone'])) {
        $bl_data['phone'] = xarVarPrepForDisplay($bl_data['phone']);
    } else {
        $bl_data['phone'] ='';
    }
    if (!is_null($bl_data['url'])) {  
        $bl_data['URL'] = xarVarPrepForDisplay($bl_data['url']); // TODO: Get rid of this   
    } else {
        $bl_data['URL'] ='';
    }
    if (isset($bl_data['zip'])) {
        $bl_data['zip'] = xarVarPrepForDisplay($bl_data['zip']);
    } else {
        $bl_data['zip'] ='';
    }


   //if there was a duration set for this event, format a string indicating the from and to times
   $duration='';
   if(strcmp($bl_data['duration'],""))
   {
     list($hours,$minutes) = explode(":",$bl_data['duration']);
     $duration=" from ".date("g:i A",strtotime($bl_data['dtstart']))." to ".date("g:i A",strtotime("+".$hours." hours ".$minutes." minutes",strtotime($bl_data['dtstart'])));
   }
   //Checking if we are viewing a reoccuring event
   if ($bl_data['recur_freq']) {
      $recur_count = $bl_data['recur_count'];
      $rrule = $bl_data['rrule'];
      $recur_interval = $bl_data['recur_interval'];
      $intervals = array("1"=>"Day(s)","2"=>"Week(s)","3"=>"Month(s)","4"=>"Year(s)");
      $day_array = array("1"=>"Sunday","2"=>"Monday","3"=>"Tuesday","4"=>"Wednesday","5"=>"Thursday","6"=>"Friday","7"=>"Saturday");
      //build the effective date string
      $eff = " effective ".date("$dateformat",strtotime($bl_data['dtstart']));
      //start the time string
      $time = xarML('Occurs ');
      //Build the strings to describe the repeating event.
      if (!$bl_data['recur_count']) {
         //this is for the 'every' recurring event type
         $time .= "every ".$bl_data['recur_freq']." ".$intervals[$rrule] . " on " . date('l',strtotime($bl_data['dtstart'])) . $eff;
      } else {
         // build a day array
         $weektimes = array("1"=>"First","2"=>"Second","3"=>"Third","4"=>"Fourth","5"=>"Last");
         // this is for the 'on every' recurring event type
         $time .= "the ".$weektimes[$recur_interval] ." ".$day_array[$recur_count]." every ".$bl_data['recur_freq']." ". $intervals[$rrule] . $eff;
      }
      
      
      //add the end date if one exists
      if (strcmp($bl_data['recur_until'],""))
         $time .= " until ".date("$dateformat",strtotime($bl_data['recur_until']));
     //if the duration has not been set and this is not an all day event, add the start time to the string
     $duration=strcmp($duration,"")?$duration:($bl_data['isallday']?'':' at '.date("g:i A",strtotime($bl_data['dtstart'])));
     $bl_data['time'] = $time.$duration .".";
     
   //If there is no duration and this is not an all day event, show the time at the front.
   } else if (!$bl_data['isallday'] && !strcmp($duration,'')) {
      $bl_data['time'] = date("g:i A l, $dateformat",strtotime($bl_data['dtstart']));
   } else {
      $bl_data['time'] = date("l, $dateformat",strtotime($bl_data['dtstart'])).$duration;
   }
   $bl_data['cal_date']=$cal_date;
   //set the url to this page in session as the last page viewed
   $lastview=xarModURL('julian','user','viewevent',array('cal_date'=>$cal_date,'event_id'=>$event_id));
   xarSessionSetVar('lastview',$lastview);
   return $bl_data;
}
?>
