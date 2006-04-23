<?php
/**
 * Inserts/Updates an event.
 *
 * @package modules
 * @copyright (C) 2005 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * @subpackage Julian Module
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 */
/**
 * Create or update an event
 *
 * @author  Jodie Razdrh/John Kevlin/David St.Clair
 * @author  MichelV <michelv@xaraya.com>
 * @author  Zsolt for PostGres compatability
 * @access  public
 * @param   array event data
 * @return  array returnURL
 * @todo    MichelV. <1> move queries to API
 */
function julian_user_updateevent()
{
   if (!xarVarFetch('cancel','str',$cancel,'')) return;
   //If Cancel was pressed, go back to previous page
   if (strcmp($cancel,''))
   {
      $back_link=xarSessionGetVar('lastview');
      xarResponseRedirect($back_link);
   }

   if (!xarVarFetch('id',               'id',       $id,            $id, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('cal_sdow',         'int:0:6',  $cal_sdow,      0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('title',            'str:1:',   $summary,       '')) return;
   if (!xarVarFetch('event_month',      'int:1:',   $event_month,   0)) return;
   if (!xarVarFetch('event_day',        'int',      $event_day,     0)) return;
   if (!xarVarFetch('event_year',       'int',      $event_year,    0)) return;
   if (!xarVarFetch('event_desc',       'str:1:',   $description,   '', XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_allday',     'int:1:',   $event_allday,  0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_starttimeh', 'int',      $event_starttimeh,0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_starttimem', 'int',      $event_starttimem,0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_startampm',  'int::',   $event_startampm,0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_dur_hours',  'int::',   $event_dur_hours,0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_dur_minutes','int::',   $event_dur_minutes,0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('category',         'str:1:',   $category,      '', XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('location',         'str::',   $location,      NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('street1',          'str::',   $street1,       NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('street2',          'str::',   $street2,       NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('city',             'str::',   $city,          NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('state',            'str::',   $state,         NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('postal',           'str::',   $zip,           NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('phone1',           'str::',   $phone1,        NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('phone2',           'str::',   $phone2,        NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('phone3',           'str::',   $phone3,        NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('email',            'str::',   $email,         NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('fee',              'str::',   $fee,           NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('website',          'str::',   $website,       NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('contact',          'str::',   $contact,       NULL, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_repeat_freq_type', 'int:1:', $rrule,     0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_repeat',     'int:1:',   $event_repeat,  0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_endtype',    'int:1:',   $event_endtype, 0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_endmonth',   'int:1:',   $event_endmonth,'', XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_endday',     'int:1:',   $event_endday,  '', XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_endyear',    'int:1:',   $event_endyear, '', XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_repeat_on_day','int:1:', $recur_count,   0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_repeat_on_num','int:1:', $recur_interval,0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_repeat_on_freq','int:1:',$recur_freq1,   0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_repeat_freq','int:1:',   $recur_freq2,   0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('class',            'int:1:',   $class,         0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('share_uids',       'array',    $share_uids,    array(), XARVAR_NOT_REQUIRED)) return;

    if(strcmp($id,"")) {
        if (!xarSecurityCheck('EditJulian', 1, 'Item')) {
            return;
        }
    } else {
    // Event doesn't exist yet. Create one
        if (!xarSecurityCheck('AddJulian', 1, 'Item')) { // TODO: improve
           return;
        }
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

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
   $eventstartdate = $event_year."-".$event_month."-".$event_day;
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
      $ampm = $event_startampm== 1 ? "AM" : "PM";
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
    $xartable =& xarDBGetTables();
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
        $bindvars = array ($event_allday
                        , $contact
                        , $website
                        , $summary
                        , $description
                        , (int) $class
                        , $location
                        , $share
                        , $street1
                        , $street2
                        , $city
                        , $state
                        , $zip
                        , $phone
                        , $email
                        , $fee
                        , $category
                        , $rrule
                        , (int) $recur_freq
                        , $recur_until == '' ? NULL : $recur_until
                        , (int) $recur_count
                        , (int) $recur_interval
                        , $duration
                        , $eventstartdate == '' ? NULL : $eventstartdate
                        , $now);
        $result = $dbconn->Execute($query, $bindvars);

        // Call the hooks. Event already exists (we are just updating)
        $item = array();
        $item['module'] = 'julian';
        $hooks = xarModCallHooks('item', 'update', $id, $item);

    } else {
        // Event doesn't exist yet. Create one
        $uidnow = xarUserGetVar('uid');
        if (!xarSecurityCheck('AddJulian', 1, 'Item', "All:$uidnow:All:All")) { // TODO: improve
            return;
        }
        $query = "INSERT INTO " .  $event_table . " (
                calendar_id,
                isallday,
                organizer,
                contact,
                url,
                summary,
                description,
                class,
                priority,
                status,
                share_uids,
                location,
                street1,
                street2,
                city,
                state,
                zip,
                phone,
                email,
                fee,
                categories,
                rrule,
                recur_freq,
                recur_until,
                recur_count,
                recur_interval,
                duration,
                dtstart,
                transp,
                created
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
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?);";
        $created =date("Y-m-d H:i:s");

        $bindvars = array (0
                          , $event_allday
                          , (int) $uidnow
                          , $contact
                          , $website
                          , $summary
                          , $description
                          , (int) $class
                          , 0
                          , 1
                          , $share
                          , $location
                          , $street1
                          , $street2
                          , $city
                          , $state
                          , $zip
                          , $phone
                          , $email
                          , $fee
                          , $category
                          , $rrule
                          , (int) $recur_freq
                          , $recur_until == '' ? NULL : $recur_until
                          , (int) $recur_count
                          , (int) $recur_interval
                          , $duration
                          , $eventstartdate == '' ? NULL : $eventstartdate
                          , 1
                          , $created);
        $result = $dbconn->Execute($query, $bindvars);
        if (!$result) return;

        /* Get the ID of the item that we inserted. It is possible, depending
         * on your database, that this is different from $nextId as obtained
         * above, so it is better to be safe than sorry in this situation
         */
         // 'serial' is PostGres7 specific
        $id = $dbconn->Insert_ID($event_table, 'event_id', 'serial');

        // Call the hooks. Event is new, we have just created it.
        $item = array();
        $item['module'] = 'julian';
        $hooks = xarModCallHooks('item', 'create', $id, $item);

    }
    //Go back to the view of the event
    xarResponseRedirect(xarModURL('julian', 'user', 'viewevent',array('cal_date'=>$cal_date,'event_id' => $id)));
}

?>
