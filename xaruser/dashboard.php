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
function dossier_user_dashboard($args)
{
    extract($args);
    
    if(!xarVarFetch('displaymonth', 'str::', $displaymonth, date('Y-m'), XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('dossier','user','menu');
    
    $data['displaymonth'] = $displaymonth;

    if (!xarSecurityCheck('TeamDossierAccess')) {
        return;
    }
    
    $dayview = xarModFunc('dossier', 'user', 'dayschedule', array('displaymonth' => $displaymonth));
    
    $data['dayview'] = $dayview;
    
    $eventlist = array();
    
    // get list of all events:
    // * julian    
    if(xarModIsAvailable('julian')) {
        if(xarModAPILoad('julian', 'user')) {
        
            $julian_dates = xarModAPIFunc('julian', 'user', 'getall', array('startdate' => $displaymonth."-01"));
            if(!$julian_dates) $julian_dates = array();
            
        //    echo "<pre>";
        //    print_r($julian_dates);
        //    echo "</pre>";
        //    die();
            
            foreach($julian_dates as $julianeventlist) {
                foreach($julianeventlist as $eventinfo) {
                    list($month,$day,$year) = explode("-",$eventinfo['startdate']);
                    $eventlist[$year][$month][$day] = $eventinfo['summary'];
                }
            }
        }
    }
            
    $ownerid = xarSessionGetVar('uid');
    $reminder_dates = xarModAPIFunc('dossier', 'reminders', 'getall', array('ownerid' => $ownerid));
    if(!$reminder_dates) $reminder_dates = array();
    
//    echo "<pre>";
//    print_r($julian_dates);
//    echo "</pre>";
//    die();
    
    foreach($reminder_dates as $eventinfo) {
        list($longdate,$longtime) = explode(" ",$eventinfo['reminderdate']);
        list($year,$month,$day) = explode("-",$longdate);
        $eventlist[$year][$month][$day] = $eventinfo['notes'];
    }
    
    // * events
    // * project dates
    // * reminder dates
    
    $data['eventlist'] = $eventlist;
    
    return $data;
}

?>
