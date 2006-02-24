<?php
/**
 * Julian Module : calendar with events
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module development team
 */
/**
 *  xarEvent
 *  container class for event information
 */
class Event
{
    var $author;            // event author
    var $startdate;         // (YYYYMMDD) event start date
    var $starttime;         // (HHMMSS) event start time
    var $enddate;           // (YYYYMMDD) event end date (if any)
    var $endtime;           // (HHMMSS) event end time (if any)
    var $duration;          // in seconds (-1 for all-day)
    var $repeat;            // type of repeating
    var $repeatfreq;        // frequency of the repeating
    var $repeattype;        // frequency type
    var $repeatonnum;       // repeat on 1st,2nd,3rd,4th,Last
    var $repeatonday;       //
    var $color;

    function Event()
    {   // set the author
        //$this->author = xarUserGetVar('uid');
    }

    function buildEvent(&$id)
    {
        return true;
    }

    /**
     *  appends data to the block layout array.
     *
     *  @param array $bl_data the block layout data array
     */
    function getEventDataForBL(&$bl_data)
    {
        $bl_data['event_id']            = 0;
        $bl_data['calendar_id']         = 'default';
        $bl_data['event_owner']         = 0;
        $bl_data['event_start_date']    = 'YYYYMMDD';
        $bl_data['event_start_time']    = 'HHMMSS';
        $bl_data['event_end_date']      = 'YYYYMMDD';
        $bl_data['event_end_time']      = 'HHMMSS';
        $bl_data['event_title']         = 'title text';
        $bl_data['event_description']   = 'event description';
        // event recurrence rule
        $bl_data['event_rrule_'] = null;
        // event exceptions
        $bl_data['event_exdate'] = null;
        $bl_data['event_exrrule_'] = null;
    }
    /**
    * This function takes an event_date, the day to set the event for,
    * an event_obj which holds the data to be assigned, and an event array where the data
    * is being set (passed-by-reference). The event data is set in the event array
    *
    * initial template: Roger Raymond
    * @author Jodie Razdrh/John Kevlin/David St.Clair
    * @return
    */
    function setEventData(&$event_data,$event_date,$event_obj)
    {
    //Get variables
        $dateformat=xarModGetVar('julian', 'dateformat');

        /**
         * create a unique index for sorting using the timestamp for this
         * event and then concat the event id with a dash inbetween to guarantee uniqueness. This will allow ordering
         * by timestamp then event_id with the all day events being listed first. If there
         * is more than one event for any given time (or all day), the
         * event id makes the index unique.
         */
        $index=strtotime($event_obj->dtstart) ."-".$event_obj->event_id;

        // Default color: black.
        $color = "#000000";

        // Sad and slow: we need to do database lookups, first to find the category of the event
        // (in table categories_linkage, done by categories_userapi_getlinks), then to find the color
        // of the category in table julian_category_properties... *sigh*

        // Get categories belonging to this event (via categories_user_getlinks).
        $links = xarModAPIFunc('categories', 'user', 'getlinks',
                                       array('iids' => array($event_obj->event_id),
                                             'itemtype' => '',
                                             'modid' => xarModGetIDFromName('julian'),
                                             'reverse' => 0));

        if (!empty($links) && is_array($links) && count($links) > 0) {
            // One or more categories are coupled to this event; try to find corresponding colors.
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $julian_category_properties = $xartable['julian_category_properties'];
            $cids = implode(",", array_keys($links)); // The category-ids we want colors for.
             $query_color = "SELECT color FROM $julian_category_properties WHERE cid IN ($cids)";
               $result_color = $dbconn->Execute($query_color);
            if ($result_color && !$result_color->EOF)    $color = $result_color->fields[0];    // we found at least one color; use it.
        }

        //Set the data for the event
        $event_data[$event_date][$index]['event_id'] = $event_obj->event_id;
        $event_data[$event_date][$index]['class'] = $event_obj->class;
        $event_data[$event_date][$index]['organizer'] = $event_obj->organizer;
        $event_data[$event_date][$index]['summary'] = $event_obj->summary;
        $event_data[$event_date][$index]['description'] = $event_obj->description;
        $event_data[$event_date][$index]['isallday'] = $event_obj->isallday;
        $event_data[$event_date][$index]['color'] = $color;
        $event_data[$event_date][$index]['time'] = $event_obj->fStartTime;
        $event_data[$event_date][$index]['categories'] = $event_obj->categories;
        $event_data[$event_date][$index]['linkdate'] = date("Ymd",strtotime($event_date));
        $event_data[$event_date][$index]['startdate'] = date("m-d-Y",strtotime($event_date));

        // Make an admin adjustable time format
        $dateformat=xarModGetVar('julian', 'dateformat');
        $timeformat=xarModGetVar('julian', 'timeformat');
        $dateformat_created=$dateformat.' '.$timeformat;
        $datecreated = date("$dateformat_created",strtotime($event_obj->created));
        //$bl_data['datecreated'] = xarLocaleFormatDate($bl_data['datecreated'], $dateformat_created);

        //popover posted information
        $postedby=xarML('Posted By').': '.addslashes(xarUserGetVar('name',$event_obj->organizer));
        $postedon=xarML('on').' '.$datecreated;//date($dateformat).' '.date('h:s a',strtotime($event_obj->created));
        //multiple event in a day popup
        $event_data[$event_date][$index]['multipopover'] = $event_obj->fStartTime . " " . addslashes($event_obj->summary) . "-" .$postedby;
        //single event popup
        $event_data[$event_date][$index]['singlepopover'] = addslashes($event_obj->description) . "<br>".$postedby.' '.$postedon;
        //$event_data[$event_date][$index]['singlepopover'] = addslashes($event_obj->description);
        $event_data[$event_date][$index]['singlepopovercaption'] = addslashes($event_obj->summary)." " . $event_obj->fStartTime;
        //sort the array for this event date by the unique timestamp index which is the key for this array
        $sortArray=$event_data[$event_date];
        ksort($sortArray);
        //reset the event array for this date to the sorted array for this event date
        $event_data[$event_date]=$sortArray;
    }
    /**
    * This function takes an event_date, the day to set the event for,
    * an event_obj which holds the data to be assigned, and an event array where the data
    * is being set (passed-by-reference). The event data is set in the event array
    * Events are taken from the hooked events
    *
    * initial template: Roger Raymond
    * @author Jodie Razdrh/John Kevlin/David St.Clair
    * @return
    */
    function setLinkedEventData(&$event_data,$event_date,$event_obj)
    {
      //Get variables
      $dateformat=xarModGetVar('julian', 'dateformat');

      $event_obj->event_id .= "_link";

      // Generate unique id for event that allows for time/date-based sorting.
      $index=strtotime($event_obj->dtstart) ."-".$event_obj->event_id;

      $event_data[$event_date][$index] = array();
      $event_data[$event_date][$index] = xarModAPIFunc('julian', 'user', 'geteventinfo',
                                                         array('event'   => $event_data[$event_date][$index],
                                                               'iid'     => $event_obj->hook_iid,
                                                               'itemtype'=> $event_obj->hook_itemtype,
                                                               'modid'   => $event_obj->hook_modid));


      // Default color: black.
      $color = "#000000";

      //Set the data for the event
      $event_data[$event_date][$index]['event_id'] = $event_obj->event_id;
      $event_data[$event_date][$index]['class'] = 0;
      $event_data[$event_date][$index]['organizer'] = 0;
      $event_data[$event_date][$index]['summary'] = $event_obj->summary;
 //     $event_data[$event_date][$index]['description'] = xarML('This item does not belong to julian. It has been hooked to julian by another module.');
      $event_data[$event_date][$index]['isallday'] = $event_obj->isallday;
      $event_data[$event_date][$index]['color'] = $color;
      $event_data[$event_date][$index]['time'] = $event_obj->fStartTime;
      $event_data[$event_date][$index]['categories'] = '';
      $event_data[$event_date][$index]['linkdate'] = date("Ymd",strtotime($event_date));
      $event_data[$event_date][$index]['startdate'] = date("m-d-Y",strtotime($event_date));
      // Get additional event information for hooking module.

      //popover posted information
      //multiple event in a day popup
      $event_data[$event_date][$index]['multipopover'] = $event_obj->fStartTime . " " . addslashes($event_data[$event_date][$index]['summary']);
      //single event popup
      $event_data[$event_date][$index]['singlepopover'] = addslashes($event_data[$event_date][$index]['description']);
      $event_data[$event_date][$index]['singlepopovercaption'] = addslashes($event_data[$event_date][$index]['summary'])." " . $event_obj->fStartTime;

      //sort the array for this event date by the unique timestamp index which is the key for this array
      $sortArray=$event_data[$event_date];
      ksort($sortArray);
      //reset the event array for this date to the sorted array for this event date
      $event_data[$event_date]=$sortArray;
    }

