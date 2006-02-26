<?php
/**
 * Displays an event
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Display an event
 *
 * This function prepares a single event to be displayed. When the event is hooked,
 * the user is directed to the hooked event and no preparation is performed.
 *
 * initial template: Roger Raymond
 * @link http://www.metrostat.net
 * @copyright (C) 2005 by Metrostat Technologies, Inc.
 * @author  Jodie Razdrh/John Kevlin/David St.Clair
 * @author  MichelV. <michelv@xaraya.com>
 * @access  public
 * @param   ID $event_id for the event to display
 * @param   $cal_date
 * @return  array
 * @todo    MichelV. <1> Make this the display function and move queries to API
 *                   <2> Improve ML settings
 */
function julian_user_viewevent()
{
    //get post/get vars
    if (!xarVarFetch('event_id','isset',$event_id)) return; // can't be id, because of _link
    if (!xarVarFetch('cal_date','int',$cal_date)) return; // str here?

    // Security check

    if (!xarSecurityCheck('ReadJulian', 1, 'Item', "$event_id:All:All:All")) { // TODO: improve
        return;
    }
    // TODO: make this an API for linked event
    // establish a db connection
    $dbconn =& xarDBGetConn();
    // get db tables
    $xartable =& xarDBGetTables();

    $matches = array();
    if (preg_match("/^(\d+)_link$/",$event_id,$matches)) {
        $linkid = $matches[1];
        $query = "SELECT  hook_modid, hook_itemtype, hook_iid  FROM  ".$xartable['julian_events_linkage']."  WHERE event_id=?";
        $result = &$dbconn->Execute($query,array($linkid));
/*
        'event_id'=>array('type'=>'integer','size'=>'medium','unsigned'=>TRUE,'null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'hook_modid'   =>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'hook_itemtype'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'hook_iid'     =>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'dtstart'=>array('type'=>'datetime','size'=>'','null'=>FALSE),// Bug 4942 removed ,'default'=>''
        'duration'=>array('type'=>'varchar','size'=>'50','null'=>TRUE),
        'isallday'=>array('type'=>'integer','size'=>'tiny','default'=>'0'),
        'rrule'=>array('type'=>'text','null'=>TRUE),
        'recur_freq'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
        'recur_count'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
        'recur_interval'=>array('type'=>'integer','null'=>TRUE,'default'=>'0'),
        'recur_until'=>array('type'=>'datetime','size'=>'','null'=>FALSE),// Bug 4942 removed ,'default'=>''

*/
        if (!$result) return;
        /* Check for no rows found, and if so, close the result set and return an exception */
        if ($result->EOF) {
            $result->Close();
            $msg = xarML('This item does not exist');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
            return;
        } else {
            list($hook_modid, $hook_itemtype, $hook_iid) = $result->fields;
            $result->Close();
            $event = array();
            $event = xarModAPIFunc('julian', 'user', 'geteventinfo', array('event'=>$event,
                                                                           'modid'=>$hook_modid,
                                                                           'iid'=>$hook_iid,
                                                                           'itemtype'=>$hook_itemtype));
            return xarResponseRedirect($event['viewURL']);
        }
    }

   // Get the event, as it is not in the linked table
   // We use event_id here
   $bl_data = array();
   $bl_data = xarModAPIFunc('julian','user','get',array('event_id'=>$event_id));
   // Security check
   if (!xarSecurityCheck('ReadJulian', 1, 'Item', "$event_id:$bl_data[organizer]:$bl_data[calendar_id]:All")) {
        return;
   }

   // Make an admin adjustable time format
   $dateformat=xarModGetVar('julian', 'dateformat');
   $timeformat=xarModGetVar('julian', 'timeformat');
   $dateformat_created=$dateformat.' '.$timeformat;

    // Don't like this here
    if (!isset($bl_data['recur_until']) || is_numeric($bl_data['recur_until']) || strpos($bl_data['recur_until'], '0000')!== false) {
        $bl_data['recur_until'] = 'recur_until';
    }

   $bl_data['id'] = $bl_data['event_id'];
   $bl_data['deletesummary'] = xarVarPrepForDisplay($bl_data['summary']);

   // TODO: MichelV: improve ML settings here
   // created = yyyy-mm-dd hh:mm:ss
   //$bl_data['datecreated'] = xarLocaleGetFormattedDate($bl_data['created']);
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
     $duration="&#160;".xarML('from')."&#160;".date("g:i A",strtotime($bl_data['dtstart']))."&#160;".xarML('to')."&#160;".date("g:i A",strtotime("+".$hours." hours ".$minutes." minutes",strtotime($bl_data['dtstart'])));
   }
   //Checking if we are viewing a reoccuring event
   if ($bl_data['recur_freq']) {
      $recur_count = $bl_data['recur_count'];
      $rrule = $bl_data['rrule'];
      $recur_interval = $bl_data['recur_interval'];
      $intervals = array("1"=>xarML('Day(s)'),"2"=>xarML('Week(s)'),"3"=>xarML('Month(s)'),"4"=>xarML('Year(s)'));
      $day_array = array("1"=>xarML('Sunday'),"2"=>xarML('Monday'),"3"=>xarML('Tuesday'),"4"=>xarML('Wednesday'),"5"=>xarML('Thursday'),"6"=>xarML('Friday'),"7"=>xarML('Saturday'));
      //build the effective date string
      $eff ="&#160;".xarML('effective')."&#160;".date("$dateformat",strtotime($bl_data['dtstart']));
      //start the time string
      $time = xarML('Occurs ');
      //Build the strings to describe the repeating event.
      if (!$bl_data['recur_count']) {
         //this is for the 'every' recurring event type
         $time .= xarML('every')."&#160;".$bl_data['recur_freq']." ".$intervals[$rrule]."&#160;".xarML('on')."&#160;".date('l',strtotime($bl_data['dtstart'])) . $eff;
      } else {
         // build a day array
         $weektimes = array("1"=>xarML('First'),"2"=>xarML('Second'),"3"=>xarML('Third'),"4"=>xarML('Fourth'),"5"=>xarML('Last'));
         // this is for the 'on every' recurring event type
         $time .= xarML('the')."&#160;".$weektimes[$recur_interval] ."&#160;".$day_array[$recur_count]."&#160;".xarML('every')."&#160;".$bl_data['recur_freq']." ". $intervals[$rrule] . $eff;
      }

      //add the end date if one exists
      //TODO: MichelV move this to template
      if (strcmp($bl_data['recur_until'],"") && strcmp($bl_data['recur_until'],"recur_until")){
         $time .= "&#160;".xarML('until ')."&#160;".date("$dateformat",strtotime($bl_data['recur_until']));
      }
     //if the duration has not been set and this is not an all day event, add the start time to the string
     $duration=strcmp($duration,"")?$duration:($bl_data['isallday']?'':"&#160;".xarML('at')."&#160;".date("g:i A",strtotime($bl_data['dtstart'])));
     $bl_data['time'] = $time.$duration .".";

   // If there is no duration and this is not an all day event, show the time at the front.
   } else if (!$bl_data['isallday'] && !strcmp($duration,'')) {
      $bl_data['time'] = date("g:i A l, $dateformat",strtotime($bl_data['dtstart']));
   } else {
      $bl_data['time'] = date("l, $dateformat",strtotime($bl_data['dtstart'])).$duration;
   }
   $bl_data['cal_date']=$cal_date;

   // Set the url to this page in session as the last page viewed
   $lastview=xarModURL('julian','user','viewevent',array('cal_date'=>$cal_date,'event_id'=>$event_id));
   xarSessionSetVar('lastview',$lastview);

   $uid = xarUserGetVar('uid');
   // Priv checks. We add a AddJulian here because we need to check on the own events, not of others
   if (xarSecurityCheck('EditJulian', 0, 'Item', "$event_id:$bl_data[organizer]:$bl_data[calendar_id]:All")) {
       // Add edit link
       $bl_data['editlink'] = xarModURL('julian','user','edit',array('cal_date'=>$cal_date,'id'=> $event_id));
   } else {
       $bl_data['editlink'] = '';
   }
   if (xarSecurityCheck('DeleteJulian', 0, 'Item', "$event_id:$bl_data[organizer]:$bl_data[calendar_id]:All")) {
       // Add delete link
       $bl_data['deletelink'] = xarModURL('julian','admin','deleteevent',array('cal_date'=>$cal_date,'event_id'=> $event_id, 'authid' =>$bl_data['authid']));
   } else {
       $bl_data['deletelink'] = '';
   }
   // This doesn't work yet.
    $item = $bl_data;
    $item['returnurl'] = $lastview;
    $item['module'] = 'julian';
    $item['itemtype'] = NULL;
    $hooks = xarModCallHooks('item','display', $event_id, $item);
    if (empty($hooks)) {
        $bl_data['hookoutput'] = array();
    } else {
        /* You can use the output from individual hooks in your template too, e.g. with
         * $hookoutput['comments'], $hookoutput['hitcount'], $hookoutput['ratings'] etc.
         */
        $bl_data['hookoutput'] = $hooks;
    }
   $bl_data['organizer'] = xarUserGetVar('name',$bl_data['organizer']);
   return $bl_data;
}
?>
