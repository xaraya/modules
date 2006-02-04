<?php

/*
 * This function exports an event to a ics (iCalendar) file.  It echos out the file with the mime type so
 * the browser knows what to do with the file.  The exit at the end keeps Xaraya from going to a template.
 *
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

/**
 * @todo change to use get.php function
 * @todo improve the security on this file
 */
function julian_user_export()
{
  if (!xarVarFetch('event_id','str', $event_id)) return;

   if (!xarSecurityCheck('ReadJulian', 1, 'Item', "$event_id:All:All:All")) {
       return;
   }

  // Load up database
  $dbconn = xarDBGetConn();
  //get db tables
  $xartable = xarDBGetTables();
  //set events table
  $event_table = $xartable['julian_events'];

  $sql = "SELECT *,if(recur_until LIKE '0000%','',recur_until) AS recur_until FROM " . $event_table . " WHERE event_id='".$event_id."';";
  $rs = $dbconn->Execute($sql);
  $eventObj=$rs->FetchObject(false);

  header('Content-Type: text/calendar');
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  header("Content-Disposition: attachment; filename=event".$event_id.".ics");

  //initialize the event start/end dates
  $dtstart = "VALUE=DATE:".date("Ymd",strtotime($eventObj->dtstart));
  $dtend = "VALUE=DATE:".date("Ymd",strtotime("+1 day",strtotime($eventObj->dtstart)));
  if(!$eventObj->isallday)//if this is not an all day event, add the duration of the event to the start/end dates
  {
      //Calculating the end date/time based on the duration of the event
      $hours=$minutes='00';
      if(strcmp($eventObj->duration,""))
         list($hours,$minutes) = explode(":",$eventObj->duration);
      $dtstart = "TZID=America/New_York:".date("Ymd\THi00",strtotime($eventObj->dtstart));
      $dtend = "TZID=America/New_York:".date("Ymd\THi00",strtotime("+".$hours." hours ".$minutes." minutes ", strtotime($eventObj->dtstart)));
  }

  //Building rrule if set
  $rrule = '';
  //Outlook will crash if the order of the string is changed.
  //If event is repeating
  if ($eventObj->recur_freq)
  {
    //Arrays translate database values to ical values
    $freq = array("1"=>"DAILY","2"=>"WEEKLY","3"=>"MONTHLY","4"=>"YEARLY");
    $byday = array("1"=>"SU","2"=>"MO","3"=>"TU","4"=>"WE","5"=>"TH","6"=>"FR","7"=>"SA");
    //Start building recuring rule string
    $rrule = "RRULE:FREQ=".$freq[$eventObj->rrule];
    //Set end date
    if (strcmp($eventObj->recur_until,""))
      $rrule .=";UNTIL=".date("Ymd\T000000\Z",strtotime($eventObj->recur_until));
    //Set how often
    $rrule .= ";INTERVAL=".$eventObj->recur_freq;
    if ($eventObj->recur_count)
    {
      $interval = $eventObj->recur_interval;
      //Last is -1
      if ($interval == 5)
        $interval = "-1";
      $rrule .= ";BYDAY=".$byday[$eventObj->recur_count].";BYSETPOS=".$interval;
    }
    $rrule .= ";WKST=SU\r\n";
  }

  //This file is required to be in the format below.  The dates are in the format of "Year Month Day T Hour(24h) Minutes Seconds"
  //The T seperates the date from the time.
  echo "BEGIN:VCALENDAR\r\n
  CALSCALE:GREGORIAN\r\n
  PRODID:-//PHP/" . phpversion()."//EN\r\n
  VERSION:2.0\r\n
  METHOD:PUBLISH\r\n
  BEGIN:VTIMEZONE\r\n
  TZID:America/New_York\r\n
  X-LIC-LOCATION:America/New_York\r\n
  BEGIN:STANDARD\r\n
  TZOFFSETFROM:-0400\r\n
  TZOFFSETTO:-0500\r\n
  TZNAME:EST\r\n
  DTSTART:20031026T020000\r\n
  RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10\r\n
  END:STANDARD\r\n
  BEGIN:DAYLIGHT\r\n
  TZOFFSETFROM:-0500\r\n
  TZOFFSETTO:-0400\r\n
  TZNAME:EDT\r\n
  DTSTART:20040404T020000\r\n
  RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=4\r\n
  END:DAYLIGHT\r\n
  END:VTIMEZONE\r\n
  BEGIN:VEVENT\r\n
  UID:".date("Ymd\THi00").md5(uniqid(mt_rand(), 1))."\r\n
  DTSTAMP:".date("Ymd\THi00Z")."\r\n
  DTSTART;".$dtstart."\r\n
  DTEND;".$dtend."\r\n
  SEQUENCE:3\r\n
  SUMMARY:".$eventObj->summary."\r\n
  LOCATION:".$eventObj->location."\r\n
  CLASS:PUBLIC\r\n
  ".$rrule."
  TRANSP:OPAQUE\r\n
  DESCRIPTION:".$eventObj->description."\r\n
  ORGANIZER;CN=".xarUserGetVar('name',$eventObj->organizer).":MAILTO:".xarUserGetVar('email',$eventObj->organizer)."\r\n
  END:VEVENT\r\n
  END:VCALENDAR\r\n";
  exit;
}

?>