    function setStartTime($time)
    {
        $this->starttime = $time;
    }

    function setEndTime($time)
    {
        $this->endtime = $time;
    }

    function setStartDate($date)
    {
        $this->startdate = $date;
    }

    function setEndDate($date)
    {
        $this->enddate = $date;
    }

    function setDuration($days,$hours,$minutes)
    {
        $seconds = (int) ($days * 24 * 60 * 60) ;
        $seconds += (int) ($hours * 60 * 60) ;
        $seconds += (int) ($minutes * 60) ;
        $this->duration = $seconds;
    }

    function setRepeat($repeat)
    {
        $this->repeat = $repeat;
    }

    function setRepeatFreq($freq)
    {
        $this->repeatfreq = $freq;
    }

    function setRepeatType($type)
    {
        $this->repeattype = $type;
    }

    function setRepeatOnNum($on)
    {
        $this->repeatonnum = $on;
    }

    function setRepeatOnDay($day)
    {
        $this->repeatonday = $day;
    }
    /**
     * This function determines the start date of a recurring event based on the selected start date by the user and
     * the recurring information. The start date is returned as a string in the format of YYYY-mm-dd
     * @param int $count the number of....
     * @return string startdate
     */
    function setRecurEventStartDate($selectedstartdate,$interval,$count,$freq)
    {
       $eventstartdate='';
       $haveStartDate=0;
       //build a day array
       $day_array = array("1"=>"Sunday","2"=>"Monday","3"=>"Tuesday","4"=>"Wednesday","5"=>"Thursday","6"=>"Friday","7"=>"Saturday");
       //initialize the timestamp with the first day of the start month and year
       $newTS=strtotime(date("Y-m",strtotime($selectedstartdate))."-01");

       while(!$haveStartDate)
       {
          /* calculate when this event would occur first. Check the current month first and if this event would occur
            in the current month sometime after the start date, set the start date to the current month's first occurence.
            Otherwise, add the frequency to get the next occurrence of this event until one is found that occurs after the start date
            entered by the user.
            */
          if ($interval < 5 && $interval != 0) {
             /* event repeats 1st, 2nd, 3rd or 4th day of the week every so many month(s) (i.e. 2nd Sunday every 3 months)*/
             $newTS = strtotime($interval ." ". $day_array[$count], $newTS);
             if($newTS >= strtotime($selectedstartdate)) {
                $eventstartdate=date("Y-m-d",$newTS);
                $haveStartDate=1;
             } else {
                $newTS = strtotime(date("Y-m",$newTS)."-01 +" . $freq . " month");
             }
          } else if ($interval == 5) {
             /*event repeats a certain day the last week every so many month(s) (i.e. last Monday every two months)*/
             $endMonthTS=strtotime(date('Y-m',$newTS)."-".date('t',$newTS));
             $newTS= strtotime("next " . $day_array[$count],strtotime("last week",$endMonthTS));
             /* if determining the last occurrence in the month takes you to the next month, use 'this' instead of 'next
             in the strtotime function to create the next date for comparison. This is required for this to calculate
             the correct date when the last occurrence is on the last day of the month
             */
             if(strcmp(date('m',$newTS),date('m'))) {
                $newTS=strtotime("this " . $day_array[$count],strtotime("last week",$endMonthTS));
             }
             if($newTS >= strtotime($selectedstartdate)) {
                $eventstartdate=date("Y-m-d",$newTS);
                $haveStartDate=1;
             } else {
                $newTS = strtotime(date("Y-m",$newTS)."-01 +".$freq." month");
             }
          }
       }
       return $eventstartdate;
    }
}
?>
