<?php

/**
* File: $Id: updateevent.php,v 1.8 2005/03/30 09:50:04 caseygeene Exp $
*
* Inserts/Updates an event.
*
* @package Xaraya eXtensible Management System
* @copyright (C) 2005 by Metrostat Technologies, Inc.
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.metrostat.net
*
* @subpackage julian
* initial template: Roger Raymond
* @author Jodie Razdrh/John Kevlin/David St.Clair
*/
/**
 * Create or update an event
 *
 * 
 * @author  Jodie Razdrh/John Kevlin/David St.Clair
 * @author  Julian Development Team, MichelV. <michelv@xarayahosting.nl>
 * @access  public 
 * @param   event data
 * @return  returnURL
 * @todo    MichelV. <#> move queries to API
 */
function julian_user_updateevent()
{
   //This prevents users from viewing something they are not suppose to.
   if (!xarSecurityCheck('Editjulian')) return;
   if (!xarVarFetch('cancel','str',$cancel,'')) return;
   //If Cancel was pressed, go back to previous page
   if (strcmp($cancel,''))
   {
      $back_link=xarSessionGetVar('lastview');
      xarResponseRedirect($back_link);
   }
   
   if (!xarVarFetch('id',               'str:1:',   $id,            '')) return;
   if (!xarVarFetch('cal_sdow',         'int:0:6',  $cal_sdow,      0)) return;
   if (!xarVarFetch('title',            'str:1:',   $summary,       '')) return;
   if (!xarVarFetch('month',            'str:1:',   $month,         '')) return;
   if (!xarVarFetch('day',              'int',      $day,           '')) return;
   if (!xarVarFetch('event_year',       'int',      $year,          '')) return;
   if (!xarVarFetch('event_desc',       'str:1:',   $description,   '')) return;
   if (!xarVarFetch('event_allday',     'str:1:',   $event_allday,  0)) return;
   if (!xarVarFetch('event_starttimeh', 'int',      $event_starttimeh,0)) return;
   if (!xarVarFetch('event_starttimem', 'int',      $event_starttimem,0)) return;
   if (!xarVarFetch('event_startampm',  'str:1:',   $event_startampm,0)) return;
   if (!xarVarFetch('event_dur_hours',  'str:1:',   $event_dur_hours,0)) return;
   if (!xarVarFetch('event_dur_minutes','str:1:',   $event_dur_minutes,0)) return;
   if (!xarVarFetch('category',         'str:1:',   $category,      '')) return;
   if (!xarVarFetch('location',         'str:1:',   $location,      'NULL')) return;
   if (!xarVarFetch('street1',          'str:1:',   $street1,       'NULL')) return;
   if (!xarVarFetch('street2',          'str:1:',   $street2,       'NULL')) return;
   if (!xarVarFetch('city',             'str:1:',   $city,          'NULL')) return;
   if (!xarVarFetch('state',            'str:1:',   $state,         'NULL')) return;
   if (!xarVarFetch('postal',           'str:1:',   $zip,           'NULL')) return;
   if (!xarVarFetch('phone1',           'str:1:',   $phone1,        'NULL')) return;
   if (!xarVarFetch('phone2',           'str:1:',   $phone2,        'NULL')) return;
   if (!xarVarFetch('phone3',           'str:1:',   $phone3,        'NULL')) return;
   if (!xarVarFetch('email',            'str:1:',   $email,         'NULL')) return;
   if (!xarVarFetch('fee',              'str:1:',   $fee,           'NULL')) return;
   if (!xarVarFetch('website',          'str:1:',   $website,       'NULL')) return;
   if (!xarVarFetch('contact',          'str:1:',   $contact,       'NULL')) return;
   if (!xarVarFetch('event_repeat_freq_type', 'str:1:', $rrule,     0)) return;
   if (!xarVarFetch('event_repeat',     'str:1:',   $event_repeat,  0)) return;
   if (!xarVarFetch('event_endtype',    'str:1:',   $event_endtype, 0)) return;
   if (!xarVarFetch('event_endmonth',   'str:1:',   $event_endmonth,'')) return;
   if (!xarVarFetch('event_endday',     'str:1:',   $event_endday,  '')) return;
   if (!xarVarFetch('event_endyear',    'str:1:',   $event_endyear, '')) return;   
   if (!xarVarFetch('event_repeat_on_day','str:1:', $recur_count,   '0')) return;
   if (!xarVarFetch('event_repeat_on_num','str:1:', $recur_interval,'0')) return;
   if (!xarVarFetch('event_repeat_on_freq','str:1:',$recur_freq1,   '0')) return;   
   if (!xarVarFetch('event_repeat_freq','str:1:',   $recur_freq2,   '0')) return;
   if (!xarVarFetch('class',            'str:1:',   $class,         '0')) return;
   if (!xarVarFetch('share_uids',       'array',    $share_uids,    array())) return;
   
   //if this is an event that repeats "on", the rrule is always 3 which is the representative of monthly.
   //the 'on' events are always repeated every so many months
   if($recur_count && $recur_freq1) {
      $rrule="3";
   }
   //if the user wants to share this event, build an array of users that will share the private event
   $share='';
   if(!empty($share_uids)) {
      $share=implode(",",$share_uids);
   }
   $eventstartdate = $year."-".$month."-".$day;
   //if this is a recurring event, determine the start date based on the recur type and the selected start date by the user. 
   //Otherwise, the start date is the one selected by the user.
   if($recur_freq1>0) {
      //load the event class
      $e = xarModAPIFunc('julian','user','factory','event');
      //set the start date for this recurring event
      $eventstartdate=$e->setRecurEventStartDate($eventstartdate,$recur_interval,$recur_count,$recur_freq1); 
   }
   //set the calendar date equal to the start date of this event in the format of 'Ymd'
   $cal_date=date('Ymd',strtotime($eventstartdate));
      
   //Checking which event_repeat rule is being used and setting the recur_freq to the right reoccuring frequency
   //recur_freq_array values represent the first, second and third radio buttons on the form for repeating events.
   //Using this because the value is being written to the same place in the database.
   $recur_freq_array = array("",$recur_freq2,$recur_freq1);
   $recur_freq = $recur_freq_array[$event_repeat];
    
   //Checking to see if there is an end date for this event. There will only be an end date on recurring events
   if ($event_endtype) {
     $recur_until = $event_endyear."-".$event_endmonth."-".$event_endday;
   } else {
     $recur_until = '';   
   }
   //If not an all day event, eventstartdate gets a time and duration.
   $duration="";
   if (!$event_allday) {
      $ampm = $event_startampm==1?"AM":"PM";
      $eventstartdate =  date("Y-m-d H:i:s",strtotime($eventstartdate." ".$event_starttimeh.":".$event_starttimem.":00 ".$ampm));
      $duration = strcmp($event_dur_hours.':'.$event_dur_minutes,'0:00')?$event_dur_hours.':'.$event_dur_minutes:'';
   } 
   //Putting the 3 parts of the phone number back into 1.
   //If phone1 is empty, phone2 and phone3 have to be empty
   //and we don't want to show the dashes.
   //TODO: make it possible to have a European type/custom phone field

   $TelFieldType = xarModGetVar('julian', 'TelFieldType');
   $phone = '';      
   if (strcmp($phone1,'')) {
     if (!strcmp($TelFieldType,'US')) {
       $phone = $phone1."-".$phone2."-".$phone3;
       }
     elseif (!strcmp($TelFieldType,'EU')) {
       $phone = $phone1."-".$phone2."-".$phone3;
       }
     elseif (!strcmp($TelFieldType,'EUC')) {
       $phone = $phone1."-".$phone2;
       }
     elseif (!strcmp($TelFieldType,'OPEN')) {
       $phone = $phone1;
       }
    }
    
    // TODO: move this to API
    // Load up database
    $dbconn =& xarDBGetConn();
    //get db tables
    $xartable = xarDBGetTables();
    //set events table
    $event_table = $xartable['julian_events'];
    
    
    if(strcmp($id,"")) {
        $now = "now()";
        $query = "UPDATE " .  $event_table . "
                SET isallday= ?,
                contact= ?,
                url= ?,
                summary= ?,
                description= ?,
                class= ?,
                location= ?,
                share_uids= ?,
                street1= ?,
                street2= ?,
                city= ?,
                state= ?,
                zip= ?,
                phone= ?,
                email= ?,
                fee= ?,
                categories= ?,
                rrule= ?,
                recur_freq= ?,
                recur_until= ?,
                recur_count= ?,
                recur_interval= ?,
                duration= ?,
                dtstart= ?,
                last_modified= ?
                WHERE event_id =".$id."";
                $bindvars = array ($event_allday, $contact, $website, $summary, $description, $class, $location, $share, $street1, $street2, $city, $state, $zip, $phone, $email, $fee, $category, $rrule, $recur_freq, $recur_until, $recur_count, $recur_interval, $duration, $eventstartdate, $now);
                $result = $dbconn->Execute($query, $bindvars);
                
        // Call the hooks. Event already exists (we are just updating)
        $item = array();
        $item['module'] = 'julian';
        $hooks = xarModCallHooks('item', 'update', $id, $item);
        
    } else {
    
        $query = "INSERT INTO " .  $event_table . " 
                SET calendar_id= ?,
                isallday=?, 
                organizer=?,
                contact=?,
                url=?,
                summary=?,
                description=?, 
                class=?,
                priority=?,
                status=?,
                share_uids=?, 
                location=?,
                street1=?,
                street2=?,
                city=?,
                state=?,
                zip=?,
                phone=?,
                email=?,
                fee=?,
                categories=?,
                rrule=?,
                recur_freq=?, 
                recur_until=?,
                recur_count=?,
                recur_interval=?,
                duration=?,
                dtstart=?,
                transp=?,
                created=?";
                $created =date("Y-m-d H:i:s");
                $uidnow = xarUserGetVar('uid');
                $bindvars = array ('0', $event_allday, $uidnow, $contact, 
                $website, $summary, $description , $class, 0, 1, $share, $location, 
                $street1, $street2, $city, $state, $zip, $phone, $email, $fee, $category,
                $rrule, $recur_freq, $recur_until, $recur_count, $recur_interval, $duration, $eventstartdate,
                1, $created);
                $result = $dbconn->Execute($query, $bindvars);
                $id=$dbconn->Insert_ID();
                
        // Call the hooks. Event is new, we have just created it.
        $item = array();
        $item['module'] = 'julian';
        $hooks = xarModCallHooks('item', 'create', $id, $item);
                  
    }   
    //Go back to the view of the event
    xarResponseRedirect(xarModURL('julian', 'user', 'viewevent',array('cal_date'=>$cal_date,'event_id' => $id)));
}

?>
