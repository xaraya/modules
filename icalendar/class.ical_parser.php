<?php
/**
 * File: $Id: 
 *
 * iCalendar file parser
 *
 * @package icalendar
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @author Roger Raymond
 */

/**
 *  Defines
 */
define('_VCALENDAR_', 0);
define('_VEVENT_',    1);
define('_VTODO_',     2);
define('_VFREEBUSY_', 3);
define('_VALARM_',    4);
define('_VTIMEZONE_', 5);

/**
 * Use the PEAR date class
 */
require_once('Date/Calc.php');


class iCal_Parser
{
    var $file;              /* filename we're working on */
    var $ifile;             /* internal file pointer */
    var $file_mtime;        /* when the .ics file was created */
    var $error;             /* string to hold error messages */
    var $current;           /* what element are we parsing */
    var $line;              /* current line being read */
    var $tz_standard = false;
    var $tz_daylight = false;
    var $data;
    var $field;
    var $property;
    
    // pointers
    var $vcal_pos;      // what vcalendar are we parsing
    var $tz_pos;        // what vtimezone are we parsing
    var $event_pos;     // what vevent are we parsing
    var $todo_pos;      // what vtodo are we parsing
    var $freebusy_pos;  // what vfreebusy are we parsing
    var $alarm_pos;     // what valarm are we parsing
    
    // containers
    var $vcalendar  = array();
    
    /**
     * Constructor
     *
     * @access public
     * @param string $file optional file to parse
     */
    function iCal_Parser($file = NULL)
    {
        if(isset($file)) {
            $this->file =& $file;
            $this->parse();
        }

    }

    /**
     * Set the file to parse
     *
     * @access public
     * @param string $file path and name of icalendar file
     */
    function setFile($file) 
    {
        $this->file =& $file;
    }
    
    /**
     * Set the icalendar file content from text
     *
     * @access public
     * @param string $content icalendar content
     */
    function setContent($content)
    {
        $this->content =& $content;
    }


    /**
     * Parse the icalendar content
     *  
     * @access public
     *  
     */
    function parse()
    {
        //if(!isset($this->content)) {
        //    $this->__read_file();
        //}
        $this->__parse_file();
    }


    /**
     * Read the file into $this->content
     * 
     * @access private
     */
    /*function __read_file()
    {
        // we need to get the icalendar content
        if(!isset($this->file)) {
            // we need to return an exception here
            // we need a file!
            $this->error =& xarML('iCal_Parser Fatal Error::There is no file to parse');
            return false;
        } elseif(!file_exists($this->file)) {
            // we need to return an exception here
            // we need a file!
            $this->error =& xarML('iCal_Parser Fatal Error::File does not exist');
            return false;
        }
        $this->file_mtime = filemtime($this->file);
        $fd = fopen($this->file, 'r');
        $this->content =& trim(fread($fd, filesize($this->file)));
        fclose($fd);  
    }*/

