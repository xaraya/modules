<?php

// for now
header('Content-type: text/plain');

require_once 'class.icalendar.php';

function &get_property_varname($p)
{
    $name = strtolower($p);
    $name = str_replace('-','_',$name);
    return $name;    
}

function find_attributes(&$assign_to,&$vproperty,$n=-1) 
{
    // ok, let's tidy up and assign some other vars if available:
    if($n >= 0) {
        $property =& $vproperty->{$assign_to}[$n];
    } else {
        $property =& $vproperty->{$assign_to};
    }
    
    
    
    if(preg_match_all('/([A-Z-]+)="(.*)"[:|;]/s',$property,$matches,PREG_SET_ORDER)) {
        //print_r($matches);
        for($i=0,$max=count($matches); $i<$max; $i++) {
            $attribute = $assign_to.'_'.$matches[$i][1];
            $attribute = strtolower($attribute);
            $attribute = str_replace('-','_',$attribute);
            if($n >= 0) {
                $vproperty->{$attribute}[$n] = $matches[$i][2];
            } else {
                $vproperty->{$attribute} = $matches[$i][2];
            }
            // remove the attribute from the string
            if($n >= 0) {
                $vproperty->{$assign_to}[$n] = str_replace($matches[$i][0],'',$property);
            } else {
                $vproperty->{$assign_to} = str_replace($matches[$i][0],'',$property);
            }
            unset($attribute);
        }
    }
    
    // only catch attributes that come before the first colon (:)
    // anything after the first : should be part of the value
    if(strpos($property,':') > strpos($property,';')) {    
        if(preg_match_all('/([A-Z-]+)=([^;:]*)[:|;]?/s',$property,$matches,PREG_SET_ORDER)) {
            //print_r($matches);
            for($i=0,$max=count($matches); $i<$max; $i++) {
                $attribute = $assign_to.'_'.$matches[$i][1];
                $attribute = strtolower($attribute);
                $attribute = str_replace('-','_',$attribute);
                if($n >= 0) {
                    $vproperty->{$attribute}[$n] = $matches[$i][2];
                } else {
                    $vproperty->{$attribute} = $matches[$i][2];
                }
                // remove the attribute from the string
                if($n >= 0) {
                    $vproperty->{$assign_to}[$n] = str_replace($matches[$i][0],'',$property);
                } else {
                    $vproperty->{$assign_to} = str_replace($matches[$i][0],'',$property);
                }
                unset($attribute);
            }
        }
        
    }
}

function &parse_vcalendar(&$content,&$ical)
{
    foreach($content as $line_number => $line) {
        if(preg_match('/^\s+/',$line) && isset($assign_to)) {
            $ical->{$assign_to} .= trim($line);
            continue;
        
        } elseif(isset($assign_to)) {
            $ical->{$assign_to} = preg_replace('/[\n\r]/','',trim($ical->{$assign_to}));
            $ical->{$assign_to} = preg_replace('/^[:;]/','',$ical->{$assign_to});
            unset($assign_to);
        }
        
        if(preg_match('/^([A-Z-]+)(;|:)?(.*)$/',$line,$match)) {
            $assign_to = get_property_varname($match[1]);
            // ok, since we can have lots of stuff from this, let's bust it up            
            $ical->{$assign_to} = trim(substr($line,strlen($assign_to)+1));
            // we don't need to be in this loop anymore
        }
    }
}

