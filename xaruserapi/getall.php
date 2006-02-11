<?PHP
/**
 *    will return array of events for the date range specified for the current user
 *    @params $startdate string valid date as YYYY-MM-DD
 *    @params $enddate string valid date as YYYY-MM-DD
 *    @return array events for the range specified
 * @package modules
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 *
 */

/**
 * Get all events
 *
 * Get all events from the events table and the linked events table
 * Events are formatted for display
 *
 * @author Jodie Razdrh/John Kevlin/David St.Clair MichelV (Michelv@xarayahosting.nl)
 * initial template: Roger Raymond

 * @access  public / private / protected
 * @param   int $startnum Description of parameter 1
 * @param   int $numitems Description of parameter 2
 * @param   str $sortby Sortby parameter for display in list
 * @param   date $startdate The starting date for the selection
 * @param   date $enddate The end date for the selection
 * @param orderby sortby catid
 * @return  array $event_data
 * @throws  list of exception identifiers which can be thrown
 * @todo    Michel V. <#> make userapi_getall.php from this.
 */

function julian_userapi_getall($args)
{
    // Get arguments
    extract($args);
    if (!xarVarFetch('startdate','isset',  $startdate, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enddate',  'isset',  $enddate, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid',    'int:1:', $catid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:', $numitems, '-1', XARVAR_NOT_REQUIRED)) return;

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

    if (!isset($startdate)) {
        $startdate = date('Ymd');
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
    $day_array = array("1"=>xarML('Sunday'),"2"=>xarML('Monday'),"3"=>xarML('Tuesday'),"4"=>xarML('Wednesday'),"5"=>xarML('Thursday'),"6"=>xarML('Friday'),"7"=>xarML('Saturday'));
    // build an array of units that coincides with an interval rule
    $units = array("1"=>xarML('days'),"2"=>xarML('weeks'),"3"=>xarML('months'),"4"=>xarML('years'));
    $startdate=date('Y-m-d',strtotime($startdate));
    if(strcmp($enddate,"")) {
        $enddate=date('Y-m-d',strtotime($enddate));
        $condition=" AND ((DATE_FORMAT(dtstart,'%Y-%m-%d')>='" . $startdate . "' AND DATE_FORMAT(dtstart,'%Y-%m-%d') <='" . $enddate . "') OR recur_freq>0) ";
    } else {
        $condition = " AND (DATE_FORMAT(dtstart,'%Y-%m-%d') ='". $startdate  ."' OR recur_freq>0)";
        // set the end date to the start date for recurring events
        $enddate=$startdate;
    }

    $event_data = array();
    // Getting all events that are scheduled for this user,public events for other users, and shared events for this user in
    // the date range specified.
    // TODO Bug 5190 rewrite the if clause in here
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
                   if(recur_until LIKE '0000%','',recur_until) as fRecurUntil,
                   recur_interval,
                   if(isallday,'',DATE_FORMAT(dtstart,'%l:%i %p')) as fStartTime,
                   DATE_FORMAT(dtstart,'%Y-%m-%d') as fStartDate";

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

     $query .= " ( $event_table.organizer = $current_user
                 OR ($event_table.class= '0' AND $event_table.organizer != '" .$current_user."' )
                 OR FIND_IN_SET('" . $current_user."',share_uids) )
                 $condition
                 ORDER BY $event_table.$sortby $orderby";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

    if (!$result) return;

    while(!$result->EOF) {
         $eventObj = $result->FetchObject(false);
         if (!$eventObj->recur_freq) {
            //this is a non-repeating event and falls in the current date range...add to the events array
            $e->setEventData($event_data,$eventObj->fStartDate,$eventObj);
         } else {
            //determine if this repeating event ever falls in the current date range
            $nextTS = strtotime($eventObj->fStartDate);
            $next_date = date("Y-m-d",$nextTS);
            //Keep adding the recurring events until we hit the enddate or the recur_until end date.
            $recur_until = 1;
            //If the db recur_until is set, check to see if we are past the recur_until date, otherwise it's 1 and it will fall through
            if (strcmp($eventObj->fRecurUntil,"")) {
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
                if (strcmp($eventObj->fRecurUntil,"")) {
                $recur_until = ($nextTS <= strtotime($eventObj->recur_until));
                }
         }
    }
    $result->MoveNext();
}
    $result->Close();


    // Get the linked events
    // TODO Bug 5190 rewrite the if clause in here
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $event_linkage_table = $xartable['julian_events_linkage'];
    $query_linked = "SELECT DISTINCT event_id,
                         hook_modid,
                         hook_itemtype,
                         hook_iid,
                         summary,
                         dtstart,
                         duration,
                         isallday,
                         rrule,
                         recur_freq,
                         recur_count,
                         recur_until,
                         if(recur_until LIKE '0000%','',recur_until) as fRecurUntil,
                         recur_interval,
                         if(isallday,'',DATE_FORMAT(dtstart,'%l:%i %p')) as fStartTime,
                         DATE_FORMAT(dtstart,'%Y-%m-%d') as fStartDate
                 FROM $event_linkage_table
                 WHERE (1) ".$condition."
                 ORDER BY dtstart ASC;";
    $result_linked =& $dbconn->Execute($query_linked);
    if (!$result_linked) return;

    while(!$result_linked->EOF) {
     $eventObj = $result_linked->FetchObject(false);
        if (!$eventObj->recur_freq) {
            //this is a non-repeating event and falls in the current date range...add to the events array
            $e->setLinkedEventData($event_data,$eventObj->fStartDate,$eventObj);
        } else {
            //determine if this repeating event ever falls in the current date range
            $nextTS = strtotime($eventObj->fStartDate);
            $next_date = date("Y-m-d",$nextTS);
            //Keep adding the recurring events until we hit the enddate or the recur_until end date.
            $recur_until = 1;
            //If the db recur_until is set, check to see if we are past the recur_until date, otherwise it's 1 and it will fall through
            if (strcmp($eventObj->fRecurUntil,"")) {
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
                  if (strcmp($eventObj->fRecurUntil,"")) {
                    $recur_until = ($nextTS <= strtotime($eventObj->recur_until));
                  }
            }    // while recurrence is still within wanted time span
        }    // if the event is recurring

       $result_linked->MoveNext();
    }    // while we have records
    $result_linked->Close();
    return $event_data;
}
?>
