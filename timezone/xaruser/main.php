<?php
// $Id: month.php,v 1.3 2003/06/24 21:22:21 roger Exp $

function timezone_user_main()
{
// some timing for now to see how fast|slow the parser is
include_once('Benchmark/Timer.php');
//$t =& new Benchmark_Timer;
//$t->start();
    $ical =& xarModAPIFunc('icalendar','user','factory','ical_parser');
//$t->setMarker('Class Instantiated');    
    xarVarFetch('tz','str::',$tz);
//$t->setMarker('File Var Fetched');    
    //$ical->setFile('modules/timezone/zoneinfo/America/Phoenix.ics');
    $ical->setFile("modules/timezone/zoneinfo/$tz.ics");
//$t->setMarker('File Set');
    $ical->parse();
//$t->setMarker('Parsing Complete');

//$t->stop(); 
    
    ob_start();
        print_r($ical);
        $ical_out = ob_get_contents();
    ob_end_clean();
    

    
    /*
        In order to do this correctly we need to do a couple of things
        Calculate the UTC standard time for the current TZ
        Calculate the UTC daylight time for the current TZ
        Determine which RRULE the date falls into
        return the correct time.
    
    */
    $st_offset =& $ical->vcalendar[0]['vtimezone'][0]['standard'][0]['TZOFFSETTO'];
    $dt_offset =& $ical->vcalendar[0]['vtimezone'][0]['daylight'][0]['TZOFFSETTO'];
    $st_name =& $ical->vcalendar[0]['vtimezone'][0]['standard'][0]['TZNAME'];
    $dt_name =& $ical->vcalendar[0]['vtimezone'][0]['daylight'][0]['TZNAME'];
    
    preg_match('/([-+])? #optional -+ signs
                ([\d]{2}) # first 0-9
                ([\d]{2}) # second 0-9
                /x', $st_offset, $st_matches);
    
    preg_match('/([-+])? #optional -+ signs
                ([\d]{2}) # first 0-9
                ([\d]{2}) # second 0-9
                /x', $dt_offset, $dt_matches);
    
    $utc = time();
    $st_h =& $st_matches[2];
    $st_m =& $st_matches[3]/60;
    $dt_h =& $dt_matches[2];
    $dt_m =& $dt_matches[3]/60;
    
    switch($st_matches[1]) {
        case '-':
            $st_utc = $utc - (int)($st_h+$st_m)*3600;
            break;
        default:
            $st_utc = $utc + (int)($st_h+$st_m)*3600;
            break;
    }
    switch($dt_matches[1]) {
        case '-':
            $dt_utc = $utc - (int)($dt_h+$dt_m)*3600;
            break;
        default:
            $dt_utc = $utc + (int)($dt_h+$dt_m)*3600;
            break;
    }
    
    $bl_data = array(
        'ical'=>$ical_out,
        'st_offset'=>$st_offset,
        'dt_offset'=>$dt_offset,
        'st_name'=>$st_name,
        'dt_name'=>$dt_name,
        'st_utc'=>$st_utc,
        'dt_utc'=>$dt_utc,
        'utc'=>$utc
  //      'profile'=>$t->getOutput()
    );
    
    return $bl_data;
    
    
}

?>