<?php
// $Id: month.php,v 1.3 2003/06/24 21:22:21 roger Exp $

function timezone_user_main()
{
    xarVarFetch('tz','str::',$tz);
    $bldata =& timezone_getCurrentTime($tz);
    return $bldata;
}

function &timezone_getCurrentTime(&$tz)
{
    $ical =& xarModAPIFunc('icalendar','user','factory','ical_parser');
    $ical->setFile("modules/timezone/zoneinfo/$tz.ics");
    $ical->parse();
    
    if(!isset($ical->vcalendar[0]['vtimezone'][0]['standard'])) {
        // this should at least exist
        return false;
    }
    
    // grab the standard times and sort them
    $standard =& $ical->vcalendar[0]['vtimezone'][0]['standard'];
    //usort($standard,'dtstart_asort');
    
    // check for the existence of daylight saving time and sort
    if(isset($ical->vcalendar[0]['vtimezone'][0]['daylight'])) {
        // this timezone has daylight saving time
        $daylight =& $ical->vcalendar[0]['vtimezone'][0]['daylight'];
        //usort($daylight,'dtstart_asort');
    } else {
        // this timezone does not have daylight saving time
        $daylight = false;
    }
    
    // initialize some counter and place holders
    $spos = 0; 
    $scount = 0;
    
    // loop through each standard time definition
    // see which one, if any, applies to us
    foreach($standard as $s){
        // remove the T and Z elements to aide in mathmatical comparisons
        $dtstart = str_replace(array('T','Z'),'',$s['DTSTART']);
        if(currentUTC() >= $dtstart) {
            // ok, we have a definition to check
            if(isset($s['RDATE'])) {
                // this is rather simple to check
                // first check to see if any of the dates match
                foreach($s['RDATE'] as $d) {
                    // grab the CCYYMMDD portion of the date string
                    $tmp_date = (int) substr($d,0,8);
                    if((int)currentUTCDate() < $tmp_date) {
                        // no need to go any further with this RDATE
                        continue;
                    } else {
                        // we found a date match so we need to check the time
                        $tmp_time = (int) substr($d,9,6);
                        if((int)currentUTCTime() < $tmp_time) {
                            // this time has not yet come
                            continue;
                        } else {
                            // we should mark this as a possible setting
                            $spos = $scount;
                        }
                    }
                }
            } elseif(isset($s['RRULE'])) {
                // this takes a bit more time
            }
        }
        $scount++;
    }
    
    echo $spos;
    
    if($daylight) {
        $dpos = 0; $dcount = 0;
        foreach($daylight as $d){
            // remove the T and Z elements
            $dtstart = str_replace(array('T','Z'),'',$d['DTSTART']);
            if(currentUTC() >= $dtstart) {
                $dpos = $dcount;
            }
            $dcount++;
        }
    }   
    mydump($ical);
    //mydump("spos: $spos"); 
    //mydump("dpos: $dpos");
    
    $st_offset =& $standard[0]['TZOFFSETTO'];
    $dt_offset =& $daylight[0]['TZOFFSETTO'];
    $st_name =& $standard[0]['TZNAME'];
    $dt_name =& $daylight[0]['TZNAME'];
    
    $utc = time();
    $st = xarModAPIFunc('timezone','user','parseOffset',$st_offset);
    $dt = xarModAPIFunc('timezone','user','parseOffset',$dt_offset);
    
    $st_utc = $utc + $st['total'];
    $dt_utc = $utc + $dt['total'];
    
    $bl_data = array(
        'st_offset'=>$st_offset,
        'dt_offset'=>$dt_offset,
        'st_name'=>$st_name,
        'dt_name'=>$dt_name,
        'st_utc'=>$st_utc,
        'dt_utc'=>$dt_utc,
        'utc'=>$utc
    );
    
    return $bl_data;
}

function __dtstart_rsort($a,$b)
{
    $a['DTSTART'] = (int) str_replace(array('T','Z'),'',$a['DTSTART']);
    $b['DTSTART'] = (int) str_replace(array('T','Z'),'',$b['DTSTART']);
    if($a['DTSTART'] == $b['DTSTART']) {
        return 0;
    } elseif($a['DTSTART'] < $b['DTSTART']) {
        return 1;
    } else {
        return -1;
    }
}
function __dtstart_asort($a,$b)
{
    $a['DTSTART'] = (int) str_replace(array('T','Z'),'',$a['DTSTART']);
    $b['DTSTART'] = (int) str_replace(array('T','Z'),'',$b['DTSTART']);
    if($a['DTSTART'] == $b['DTSTART']) {
        return 0;
    } elseif($a['DTSTART'] < $b['DTSTART']) {
        return -1;
    } else {
        return 1;
    }
}
/**
 *  returns the current utc datetime in ical format 
 */
function currentUTC()
{
    return xarLocaleFormatUTCDate('%Y%m%d%H%M%S',time());
}
/**
 *  returns the current utc date in ical format 
 */
function currentUTCDate()
{
    return xarLocaleFormatUTCDate('%Y%m%d',time());
}
/**
 *  returns the current utc time in ical format 
 */
function currentUTCTime()
{
    return xarLocaleFormatUTCDate('%H%M%S',time());
}

function mydump($var) 
{
    if(is_array($var)) {
        echo '<pre>'; print_r($var); echo '</pre>';
    } elseif(is_object($var)) {
        echo '<pre>'; print_r($var); echo '</pre>';
    } else {
        echo '<pre>'; echo $var; echo '</pre>';
    }
    echo "\n\n";
}

?>
