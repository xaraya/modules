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
 * @todo    MichelV. <1> move queries to API [DONE]
 */
function julian_user_updateevent()
{
    xarVarFetch('cancel', 'str', $cancel, '', XARVAR_NOT_REQUIRED);

    // If Cancel was pressed, go back to previous page
    // TODO: use 'return_url' in the URL, rather than session variables, as session
    // variables have a habbit of hanging around and coming back to bite you.
    if (strcmp($cancel, '')) {
        $back_link = xarSessionGetVar('lastview');
        xarResponseRedirect($back_link);
    }

    if (!xarVarFetch('id',               'id',       $event_id,      0, XARVAR_NOT_REQUIRED)) return;
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

    if (!empty($event_id)) {
        // Event ID supplied: update existing event.
        if (!xarSecurityCheck('EditJulian', 1, 'Item')) return;
    } else {
        // Event doesn't exist yet. Create one.
        if (!xarSecurityCheck('AddJulian', 1, 'Item')) return;
    }

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    if (!empty($event_id)) {
        // If this is an event that repeats "on", the rrule is always 3 which is the representative of monthly.
        // the 'on' events are always repeated every so many months
        if($recur_count && $recur_freq1) {
            // TODO: create some constants. What does "3" mean...?
            $rrule = "3";
        }

        // If the user wants to share this event, build an array of users that will share the private event
        if(!empty($share_uids)) {
            $share = implode(',', $share_uids);
        } else {
            $share = '';
        }

        // TODO: support single-parameter dates being passed in.
        // TODO: validate this combination as a proper date.
        $eventstartdate = $event_year . '-' . $event_month . '-' . $event_day;

        // If this is a recurring event, determine the start date based on the recur type and the selected start date by the user.
        // Otherwise, the start date is the one selected by the user.
        if ($recur_freq1 > 0) {
            // load the event class
            $e = xarModAPIFunc('julian', 'user', 'factory', 'event');

            //set the start date for this recurring event
            $eventstartdate = $e->setRecurEventStartDate($eventstartdate, $recur_interval, $recur_count, $recur_freq1);
        }

        // Set the calendar date equal to the start date of this event in the format of 'Ymd'
        // TODO: why do we do this? If for the database, then deal with that in the update API.
        // (possibly something to do with the return URL)
        $cal_date = date('Ymd', strtotime($eventstartdate));

        // Checking which event_repeat rule is being used and setting the recur_freq to the right reoccuring frequency
        // recur_freq_array values represent the first, second and third radio buttons on the form for repeating events.
        // Using this because the value is being written to the same place in the database.
        $recur_freq_array = array('', $recur_freq2, $recur_freq1);
        $recur_freq = $recur_freq_array[$event_repeat];

        // Checking to see if there is an end date for this event. There will only be an end date on recurring events
        if ($event_endtype) {
            $recur_until = $event_endyear . '-' . $event_endmonth . '-' . $event_endday;
        } else {
            $recur_until = '';
        }
       
        // Set the start time
        // TODO: support 24-hour clock input formats
        $ampm = ($event_startampm == 1) ? "AM" : "PM";

        // TODO: this is the format the MySQL database requires - format it in the API, not here.
        $eventstartdate =  date('Y-m-d H:i:s', strtotime($eventstartdate . ' ' . $event_starttimeh . ':' . $event_starttimem . ':00 ' . $ampm));
       
        // If not an all day event, set the duration.
        if (!$event_allday && ($event_dur_hours > 0 || $event_dur_minutes > 0)) {
            // This is formatted in hours:minutes format as a string, and stored that way.
            // TODO: see if we can store this thing in a more sensible format, such as simple minutes.
            // At the very least, allow us to interact with the APIs in this format, isolating us from
            // the storage format.
            $duration = $event_dur_hours . ':' . $event_dur_minutes;
        } else {
            $duration = '';
        }

        // Putting the 3 parts of the phone number back into 1.
        // If phone1 is empty, phone2 and phone3 have to be empty
        // and we don't want to show the dashes.
        // TODO: make it possible to have a European type/custom phone field
        // TODO/FIXME: this is only done when an event is first created. What about update? Should it be any different?

        $TelFieldType = xarModGetVar('julian', 'TelFieldType');

        // Format the phone number string.
        // TODO: centralise this. Better still, can it not just be a DD property with the appropriate validation?
        // So many modules do things the long and hard way...
        if ($TelFieldType == 'US') {
            $phone = $phone1 . '-' . $phone2 . '-' . $phone3;
        } elseif ($TelFieldType == 'EU') {
            $phone = $phone1 . '-' . $phone2 . '-' . $phone3;
        } elseif ($TelFieldType == 'EUC') {
            $phone = $phone1 . '-' . $phone2;
        } elseif ($TelFieldType == 'OPEN') {
            $phone = $phone1;
        } else {
            $phone = '';
        }
    }

    // Build the array to pass into the API
    $params = compact(
        'event_id',
        'event_allday', 'contact', 'website',
        'summary', 'description',
        'class', 'location', 'share',
        'street1', 'street2', 'city', 'state', 'zip',
        'phone', 'email',
        'fee', 'category',
        'rrule', 'recur_freq', 'recur_until', 'recur_count', 'recur_interval',
        'duration', 'eventstartdate'
    );

    if (!empty($event_id)) {
        // Call the API to update the event.
        xarModAPIFunc('julian', 'admin', 'update', $params);
    } else {
        // Call the API to update the event.
        $event_id = xarModAPIFunc('julian', 'admin', 'create', $params);
    }

    // Go back to the view of the event
    // TODO: go back to the return_url if there is one.
    xarResponseRedirect(xarModURL('julian', 'user', 'viewevent', array('cal_date'=>$cal_date, 'event_id' => $event_id)));
}

?>