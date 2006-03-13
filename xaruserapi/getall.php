<?PHP
/**
 * Julian module get all events and place in date array
 *
 * @package modules
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Get all events
 *
 * Get all events from the events table and the linked events table
 * Events are formatted for display
 *
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @author MichelV <michelv@xaraya.com>
 * initial template: Roger Raymond
 *    will return array of events for the date range specified for the current user
 * @param string $startdate valid date as YYYY-MM-DD
 * @param string $enddate valid date as YYYY-MM-DD
 * @access  public
 * @param   int $startnum startnumber
 * @param   int $numitems Max number of items
 * @param   string startdate Starting date to get events for
 * @param   string enddate End date to get events for
 * @param   str $sortby Sortby parameter for display in list
 * @param   orderby sortby catid
 * @return array events for the range specified $event_data
 * @throws  list of exception identifiers which can be thrown
 * @todo    MichelV <#> Move the array formatting to a seperate function so this becomes a real getall
 */

function julian_userapi_getall($args)
{
    // Get arguments
    extract($args);

    // Optional arguments.

    if (!isset($sortby)) {
        $sortby = 'dtstart';
    }

    if (!isset($orderby)) {
        $orderby = 'ASC';
    }

    if (!isset($catid)) {
        $catid = '';
    }
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    if (!isset($startdate)) {
        $startdate = date('Y-m-d');
    } else {
        $startdate = date('Y-m-d',strtotime($startdate));
    }

    if (!isset($enddate)) {
        $yearend = (date('Y') + 1);
        $enddate = $yearend . date('md');
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $event_table = $xartable['julian_events'];
    // load the event class
    $e = xarModAPIFunc('julian','user','factory','event');
    // build a day array
    $day_array = array("1"=>"Sunday","2"=>"Monday","3"=>"Tuesday","4"=>"Wednesday","5"=>"Thursday","6"=>"Friday","7"=>"Saturday");
    // build an array of units that coincides with an interval rule
    $units = array("1"=>"days","2"=>"weeks","3"=>"months","4"=>"years");
    // set the enddate to the day after, so that we can use the SQL compliant "BETwEEN startdate AND enddate"
    // this works as Y-m-d dates are iterpreted as timestamps with 00:00:00 time
    $enddatesql = date('Y-m-d',(strtotime($enddate)+24*60*60));
    $condition = " AND ( (dtstart BETWEEN '" . $startdate . "' AND '" . $enddatesql . "' ) OR recur_freq > 0 ) ";

    $event_data = array();
    // Getting all events that are scheduled for this user,public events for other users, and shared events for this user in
    // the date range specified.
    $current_user=xarUserGetVar('uid');
    $query = "SELECT DISTINCT event_id,
                   created,
                   dtstart,
                   organizer,
                   class,
                   summary,
                   description,
                   categories,
                   isallday,
                   recur_freq,
                   rrule,
                   recur_count,
                   recur_until,
                   recur_interval,
                   share_uids";

    // Select on categories
    if (!empty($catid) && xarModIsHooked('categories','julian')) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('modid' => xarModGetIDFromName('julian'),
                                             'catid' => $catid));
        if (!empty($categoriesdef)) {
            $query .= " FROM ($event_table
                        LEFT JOIN $categoriesdef[table]
                        ON $categoriesdef[field] = event_id )
                        $categoriesdef[more]
                        WHERE $categoriesdef[where]
                        AND ";
        } else {
            $query .= " FROM $event_table
                        WHERE ";
        }
    } else {
        $query .= " FROM $event_table
                    WHERE ";
    }
    /* MySQL friendly query
     * Comment this one if you need to use PostGres
     */

    $query .= " ( $event_table.organizer = $current_user
                OR ($event_table.class= '0' AND $event_table.organizer != '" .$current_user."' )
                OR FIND_IN_SET('".$current_user."',share_uids))
                $condition
                ORDER BY $event_table.$sortby $orderby";

     /* PostGres Query. Uncomment this one to use with PostGres.
      * I could only replace the MySQL-specific FIND_IN_SET() with a (PostgreSQL-specific)
      * feature (arrays). This should be redesigned to use standard SQL features to make sure
      * it will work with various DBs.
      * @author Zsolt

     $query .= " ( $event_table.organizer = $current_user
                 OR ($event_table.class= '0' AND $event_table.organizer != '" .$current_user."' )
                 OR " . $current_user . "=ANY(STRING_TO_ARRAY(share_uids, ','))
                 )
                 $condition
                 ORDER BY $event_table.$sortby $orderby";
      */
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

    if (!$result) return;

    while(!$result->EOF) {
         $eventObj = $result->FetchObject(false);
         $fRecurUntil ='';
         if(!is_null($eventObj->recur_until) || !empty($eventObj->recur_until)) {
             $fRecurUntil = $eventObj->recur_until;
         }
         $fStartDate = date("Y-m-d",strtotime($eventObj->dtstart));
         $fStartTime ='';
         if (!$eventObj->isallday) {
             $fStartTime = date("H:i",strtotime($eventObj->dtstart));
         }
         if (!$eventObj->recur_freq) {
              //this is a non-repeating event and falls in the current date range...add to the events array
              $e->setEventData($event_data,$fStartDate,$eventObj);
         } else {
              //determine if this repeating event ever falls in the current date range
              $nextTS = strtotime($fStartDate);
              $next_date = date("Y-m-d",$nextTS);
              //Keep adding the recurring events until we hit the enddate or the recur_until end date.
              $recur_until = 1;
              //If the db recur_until is set, check to see if we are past the recur_until date, otherwise it's 1 and it will fall through
              if (strcmp($fRecurUntil,"")) {
                  $recur_until = ($nextTS <= strtotime($eventObj->recur_until));
              }
              while($nextTS <= strtotime($enddate) && $recur_until) {
                  //Add the event to the event array if the event is after or on the startdate
                  if ($nextTS >= strtotime($startdate)) {
                     $e->setEventData($event_data, $next_date,$eventObj);
                  }
                  /*calculate when this event would recur next. The loop will determine whether to add the next recur date or not*/
                  if ($eventObj->recur_interval < 5 && $eventObj->recur_interval != 0) {
                     /*event repeats 1st, 2nd, 3rd or 4th day of the week every so many month(s) (i.e. 2nd Sunday every 3 months)*/
                    $newTS = strtotime(date("Y-m",strtotime($next_date))."-01 +"  . $eventObj->recur_freq . " ".$units[$eventObj->rrule]);
                    $next_date = date("Y-m-d",strtotime($eventObj->recur_interval ." ". $day_array[$eventObj->recur_count], $newTS));
                  } else if ($eventObj->recur_interval == 5) {
                     /*event repeats a certain day the last week every so many month(s) (i.e. last Monday every two months)*/
                     $newTS = strtotime(date("Y-m",strtotime($next_date))."-01 +".$eventObj->recur_freq." ".$units[$eventObj->rrule]);
                     $endMonthTS=strtotime(date('Y-m',$newTS)."-".date('t',$newTS));
                     $next_date= date('Y-m-d',strtotime("this " . $day_array[$eventObj->recur_count],strtotime("last week",$endMonthTS)));
                  } else {
                     /*event repeats every so many days, weeks, months or years (i.e. every 2 weeks)*/
                     $next_date=date("Y-m-d",strtotime($next_date." +".$eventObj->recur_freq." ".$units[$eventObj->rrule]));
                  }
                  //Get the next recur date's timestamp
                  $nextTS = strtotime($next_date);
                  //determine if the next recur date should be added
                if (strcmp($fRecurUntil,"")) {
                     $recur_until = ($nextTS <= strtotime($eventObj->recur_until));
                  }
              }
         }
        $result->MoveNext();
    }
    $result->Close();

    // Get the linked events
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $event_linkage_table = $xartable['julian_events_linkage'];
    $query_linked = "SELECT DISTINCT event_id,
                         hook_modid,
                         hook_itemtype,
                         hook_iid,
                         dtstart,
                         summary,
                         duration,
                         isallday,
                         rrule,
                         recur_freq,
                         recur_count,
                         recur_until,
                         recur_interval
                 FROM $event_linkage_table
                 WHERE (1=1) ".$condition."
                 ORDER BY dtstart ASC;";
    $result_linked =& $dbconn->Execute($query_linked);
    if (!$result_linked) return;

    while(!$result_linked->EOF) {
         $eventObj = $result_linked->FetchObject(false);
         $itemlinks = xarModAPIFunc('julian', 'user', 'geteventinfo',
                                     array(//'event'   => $eventObj->event_id,
                                           'iid'     => $eventObj->hook_iid,
                                           'itemtype'=> $eventObj->hook_itemtype,
                                           'modid'   => $eventObj->hook_modid));
         if (!empty($itemlinks['description'])) {
            $fStartDate = date("Y-m-d",strtotime($eventObj->dtstart));
            $fStartTime = date("H:i",strtotime($eventObj->dtstart));
            if (!$eventObj->recur_freq) {
                //this is a non-repeating event and falls in the current date range...add to the events array
                $e->setLinkedEventData($event_data,$fStartDate,$eventObj);
            } else {
                //determine if this repeating event ever falls in the current date range
                $nextTS = strtotime($fStartDate);
                $next_date = date("Y-m-d",$nextTS);
                //Keep adding the recurring events until we hit the enddate or the recur_until end date.
                $recur_until = 1;
                //If the db recur_until is set, check to see if we are past the recur_until date, otherwise it's 1 and it will fall through
                if (!is_null($eventObj->recur_until)) {
                    $recur_until = ($nextTS <= strtotime($eventObj->recur_until));
                }
                while($nextTS <= strtotime($enddate) && $recur_until) {
                      //Add the event to the event array if the event is after or on the startdate
                      if ($nextTS >= strtotime($startdate)) {
                          $e->setLinkedEventData($event_data, $next_date,$eventObj);
                      }
                      //calculate when this event would recur next. The loop will determine whether to add the next recur date or not
                      if ($eventObj->recur_interval < 5 && $eventObj->recur_interval != 0) {
                          // event repeats 1st, 2nd, 3rd or 4th day of the week every so many month(s) (i.e. 2nd Sunday every 3 months)
                          $newTS = strtotime(date("Y-m",strtotime($next_date))."-01 +"  . $eventObj->recur_freq . " ".$units[$eventObj->rrule]);
                          $next_date = date("Y-m-d",strtotime($eventObj->recur_interval ." ". $day_array[$eventObj->recur_count], $newTS));
                      } else if ($eventObj->recur_interval == 5) {
                          // event repeats a certain day the last week every so many month(s) (i.e. last Monday every two months)
                          $newTS = strtotime(date("Y-m",strtotime($next_date))."-01 +".$eventObj->recur_freq." ".$units[$eventObj->rrule]);
                          $endMonthTS=strtotime(date('Y-m',$newTS)."-".date('t',$newTS));
                          $next_date= date('Y-m-d',strtotime("this " . $day_array[$eventObj->recur_count],strtotime("last week",$endMonthTS)));
                      } else {
                          // event repeats every so many days, weeks, months or years (i.e. every 2 weeks)
                          $next_date=date("Y-m-d",strtotime($next_date." +".$eventObj->recur_freq." ".$units[$eventObj->rrule]));
                      }
                      //Get the next recur date's timestamp
                      $nextTS = strtotime($next_date);
                      //determine if the next recur date should be added
                      if (!is_null($eventObj->recur_until)) {
                        $recur_until = ($nextTS <= strtotime($eventObj->recur_until));
                      }
                }    // while recurrence is still within wanted time span
            }    // if the event is recurring
         }
       $result_linked->MoveNext();
    }    // while we have records
    $result_linked->Close();
    return $event_data;
}
?>
