<?php
/**
 * Julian Module : calendar with events
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module development team
 */
/**
 *  iCalendar file parser
 */
define('_VCALENDAR_', 0);
define('_VEVENT_',    1);
define('_VTODO_',     2);
define('_VFREEBUSY_', 3);
define('_VALARM_',    4);
define('_VTIMEZONE_', 5);

class iCal_Parser
{
    var $file;              /* filename we're working on */
    var $ifile;             /* internal file pointer */
    var $file_mtime;        /* when the .ics file was created */
    //var $content;           /* icalendar plain text content */
    //var $parsed_content;    /* icalendar content parsed into an array */
    var $error;             /* string to hold error messages */
    var $current;           /* what element are we parsing */
    var $line;              /* current line being read */
    var $tz_standard = false;
    var $tz_daylight = false;
    var $data;
    var $field;
    var $property;

    // pointers
    var $vcal_pos; // what vcalendar are we parsing

    // containers
    var $vcalendar  = array();
    //var $vtimezone  = array();
    //var $vevent     = array();
    //var $vtodo      = array();
    //var $vfreebusy  = array();
    //var $valarm     = array();

    /**
     *  ical_parser constructor
     *  @access public
     *  @param string $file optional file to parse
     */
    function iCal_Parser($file=null)
    {
        if(isset($filename)) {
            $this->file = $file;
            $this->parse();
        }
    }

    /**
     *  Set the file to parse
     *  @access public
     *  @param string $file path and name of icalendar file
     */
    function setFile($file)
    {
        $this->file = $file;
    }

    /**
     *  Sets the icalendar file content from text
     *  @access public
     *  @param string $in icalendar content
     */
    function setContent($content)
    {
        $this->content = $content;
    }


    /**
     *  Parses the icalendar content
     *  @access public
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
     *  reads the file into $this->content
     *  @access private
     */
    /*function __read_file()
    {
        // we need to get the icalendar content
        if(!isset($this->file)) {
            // we need to return an exception here
            // we need a file!
            $this->error = xarML('iCal_Parser Fatal Error::There is no file to parse');
            return false;
        } elseif(!file_exists($this->file)) {
            // we need to return an exception here
            // we need a file!
            $this->error = xarML('iCal_Parser Fatal Error::File does not exist');
            return false;
        }
        $this->file_mtime = filemtime($this->file);
        $fd = fopen($this->file, 'r');
        $this->content = trim(fread($fd, filesize($this->file)));
        fclose($fd);
    }*/

    /**
     *  parses the actual icalendar content
     *  @access private
     */
    function __parse_file()
    {
        // we need to get the icalendar content
        if(!isset($this->file)) {
            // we need to return an exception here
            // we need a file!
            $this->error = xarML('iCal_Parser Fatal Error::There is no file to parse');
            return false;
        } elseif(!file_exists($this->file)) {
            // we need to return an exception here
            // we need a file!
            $this->error = xarML('iCal_Parser Fatal Error::File does not exist');
            return false;
        }
        // we need the actual file date for icalendar compliance
        $this->file_mtime = filemtime($this->file);

        // grab the file and parse it
        $this->ifile = fopen($this->file, 'r');
        $nextline = fgets($this->ifile, 1024);
        if(trim($nextline) != 'BEGIN:VCALENDAR') {
            $this->error = xarML('iCal_Parser Error::File is not a valid iCalendar file');
            return false;
        }

        // parse the rest of the file
        while(!feof($this->ifile)) {
            $this->line = $nextline;
            $nextline = fgets($this->ifile, 1024);
            $nextline = preg_replace('/[\r\n]/', '', $nextline);
            // check for folding
            while(substr($nextline,0,1) == ' ') {
                $this->line = $line . substr($nextline, 1);
                $nextline = fgets($this->ifile, 1024);
                $nextline = preg_replace('/[\r\n]/', '', $nextline);
            }
            $this->line = trim($this->line);

            switch($this->line) {

                case 'BEGIN:VCALENDAR' :
                    $this->current = _VCALENDAR_;
                    $this->vcal_pos = count($this->vcalendar);
                    $this->vcalendar[$this->vcal_pos] = array();
                    break;

                case 'END:VCALENDAR' :
                    $this->current = null;
                    break;

                case 'BEGIN:VEVENT' :
                    $this->current = _VEVENT_;
                    break;

                case 'END:VEVENT' :
                    $this->current = null;
                    break;

                case 'BEGIN:VTODO' :
                    $this->current = _VTODO_;
                    break;

                case 'END:VTODO' :
                    $this->current = null;
                    break;

                case 'BEGIN:VFREEBUSY' :
                    $this->current = _VFREEBUSY_;
                    break;

                case 'END:VFREEBUSY' :
                    $this->current = null;
                    break;

                case 'BEGIN:VALARM' :
                    $this->current = _VALARM_;
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

                default:
                    $this->__parse_params();
                    break;
            }


        }
    }

    function __parse_params()
    {
        $this->__get_property();
        // parse depending on where we are in the file
        switch($this->current) {

            case _VTIMEZONE_ :
                $this->__parse_vtimezone();
                break;

            case _VCALENDAR_:
            default :
                $this->__parse_vcalendar();
                break;

        }
    }

    /**
     *  Grabs the property name and and data associated with it
     */
    function &__get_property()
    {
        unset($field, $data, $prop_pos, $property);
        preg_match("/([^:]+):(.*)/i", $this->line, $line);
        $this->field = $line[1];
        $this->data = $line[2];
        $property = $this->field;
        $prop_pos = strpos($property,';');
        if ($prop_pos !== false) $property = substr($property,0,$prop_pos);
        $this->property = strtoupper($property);
    }

    /**
     *  Parses the information associated with the
     *  top level VCALENDAR component.
     */
    function __parse_vcalendar()
    {
        $this->vcalendar[$this->vcal_pos][$this->property] = $this->data;
    }

    function __parse_vtimezone()
    {
        // what object are we assigning data to?
        if((bool)$this->tz_standard) {
            $el = $this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos]['standard'][$this->tz_spos];
        } elseif((bool)$this->tz_daylight) {
            $el = $this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos]['daylight'][$this->tz_dpos];
        } else {
            $el = $this->vcalendar[$this->vcal_pos]['vtimezone'][$this->tz_pos];
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
// TODO: adodb_datetime functions only handle dates >= 100 A.D.
// TODO: so, what are we going to do about that - eh!?!
                $start_unixtime = adodb_mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);

                // we're going to convert this timestamp into a UTC unix stamp
                // this should make it easier to implement in our applications
                if( (bool) $zulu) {
                    $offset = '+0000';
                } elseif( (bool) $this->tz_standard) {
                    $offset = $el['TZOFFSETTO'];
                } elseif( (bool) $this->tz_daylight) {
                    $offset = $el['TZOFFSETTO'];
                } else {
                    $offset = '+0000';
                }
                $offset = $this->tzOffset2Seconds($offset);
                $start_unixtime -= $offset;
                $el['DTSTART'] = adodb_date('Ymd\THis\Z', $start_unixtime);
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

    function __parse_vevent()
    {

    }

    function __parse_vtodo()
    {

    }

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