    /**
     * Parse the actual icalendar content
     * 
     * @access private
     */
    function __parse_file()
    {
        // we need to get the icalendar content
        if(!isset($this->file)) {
            // we need to return an exception here
            // we need a file!
            $this->error =& xarML('iCal_Parser Fatal Error::There is no file to parse');
            return false;
        } elseif(!file_exists($this->file)) {
            // we need to return an exception here
            // we need a file!
            $this->error =& xarML('iCal_Parser Fatal Error::File does not exist');
            return false;
        }
        // we need the actual file date for icalendar compliance
        $this->file_mtime = filemtime($this->file);
        
        // grab the file and parse it
        $this->ifile = fopen($this->file, 'r');
        $nextline = fgets($this->ifile, 1024);
        if(trim($nextline) != 'BEGIN:VCALENDAR') {
            $this->error =& xarML('iCal_Parser Error::File is not a valid iCalendar file');
            return false;
        } 
        
        // parse the rest of the file
        while(!feof($this->ifile)) {
            $this->line = $nextline;
            $nextline = fgets($this->ifile, 1024);
            $nextline = preg_replace('/[\r\n]/', '', $nextline);
            // check for folding
            while(substr($nextline,0,1) == ' ') {
                $this->line = $this->line . substr($nextline, 1);
                $nextline = fgets($this->ifile, 1024);
                $nextline = preg_replace('/[\r\n]/', '', $nextline);
            }
            $this->line =& trim($this->line);

            switch($this->line) {
                
                case 'BEGIN:VCALENDAR' :
                    $this->current = _VCALENDAR_;
                    $this->vcal_pos = count($this->vcalendar);
                    $this->vcalendar[$this->vcal_pos] = array();
                    //$this->vcalendar[$this->vcal_pos] =& new iCal_VCALENDAR;
                    break;
                    
                case 'END:VCALENDAR' :
                    $this->current = null;
                    break;
                
                case 'BEGIN:VEVENT' :
                    $this->current = _VEVENT_;
                    // set up the new VEVENT array 
                    if(!isset($this->vcalendar[$this->vcal_pos]['vevent'])) {
                        $this->vcalendar[$this->vcal_pos]['vevent'] = array();
                    }
                    $this->event_pos = count($this->vcalendar[$this->vcal_pos]['vevent']);
                    $this->vcalendar[$this->vcal_pos]['vevent'][$this->event_pos] = array();
                    break;
                    
                case 'END:VEVENT' :
                    $this->current = null;
                    break;
                    
                case 'BEGIN:VTODO' :
                    $this->current = _VTODO_;
                    // set up the new VTODO array 
                    if(!isset($this->vcalendar[$this->vcal_pos]['vtodo'])) {
                        $this->vcalendar[$this->vcal_pos]['vtodo'] = array();
                    }
                    $this->todo_pos = count($this->vcalendar[$this->vcal_pos]['vtodo']);
                    $this->vcalendar[$this->vcal_pos]['vtodo'][$this->todo_pos] = array();
                    break;
                
                case 'END:VTODO' :
                    $this->current = null;
                    break;
                    
                case 'BEGIN:VFREEBUSY' :
                    $this->current = _VFREEBUSY_;
                    // set up the new VFREEBUSY array 
                    if(!isset($this->vcalendar[$this->vcal_pos]['vfreebusy'])) {
                        $this->vcalendar[$this->vcal_pos]['vfreebusy'] = array();
                    }
                    $this->freebusy_pos = count($this->vcalendar[$this->vcal_pos]['vfreebusy']);
                    $this->vcalendar[$this->vcal_pos]['vfreebusy'][$this->freebusy_pos] = array();
                    break;
                
                case 'END:VFREEBUSY' :
                    $this->current = null;
                    break;
                    
                case 'BEGIN:VALARM' :
                    $this->current = _VALARM_;
                    // set up the new VALARM array 
                    if(!isset($this->vcalendar[$this->vcal_pos]['valarm'])) {
                        $this->vcalendar[$this->vcal_pos]['valarm'] = array();
                    }
                    $this->alarm_pos = count($this->vcalendar[$this->vcal_pos]['valarm']);
                    $this->vcalendar[$this->vcal_pos]['valarm'][$this->alarm_pos] = array();
                    break;
                
                case 'END:VALARM' :
                    $this->current = null;
                    break;
                    
                case 'BEGIN:VTIMEZONE' :
                    $this->current = _VTIMEZONE_;
                    // where are we in the timezone array
                    if(!isset($this->vcalendar[$this->vcal_pos]['vtimezone'])) {
                        $this->vcalendar[$this->vcal_pos]['vtimezone'] = array();
                    } 
                    $this->tz_pos = count($this->vcalendar[$this->vcal_pos]['vtimezone']);
                    // create a new timezone container
                    $this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos] = array();
                    break;
                
                case 'END:VTIMEZONE' :
                    $this->current = null;
                    $this->__parse_vtimezone();
                    break;
                
                case 'BEGIN:STANDARD' :
                    $this->tz_standard = true;
                    if(!isset($this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos]['standard'])) {
                        $this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos]['standard'] = array();
                    }
                    $this->tz_spos = count($this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos]['standard']);
                    $this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos]['standard'][$this->tz_spos] = array();
                    break;
                    
                case 'END:STANDARD' :
                    // close out the standard timezone definition
                    $this->tz_standard = false;
                    break;
                    
                case 'BEGIN:DAYLIGHT' :
                    $this->tz_daylight = true;
                    if(!isset($this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos]['daylight'])) {
                        $this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos]['daylight'] = array();
                    }
                    $this->tz_dpos = count($this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos]['daylight']);
                    $this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos]['daylight'][$this->tz_dpos] = array();
                    break;
                
                case 'END:DAYLIGHT' :
                    // close out the daylight saving timezone definition
                    $this->tz_daylight = false;
                    break;
                
                // if the line is not any of the above, then we just want to parse it
                // this data is placed into the current container defined above    
                default:
                    $this->__parse_params();
                    break;
            }
        }
    }
    
    function __parse_params()
    {
        // get the property we are parsing
        $this->__get_property();
        // parse depending on where we are in the file
        switch($this->current) {
        
            case _VTIMEZONE_ :
                $this->__parse_vtimezone();
                break;
            
            case _VEVENT_ :
                $this->__parse_vevent();
                break;
                
            case _VTODO_ :
                $this->__parse_vtodo();
                break;
                
            case _VCALENDAR_:
            default :
                $this->__parse_vcalendar();
                break;
        
        }
    }
    
    /**
     * Grab the property name and and data associated with it
     * 
     * @access private
     */
    function &__get_property()
    {
        unset($field, $data, $prop_pos, $property);
        preg_match("/([^:]+):(.*)/i", $this->line, $line);
        $this->field =& $line[1];
        $this->data =& $line[2];
        $property =& $this->field;
        $prop_pos = strpos($property,';');
        if ($prop_pos !== false) $property = substr($property,0,$prop_pos);
        $this->property = strtoupper($property);
    }
    
    /**
     * Parse the information associated with the
     *  top level VCALENDAR component.
     *
     * @access private
     */
    function __parse_vcalendar()
    {
        $this->vcalendar[$this->vcal_pos][$this->property] = $this->data;
    }
    
    /**
     * Parse VEVENT
     *
     * @access private
     */
    function __parse_vevent()
    {
        // set up our link to the current VEVENT object
        $el =& $this->vcalendar[$this->vcal_pos]['vevent'][$this->event_pos];
        
        switch ($this->property) {
            
            case 'SEQUENCE':
                $el['SEQUENCE'] = $this->data;
                break;
                
            case 'SUMMARY':
                $this->data = str_replace("\\n", "<br/>", $this->data);
                $this->data = str_replace("\\r", "<br/>", $this->data);
                // why do the phpical devs do this?
                $this->data = htmlentities(urlencode($this->data));
                $el['SUMMARY'] = $this->data;
                break;
                
            case 'DESCRIPTION':
                $this->data = str_replace("\\n", "<br/>", $this->data);
                $this->data = str_replace("\\r", "<br/>", $this->data);
                // why do the phpical devs do this?
                $this->data = htmlentities(urlencode($this->data));
                $el['DESCRIPTION'] = $this->data;
                break;
                
            case 'CLASS':
                $el['CLASS'] = $this->data;
                break;
            
            case 'STATUS':
                $el['STATUS'] = $this->data;
                break;
                
            case 'CATEGORIES':
                $el['CATEGORIES'] = $this->data;
                break;
            
            case 'PRIORITY':
                $el['PRIORITY'] = $this->data;
                break;
                
            case 'DURATION':
                // allow for multiple durations if they exist
                $durations = explode(',',strtoupper($this->data));
                foreach($durations as $key=>$duration) {
                    if(!isset($el['DURATION'][$key])) {
                        $el['DURATION'][$key] = array();
                    }
                    preg_match('/^[+-]?P            # [0] start of a valid period definition 
                                 ([0-9]{1,2})?      # [1] how long is it going for           
                                 ([WD])?            # [2] weeks or days                      
                                 ([T])?             # [3] do we have a time value            
                                 ([0-9]{1,2}[H])?   # [4] hour durations                     
                                 ([0-9]{1,2}[M])?   # [5] minute durations                   
                                 ([0-9]{1,2}[S])?   # [6] second durations                   
                                /x',$this->data,$matches);
                    
                    $el['DURATION'][$key]['days'] = $matches[2] == 'D' ? $matches[1] : null;
                    $el['DURATION'][$key]['weeks'] = $matches[2] == 'W' ? $matches[1] : null;
                    if(isset($matches[4])) {
                        $el['DURATION'][$key]['hours'] = str_replace('H','',$matches[4]);
                    } else {
                        $el['DURATION'][$key]['hours'] = null;
                    }
                    if(isset($matches[5])) {
                        $el['DURATION'][$key]['minutes'] = str_replace('M','',$matches[5]);
                    } else {
                        $el['DURATION'][$key]['minutes'] = null;
                    }
                    if(isset($matches[6])) {
                        $el['DURATION'][$key]['seconds'] = str_replace('S','',$matches[6]);
                    } else {
                        $el['DURATION'][$key]['seconds'] = null;
                    }
                    // get the total amount in seconds for easier math
                    $total =  (int) ($el['DURATION'][$key]['weeks'] * 60 * 60 * 24 * 7);
                    $total += (int) ($el['DURATION'][$key]['days'] * 60 * 60 * 24);
                    $total += (int) ($el['DURATION'][$key]['hours'] * 60 * 60);
                    $total += (int) ($el['DURATION'][$key]['minutes'] * 60);
                    $total += (int)  $el['DURATION'][$key]['seconds'];
                    $el['DURATION'][$key]['total'] = $total;
                }
                break;
                
            case 'UID':
                $el['UID'] = $this->data;
                break;
            
            case 'RRULE':
                $this->data = str_replace('RRULE:', '', $this->data);
                $rrule = split (';', $this->data);
                foreach ($rrule as $recur) {
                    preg_match('/(.*)=(.*)/i', $recur, $match);
                    $el['RRULE'][$match[1]] = $match[2];
                }
                break;
            
            default:
                $el[$this->property] = $this->data;
                break;
        
        }
        
    }
    
    /**
     * Parse VTODO
     *
     * @access private
     */
    function __parse_vtodo()
    {
    
    }
    
    /**
     * Parse VTIMEZONE
     *
     * @access private
     */
    function __parse_vtimezone()
    {
        // what object are we assigning data to?
        if((bool)$this->tz_standard) {
            $el =& $this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos]['standard'][$this->tz_spos];
        } elseif((bool)$this->tz_daylight) {
            $el =& $this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos]['daylight'][$this->tz_dpos];
        } else {
            $el =& $this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos];
        }
        
        switch ($this->property) {
            case 'TZID' :
                // populate the current TZID for this element
                // TODO::this element probably exists in a lot of different ways
                $el['TZID'] = $this->data;
                break;
            
            case 'TZOFFSETFROM' :
                $el['TZOFFSETFROM'] = $this->data;
                break;
                
            case 'TZOFFSETTO' :
                $el['TZOFFSETTO'] = $this->data;
                break;
            
            case 'TZNAME':
                $el['TZNAME'] = $this->data;
                break;
                
            case 'DTSTART':
                // see if the date is represented in UTC
                $zulu = (substr($this->data,-1)=='Z') ? true : false;
		        $this->data  = str_replace('T','',$this->data); // remove the T for easier processing
		        $this->data  = str_replace('Z','',$this->data); // remove the Z if it exists
		        $this->field = str_replace(';VALUE=DATE-TIME','',$this->field); // yep, we know :)

                // DTSTART for timezones should be simple and only contain a datetime
                // without a lot of extra parameters.
                preg_match('/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})([0-9]{0,2})/', $this->data, $regs);

                // ADOdb_date will change dates that fall in the less that 100 year range
                // we attempt to catch this so we can later subtract this value
                // this probably creates inaccurate results, but it shouldn't be a problem
                // since I don't expect most apps to be using dates before the year 100
                // this is mainly a hack for the timezone module
                // set a flag to check to see if we need to subtract from the date later
                $lt100 = ($regs[1] < 100) ? true : false ;
                // ADOdb_date modified the date based on prev and next centuries
                // for dates before the year 33 it makes the date the current century
                // for dates from the year 33 and on, it makes it the previous century
                // we attempt to determine how many years to subtract from year prior to 100
                $lt100subtract = (($regs[1] < 33) ? strftime('%C') : strftime('%C')-1) * 100 ;

                // get the unixtime for this date
                $start_unixtime = adodb_mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);

                // we're going to convert this timestamp into a UTC unix stamp
                // this should make it easier to implement in our applications
                if((bool)$zulu) {
                    $offset = '+0000';
                } elseif((bool)$this->tz_standard) {
                    $offset = $el['TZOFFSETFROM'];
                } elseif((bool)$this->tz_daylight) {
                    $offset = $el['TZOFFSETFROM'];
                } else {
                    $offset = '+0000';
                }
                $offset = $this->tzOffset2Seconds($offset);
                $start_unixtime -= $offset;
                // we format the date twice because we need to make sure the year
                // is comprised of four (4) integers
                $el['DTSTART'] = sprintf('%04d',adodb_date('Y', $start_unixtime));
                // check to see if the date was modified by ADOdb_date for dates before the year 100
                if($lt100) {
                    $el['DTSTART'] = sprintf('%04d',$el['DTSTART']-$lt100subtract);
                }
                unset($lessThan100,$lt100subtract);
                $el['DTSTART'] .= adodb_date('md\THis\Z', $start_unixtime);
                break;
                
            case 'RRULE':
                $this->data = str_replace('RRULE:', '', $this->data);
                $rrule = split (';', $this->data);
                foreach ($rrule as $recur) {
                    preg_match('/(.*)=(.*)/i', $recur, $match);
                    $el['RRULE'][$match[1]] = $match[2];
                }
                break;
                
            case 'RDATE':
                // see if the date is represented in UTC
                $zulu = (substr($this->data,-1)=='Z') ? true : false;
                $this->data  = str_replace('T','',$this->data); // remove the T for easier processing
                $this->data  = str_replace('Z','',$this->data); // remove the Z if it exists
                $this->field = str_replace(';VALUE=DATE-TIME','',$this->field); // yep, we know :)
                preg_match('/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})([0-9]{0,2})/', $this->data, $regs);
                $lt100 = ($regs[1] < 100) ? true : false ;
                $lt100subtract = (($regs[1] < 33) ? strftime('%C') : strftime('%C')-1) * 100 ;
                $start_unixtime = adodb_mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
                if((bool)$zulu) {
                    $offset = '+0000';
                } elseif((bool)$this->tz_standard) {
                    $offset = $el['TZOFFSETFROM'];
                } elseif((bool)$this->tz_daylight) {
                    $offset = $el['TZOFFSETFROM'];
                } else {
                    $offset = '+0000';
                }
                $offset = $this->tzOffset2Seconds($offset);
                $start_unixtime -= $offset;
                $this->data = sprintf('%04d',adodb_date('Y', $start_unixtime));
                // check to see if the date was modified by ADOdb_date for dates before the year 100
                if($lt100) {
                    $this->data = sprintf('%04d',$this->data-$lt100subtract);
                }
                unset($lessThan100,$lt100subtract);
                $this->data .= adodb_date('md\THis\Z', $start_unixtime);

                if(!isset($el['RDATE'])) {
                    $el['RDATE'] = array();
                }
                $el['RDATE'][] = $this->data;
                break;
                
            default:
                $el["$this->property"] = $this->data;    
                break;
            
        }
    }
    
    /**
     * Time Zone Offset ????
     *
     * @access public ????
     */
    function tzOffset2Seconds($offset) 
    {
        // make sure the offset starts with a + or -
        if(!preg_match('/([+-])([0-9]){2}([0-9]){2}/',$offset,$match)) {
            // we have an invalid offset, so just return zero
            return (int) 0;
        } 
        $flag  = $match[1];
        $seconds = ($match[2] * 60 * 60) + ($match[3] * 60);
        unset($match);
        return (int) "$flag$seconds";
    }
}

?>
