<?php
/**
 * Dossier Module - A Contact and Customer Service Management Module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author Chad Kraeft
 */
function dossier_user_dayschedule($args)
{
    extract($args);
    
    if (!xarVarFetch('displaydate', 'str::', $displaydate, '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('dossier','user','menu');

    if (!xarSecurityCheck('TeamAccess')) {
        return;
    }
    if(empty($displaydate)) {
        $displaydate = date("Y-m-d");
    } else {
        $displaydate = date('Y-m-d',strtotime($displaydate));
    }
    
    list($year,$month,$day) = explode("-",$displaydate);
    $enddate = sprintf("%04d-%02d-%02d", $year, $month, $day);
    
    $data['formatted_displaydate'] = date("l, M jS, Y", strtotime($displaydate));
    $nextday = date("Y-m-d", strtotime($displaydate) + (24 * 3600));
    
    if(!xarModAPILoad('julian', 'user')) return;
    
    $eventlist = array();
    
    if(!empty($displaydate)) {
        $julian_dates = xarModAPIFunc('julian', 
                                    'user', 
                                    'getall', 
                                    array('startdate' => $displaydate,
                                        'enddate' => $enddate));
        if(!$julian_dates) $julian_dates = array();
    
        foreach($julian_dates as $julianeventlist) {
            foreach($julianeventlist as $eventinfo) {
                $starthour = substr($eventinfo['time'],0,strpos($eventinfo['time'],":"));
                $eventlist[$starthour][] = array('eventlink' => xarModURL('julian', 'user', 'viewevent', array('event_id'=>$eventinfo['event_id'])),
                                                'event_id' => $eventinfo['event_id'],
                                                'starthour' => $starthour,
                                                'details' => $eventinfo['summary']);
            }
        }
    }
    
    $ownerid = xarSessionGetVar('uid');
    $reminder_dates = xarModAPIFunc('dossier', 
                                    'reminders', 
                                    'getall', 
                                    array('startdate' => $displaydate,
                                        'enddate' => $nextday));
    if(!$reminder_dates) $reminder_dates = array();
    
    foreach($reminder_dates as $eventinfo) {
        list($longdate,$longtime) = explode(" ",$eventinfo['reminderdate']);
        list($year,$month,$day) = explode("-",$longdate);
        list($hour,$min,$sec) = explode(":",$longtime);
        $eventlist[$hour][] = array('eventlink' => xarModURL('dossier', 'admin', 'display', array('contactid' => $eventinfo['contactid'])),
                                'starthour' => $hour,
                                'details' => $eventinfo['notes']);
    }
//    echo "<pre>";
//    print_r($julian_dates);
//    echo "</pre>";
//    die();
    
    // get all reminders
    // get all project dates
    // get all julian events
    
    $data['eventlist'] = $eventlist;
    
    return $data;
}

?>
