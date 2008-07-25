<?php
/**
 * Displays an event
 *
 * @package modules
 * @copyright (C) 2005-2008 The Digital Development Foundation
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
    // Get post/get vars
    if (!xarVarFetch('event_id', 'isset', $event_id)) return; // can't be id, because of _link
    if (!xarVarFetch('cal_date', 'int', $cal_date, date('dmY'), XARVAR_NOT_REQUIRED)) return; // str here?

    // Security check
    if (is_numeric($event_id)) {
        if (!xarSecurityCheck('ReadJulian', 1, 'Item', "$event_id:All:All:All")) return;
    } else {
        if (!xarSecurityCheck('ReadJulian', 1)) return;
    }

    // Establish a db connection
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $matches = array();
    if (preg_match("/^(\d+)_link$/",$event_id,$matches)) {
        $linkid = $matches[1];
        $query = "SELECT hook_modid, hook_itemtype, hook_iid  FROM  " . $xartable['julian_events_linkage'] . "  WHERE event_id=?";
        $result = &$dbconn->Execute($query, array($linkid));

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
            $event = xarModAPIFunc('julian', 'user', 'geteventinfo',
                array('event' => $event, 'modid' => $hook_modid, 'iid' => $hook_iid, 'itemtype' => $hook_itemtype)
            );
            return xarResponseRedirect($event['viewURL']);
        }
    }

    // Get the event, as it is not in the linked table
    // We use event_id here
    /*
    The event array consists of:
                'eID' => $eID,
                'eName' => $eName,
                'eDescription' => $eDescription,
                'eStreet1' => $eStreet1,
                'eStreet2' => $eStreet2,
                'eCity' => $eCity,
                'eState' => $eState,
                'eZip' => $eZip,
                'eEmail' => $eEmail,
                'ePhone' => $ePhone,
                'eLocation' => $eLocation,
                'eUrl' => $eUrl,
                'eContact' => $eContact,
                'eOrganizer' => $eOrganizer,
                'eStart' => $eStart,
                'eEnd' => $eEnd,
                'i_moduleid' => '',
                'i_itemtype' => '',
                'i_itemid' => '',
                'i_DateTime' => strtotime($eStart['timestamp']),
                'eRecur' => $eRecur,
                'eDuration' => $eDuration,
                'eDurationHours' => $eDurationHours,
                'eRrule' => $eRrule,
                'eIsallday' => $eIsallday,
                'eFee' => $eFee,
                // Migrated from get(), in preparation for merging.
                'event_id' =>$eID,
                'calendar_id' =>$eCalendarID,
                'type' => $eType,
                'organizer' => $eOrganizer,
                'contact' => $eContact,
                'url' => $eUrl,
                'summary' => $eName,
                'description' => $eDescription,
                'related_to' => $eRelatedTo,
                'reltype' => $eReltype,
                'class' => $eClass,
                'share_uids' => $eShareUIDs,
                'priority' => $ePriority,
                'status' => $eStatus,
                'location' => $eLocation,
                'street1' => $eStreet1,
                'street2' => $eStreet2,
                'city' => $eCity,
                'state' => $eState,
                'zip' => $eZip,
                'phone' => $ePhone,
                'email' => $eEmail,
                'fee' => $eFee,
                'exdate' => $eExdate,
                'categories' => $eCategories,
                'recur_freq' => $eRecurFreq,
                'recur_until' => $eRecur,
                'recur_count' => $eRecurCount,
                'recur_interval' => $eRecurInterval,
                'dtstart' => $eStart,
                'dtend' => $eDtend,
                'duration' => $eDuration,
                'freebusy' => $eFreebusy,
                'due' => $eDue,
                'transp' => $eTransp,
                'created' => $eCreated,
                'last_modified' => $eLastModified


    */



    $event = xarModAPIFunc('julian', 'user', 'get', array('event_id'=>$event_id));
    $bl_data = array();
    // Put all vars into the template array, and sanitize meanwhile

             $bl_data['eID'] = $event['eID'];
             $bl_data['eName'] = xarVarPrepForDisplay($event['eName']);
             $bl_data['eDescription'] = xarVarPrepForDisplay($event['eDescription']);
             $bl_data['eStreet1'] = xarVarPrepForDisplay($event['eStreet1']);
             $bl_data['eStreet2'] = xarVarPrepForDisplay($event['eStreet2']);
             $bl_data['eCity'] = xarVarPrepForDisplay($event['eCity']);
             $bl_data['eState'] = xarVarPrepForDisplay($event['eState']);
             $bl_data['eZip'] = xarVarPrepForDisplay($event['eZip']);
             $bl_data['eEmail'] = xarVarPrepEmailDisplay($event['eEmail']);
             $bl_data['ePhone'] = xarVarPrepForDisplay($event['ePhone']);
             $bl_data['eLocation'] = xarVarPrepForDisplay($event['eLocation']);
             $bl_data['eUrl'] = $event['eUrl'];
             $bl_data['eContact'] = xarVarPrepForDisplay($event['eContact']);
             $bl_data['eOrganizer'] = xarVarPrepForDisplay($event['eOrganizer']);
             $bl_data['eStart'] = $event['eStart'];
             $bl_data['eEnd'] = $event['eEnd'];
             $bl_data['i_DateTime'] = $event['eStart'];
             $bl_data['eRecur'] = $event['eRecur'];
             $bl_data['eDuration'] = $event['eDuration'];
             $bl_data['eDurationHours'] = $event['eDurationHours'];
             $bl_data['eRrule'] = $event['eRrule'];
             $bl_data['eIsallday'] = $event['eIsallday'];
             $bl_data['eFee'] = $event['eFee'];
                // Migrated from get()']; in preparation for merging.
             $bl_data['event_id'] =$event['eID'];
             $bl_data['calendar_id'] =$event['eCalendarID'];
             $bl_data['type'] = $event['eType'];
             $bl_data['organizer'] = $event['eOrganizer'];
             $bl_data['contact'] = $event['eContact'];
             $bl_data['url'] = $event['eUrl'];
             $bl_data['summary'] = xarVarPrepForDisplay($event['eName']);
             $bl_data['description'] = xarVarPrepForDisplay($event['eDescription']);
             $bl_data['related_to'] = $event['eRelatedTo'];
             $bl_data['reltype'] = $event['eReltype'];
             $bl_data['class'] = $event['eClass'];
             $bl_data['share_uids'] = $event['eShareUIDs'];
             $bl_data['priority'] = $event['ePriority'];
             $bl_data['status'] = $event['eStatus'];
             $bl_data['location'] = xarVarPrepForDisplay($event['eLocation']);
             $bl_data['street1'] = xarVarPrepForDisplay($event['eStreet1']);
             $bl_data['street2'] = xarVarPrepForDisplay($event['eStreet2']);
             $bl_data['city'] = xarVarPrepForDisplay($event['eCity']);
             $bl_data['state'] = xarVarPrepForDisplay($event['eState']);
             $bl_data['zip'] = xarVarPrepForDisplay($event['eZip']);
             $bl_data['phone'] = xarVarPrepForDisplay($event['ePhone']);
             $bl_data['email'] = xarVarPrepEmailDisplay($event['eEmail']);
             $bl_data['fee'] = $event['eFee'];
             $bl_data['exdate'] = $event['eExdate'];
             $bl_data['categories'] = $event['eCategories'];
             $bl_data['recur_freq'] = $event['eRecurFreq'];
             $bl_data['recur_until'] = $event['eRecur'];
             $bl_data['recur_count'] = $event['eRecurCount'];
             $bl_data['recur_interval'] = $event['eRecurInterval'];
             $bl_data['dtstart'] = $event['eStart'];
             $bl_data['dtend'] = $event['eDtend'];
             $bl_data['duration'] = $event['eDuration'];
             $bl_data['freebusy'] = $event['eFreebusy'];
             $bl_data['due'] = $event['eDue'];
             $bl_data['transp'] = $event['eTransp'];
             $bl_data['created'] = $event['eCreated'];
             $bl_data['last_modified'] = $event['eLastModified'];

    // Security check
    if (!xarSecurityCheck('ReadJulian', 1, 'Item', "$event_id:$bl_data[organizer]:$bl_data[calendar_id]:All")) return;

    $bl_data['Bullet'] = '&'.xarModGetVar('julian', 'BulletForm').';';
    // Make an admin adjustable time format
    $dateformat = xarModGetVar('julian', 'dateformat');
    $timeformat = xarModGetVar('julian', 'timeformat');
    $dateformat_created = $dateformat . ' ' . $timeformat;

    // Don't like this here
    if (!isset($bl_data['eRecur']['timestamp']) ||
        is_numeric($bl_data['eRecur']['timestamp']) ||
        strpos($bl_data['eRecur']['timestamp'], '0000')!== false) {
            $bl_data['recur_until'] = 'recur_until';
    }

    $bl_data['event_id'] = $bl_data['event_id'];
    $bl_data['deletesummary'] = xarVarPrepForDisplay($bl_data['summary']);

    // TODO: MichelV: improve ML settings here
    // created = yyyy-mm-dd hh:mm:ss
    //$bl_data['datecreated'] = xarLocaleGetFormattedDate($bl_data['created']);
    $bl_data['datecreated'] = date("$dateformat_created",strtotime($bl_data['created']));
    $bl_data['fee'] = ($bl_data['fee'] != '') ? xarLocaleFormatCurrency($bl_data['fee']) : xarML('Unknown');
    $bl_data['authid'] = xarSecGenAuthKey();

    // Add obfuscator: for later Bug 4971
    // $bl_data['email'] = xarModAPIFunc('sitecontact', 'user', 'obfuemail', array('email'=>$bl_data['email']));
    // Add bullet for header
    $bl_data['Bullet'] = '&' . xarModGetVar('julian', 'BulletForm') . ';';
    /* Get rid of the NULLs */

    if (isset($bl_data['phone'])) {
        $bl_data['phone'] = xarVarPrepForDisplay($bl_data['phone']);
    } else {
        $bl_data['phone'] ='';
    }

    if (!is_null($bl_data['url'])) {
        $bl_data['URL'] = xarVarPrepForDisplay($bl_data['url']); // TODO: Get rid of this [Why?]
    } else {
        $bl_data['URL'] ='';
    }

    if (isset($bl_data['zip'])) {
        $bl_data['zip'] = xarVarPrepForDisplay($bl_data['zip']);
    } else {
        $bl_data['zip'] = '';
    }

    //if there was a duration set for this event, format a string indicating the from and to times
    $duration='';
    if ($bl_data['duration'] != '')
    {
        list($hours,$minutes) = explode(":",$bl_data['duration']);
        $duration = xarML(
            'from #(1) to #(2)',
            date("g:i A", $bl_data['dtstart']['unixtime']),
            date("g:i A", strtotime("+" . $hours . " hours " . $minutes . " minutes", $bl_data['dtstart']['unixtime']))
        );
    }

    //Localise day names
    $day_array = array(
        "1" => xarML('Sunday'),
        "2" => xarML('Monday'),
        "3" => xarML('Tuesday'),
        "4" => xarML('Wednesday'),
        "5" => xarML('Thursday'),
        "6" => xarML('Friday'),
        "7" => xarML('Saturday')
     );

    //Checking if we are viewing a reoccuring event
    if ($bl_data['recur_freq']) {
        $recur_count = $bl_data['recur_count'];
        $rrule = $bl_data['eRrule'];
        $recur_interval = $bl_data['recur_interval'];
        $intervals = array(
            "1" => xarML('Day(s)'),
            "2" => xarML('Week(s)'),
            "3" => xarML('Month(s)'),
            "4" => xarML('Year(s)')
        );
        //build the effective date string
        $eff =xarML('effective #(1)', date("$dateformat", $bl_data['dtstart']['unixtime']));

        //start the time string
        //Build the strings to describe the repeating event.
        if (!$bl_data['recur_count']) {
            //this is for the 'every' recurring event type
            $time = xarML(
                'Occurs every #(1) #(2) on #(3) #(4)',
                $bl_data['recur_freq'],
                $intervals[$rrule],
                xarLocaleFormatDate('%A', $bl_data['dtstart']['unixtime']),
                $eff
            );
        } else {
            // build a day array
            $weektimes = array(
                "1" => xarML('First'),
                "2" => xarML('Second'),
                "3" => xarML('Third'),
                "4" => xarML('Fourth'),
                "5" => xarML('Last')
            );
            // this is for the 'on every' recurring event type
            $time = xarML(
                'Occurs the #(1) #(2) every #(3) #(4) #(5)',
                $weektimes[$recur_interval],
                $day_array[$recur_count],
                $bl_data['recur_freq'],
                $intervals[$rrule],
                $eff
            );
        }

        //add the end date if one exists
        //TODO: MichelV move this to template
        if ($bl_data['recur_until']['timestamp'] != '' && $bl_data['recur_until'] != 'recur_until') {
            $time = xarML('#(1) until #(2)', $time, date("$dateformat", $bl_data['recur_until']['unixtime']));
        }

        // if the duration has not been set and this is not an all day event, add the start time to the string
        // FIXME: dreaded strcmp! Too late in the day to work out what this one does.
        $duration= strcmp($duration,"") ? $duration:($bl_data['isallday']?'':"&#160;".xarML('at #(1)',date("g:i A",$bl_data['dtstart']['unixtime'])));
        $bl_data['time'] = $time;

        // If there is no duration and this is not an all day event, show the time at the front.
    } else if (!$bl_data['isallday'] && !strcmp($duration,'')) {
        $bl_data['time'] = date("g:i A l, $dateformat", $bl_data['dtstart']['unixtime']);
    } else {
        $bl_data['time'] =  $day_array[date("w", $bl_data['dtstart']['unixtime'])+1] . " " . $bl_data['dtstart']['viewdate'];
    }
    $bl_data['cal_date'] = $cal_date;
    $bl_data['duration'] = $duration;

    // Set the url to this page in session as the last page viewed
    // FIXME: remove session stuff; we have URLs and forms for this
    $lastview = xarModURL(
        'julian', 'user', 'viewevent',
        array('cal_date'=>$cal_date, 'event_id' => $event_id)
    );
    xarSessionSetVar('lastview', $lastview);

    $uid = xarUserGetVar('uid');

    // Priv checks. We add a AddJulian here because we need to check on the own events, not of others
    // TODO: put the links in the templates, with secruity checks there
    if (xarSecurityCheck('EditJulian', 0, 'Item', "$event_id:$bl_data[organizer]:$bl_data[calendar_id]:All")) {
        // Add edit link
        $bl_data['editlink'] = xarModURL('julian', 'user', 'edit', array('cal_date' => $cal_date, 'event_id' => $event_id));
    } else {
        $bl_data['editlink'] = '';
    }

    if (xarSecurityCheck('DeleteJulian', 0, 'Item', "$event_id:$bl_data[organizer]:$bl_data[calendar_id]:All")) {
        // Add delete link
        $bl_data['deletelink'] = xarModURL(
            'julian', 'admin', 'delete',
            array('cal_date' => $cal_date, 'event_id' => $event_id, 'authid' => $bl_data['authid'])
        );
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
        // You can use the output from individual hooks in your template too, e.g. with
        // $hookoutput['comments'], $hookoutput['hitcount'], $hookoutput['ratings'] etc.
        $bl_data['hookoutput'] = $hooks;
    }

    $bl_data['organizer'] = xarUserGetVar('name', $bl_data['organizer']);

    return $bl_data;
}

?>