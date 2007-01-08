<?php
/**
 * Get an event.
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://www.metrostat.net
 * initial template: Roger Raymond
 */
/**
 * Get a single event
 *
 * Get a single event from the events table
 * Later we will look in the linked events table
 *
 * @author Julian package development team
 * @author  MichelV (Michelv@xarayahosting.nl)
 * @access  public
 * @param   int $event_id ID of the event to get
 * @return  array $item
 * @throws  BAD_PARAM list of exception identifiers which can be thrown
 * @todo    Michel V. <#> Implement in Julian.
 */

function julian_userapi_get($args)
{
    extract($args);

    if (!isset($event_id) || !is_numeric($event_id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'julian');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // establish a db connection
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $event_table = $xartable['julian_events'];

    // Get all the info for the event
    $query = "SELECT
            event_id,
            calendar_id,
            type,
            organizer,
            contact,
            url,
            summary,
            description,
            related_to,
            reltype,
            class,
            share_uids,
            priority,
            status,
            location,
            street1,
            street2,
            city,
            state,
            zip,
            phone,
            email,
            fee,
            exdate,
            categories,
            rrule,
            recur_freq,
            recur_until,
            recur_count,
            recur_interval,
            dtstart,
            dtend,
            duration,
            isallday,
            freebusy,
            due,
            transp,
            created,
            last_modified
             FROM $event_table
             WHERE event_id =?";
    $result = &$dbconn->Execute($query, array($event_id));
    if (!$result) return;
    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This event does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }

    // Extract fields
    list($event_id, $calendar_id,$type,$organizer,$contact,$url,$summary,
            $description,$related_to,$reltype,$class,$share_uids,$priority,$status,$location,$street1,$street2,$city,$state,
            $zip,$phone,$email,$fee,$exdate,$categories,$rrule,$recur_freq,$recur_until,
            $recur_count,$recur_interval,$dtstart,$dtend,$duration,$isallday,$freebusy,$due,$transp,$created,$last_modified) = $result->fields;
    $result->Close();

    // Security checks
    // TODO make these work
    // For this function, the user must *at least* have READ access to this item
    if (!xarSecurityCheck('ReadJulian', 1, 'Item', "event_id:All:All:All")) {
        return;
    }

    $item = array(
            'event_id'      =>$event_id,
            'calendar_id'   =>$calendar_id,
            'type'          =>$type,
            'organizer'     =>$organizer,
            'contact'       =>$contact,
            'url'           =>$url,
            'summary'       =>$summary,
            'description'   =>$description,
            'related_to'    =>$related_to,
            'reltype'       =>$reltype,
            'class'         =>$class,
            'share_uids'    =>$share_uids,
            'priority'      =>$priority,
            'status'        =>$status,
            'location'      =>$location,
            'street1'       =>$street1,
            'street2'       =>$street2,
            'city'          =>$city,
            'state'         =>$state,
            'zip'           =>$zip,
            'phone'         =>$phone,
            'email'         =>$email,
            'fee'           =>$fee,
            'exdate'        =>$exdate,
            'categories'    =>$categories,
            'rrule'         =>$rrule,
            'recur_freq'    =>$recur_freq,
            'recur_until'   =>$recur_until,
            'recur_count'   =>$recur_count,
            'recur_interval'=>$recur_interval,
            'dtstart'       =>$dtstart,
            'dtend'         =>$dtend,
            'duration'      =>$duration,
            'isallday'      =>$isallday,
            'freebusy'      =>$freebusy,
            'due'           =>$due,
            'transp'        =>$transp,
            'created'       =>$created,
            'last_modified' =>$last_modified);

        // get the event category color
        $item['color'] = xarModAPIFunc('julian','user','getcolor',array('category'=>$categories));

    // Return the item array
    return $item;
}
?>