function &parse_icalendar_data(&$content)
{
    $vcalendar_content = array();
    $vevent_collect = false;
    $vevent_content = '';
    $pointer = array();
    $parsed_cals = array();
    
    foreach($content as $line_number=>$line) {

        // look for an event to parse
        if(strstr($line,'BEGIN:VCALENDAR')) {
            // well, we need this, so include it.
            $vcalendar_start_at = $line_number;
            $ical =& new iCalendar;
            $pointer[] = 'VCALENDAR';

        } elseif(strstr($line,'END:VCALENDAR')) {
            // we should have content for the main vcalendar object as well now
            parse_vcalendar($vcalendar_content,$ical);
            //print_r($vcalendar_content);
            //print_r($ical);
            // this should give us an empty array
            $parsed_cals[] = $ical;
            array_pop($pointer);

        } elseif(strstr($line,'BEGIN:VEVENT')) {
            // well, we need this, so include it.
            require_once 'class.icalendar_event.php';
            require_once 'parser.icalendar_event.php';
            // capture the starting line for this event
            $vevent_start_at = $line_number;
            $ical->create_vevent();
            $pointer[] = 'VEVENT';

        } elseif(strstr($line,'END:VEVENT')) {
            // capture the ending line for this event
            $vevent_content = array_slice($content,$vevent_start_at,($line_number-$vevent_start_at+1));
            //print_r($vevent_content);
            parse_vevent($vevent_content,$ical);
            // this should put us back to where we were
            array_pop($pointer);

        } elseif(strstr($line,'BEGIN:VTODO')) {
            // well, we need this, so include it.
            require_once 'class.icalendar_todo.php';
            require_once 'parser.icalendar_todo.php';
            // capture the starting line for this event
            $vtodo_start_at = $line_number;
            $ical->create_vtodo();
            $pointer[] = 'VTODO';

        } elseif(strstr($line,'END:VTODO')) {
            // capture the ending line for this event
            $vtodo_content = array_slice($content,$vtodo_start_at,($line_number-$vtodo_start_at+1));
            //print_r($vevent_content);
            parse_vtodo($vtodo_content,$ical);
            // this should put us back to where we were
            array_pop($pointer);

        } elseif(strstr($line,'BEGIN:VJOURNAL')) {
            // well, we need this, so include it.
            require_once 'class.icalendar_journal.php';
            require_once 'parser.icalendar_journal.php';
            // capture the starting line for this event
            $vjournal_start_at = $line_number;
            $ical->create_vjournal();
            $pointer[] = 'VJOURNAL';

        } elseif(strstr($line,'END:VJOURNAL')) {
            // capture the ending line for this event
            $vjournal_content = array_slice($content,$vjournal_start_at,($line_number-$vjournal_start_at+1));
            //print_r($vevent_content);
            parse_vjournal($vjournal_content,$ical);
            // this should put us back to where we were
            array_pop($pointer);

        } elseif($pointer[count($pointer)-1] == 'VCALENDAR') {
            // we're inside the VCALENDAR object, we should probably be parsing something
            $vcalendar_content[] = $line;
        } else {
            // we're here, but we don't really have anything to do - HI!
            continue;
        }

    }
    return $parsed_cals;
}

/**
 *  Used to parse an icalendar file from someplace (http, local, etc)
 */
function &parse_icalendar_file($filename = null) 
{
    // just for testing purposes right now
    if(!isset($filename)) {
        $filename = 'test_cals/Home.ics'; file();
    } 
    
    $content = file($filename);
    
    // let's pass this content array to the parser
    $ical =& parse_icalendar_data($content);
    // here you go
    return $ical;
}

/**
 *  Used to parse icalendar content from plain text
 */
function &parse_icalendar_content($content = null) 
{
    // just for testing purposes right now
    if(!isset($content) && !is_array($content)) {
        return false;
    }
    // we need to split it based on LF/CR and create a line-by-line array
    $array_content = preg_split("/[\r\n]/",$content);
    // let's pass this array to the parser
    $ical =& parse_icalendar_data($array_content);
    // here you go
    return $ical;
}

function getmicrotime()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
} 

// just include a timer script here to see how long this takes
$start = getmicrotime();
//print_r(parse_icalendar_file('/home/httpd/repos/xaraya-icalendar/xarincludes/test_cals/CalendarDataFile0.ics'));
//print_r(parse_icalendar_file('/home/httpd/repos/xaraya-icalendar/xarincludes/test_cals/CalendarDataFile1.ics'));
//print_r(parse_icalendar_file('/home/httpd/repos/xaraya-icalendar/xarincludes/test_cals/CalendarDataFile2.ics'));
//print_r(parse_icalendar_file('/home/httpd/repos/xaraya-icalendar/xarincludes/test_cals/CalendarDataFile3.ics'));
//print_r(parse_icalendar_file('/home/httpd/repos/xaraya-icalendar/xarincludes/test_cals/CalendarDataFile4.ics'));
//print_r(parse_icalendar_file('http://www.mozilla.org/projects/icalendar/caldata/PolishHolidays.ics'));
$parsed_ical =& parse_icalendar_file('/home/httpd/repos/xaraya-icalendar/xarincludes/test_cals/Home.ics');
$cached_ical = serialize($parsed_ical);

print_r($parsed_ical);
print_r($cached_ical);

$end = getmicrotime();
echo "\n\n".($end-$start)."\n\n";

// this should work nicely to tokenize a string for the cals
$tok = strtok('TZID=US/Pacific:20020822T170000',':;');
while($tok) {
    echo "$tok\n";
    $tok = strtok(':;');
}

?>