<?php
// $Id:$

/**
 *  Class structure to hold the Rule Data for a Time Zone 
 */
class RuleData
{
    function RuleData($loyear,$hiyear,$yrtype,$month,$onday,$tod,$todcode,$stdoff,$abbrvar)
    {
        $this->loyear  =& $loyear;
        $this->hiyear  =& $hiyear;
        $this->yrtype  =& $yrtype;
        $this->month   =& $month;
        $this->onday   =& $onday;
        $this->tod     =& $tod;
        $this->todcode =& $todcode;
        $this->stdoff  =& $stdoff;
        $this->addrvar =& $abbrvar;
    }
}

/**
 *  Class structure to hold the TimeZone Data
 */
class ZoneData
{
    function ZoneData($gmtoff,&$rules,$format,$year,$month,$onday,$tod,$todcode)
    {
        $this->gmtoff  =& $gmtoff;
        $this->rules   =& $rules;
        $this->format  =& $format;
        $this->year    =& $year;
        $this->month   =& $month;
        $this->onday   =& $onday;
        $this->tod     =& $tod;
        $this->todcode =& $todcode;
    }
}

// helper function to parse the HH:MM time values and return seconds
function parseTime($in)
{
    if((int)$in <= 0) { return 0; }
    $in = preg_replace('/[a-z]$/i','',$in); // remove any letters at the end of the time
    list($hours,$minutes) = explode(':',$in);
    $seconds = ($hours * 60 * 60) + ($minutes * 60);
    return $seconds;
}
// helper function to parse the offset and return seconds
function getOffset($offset)
{
    // check to see if it's a number already
    if(is_int($offset)) {
        return $offset;
    } else {
        $seconds = xarModAPIFunc('timezone','user','parseoffset',array('offset'=>$offset));
        return $seconds['total'];
    }
}
// helper function to determine the time type
function timeType($in)
{
    switch(substr($in,-1)) {
        case 'u':
        case 'g':
        case 'z':
            return 'u';
            break;
        case 's':
            return 's';
            break;
        default:
            return 'w';
    }
}

function &timezone_userapi_getTimezoneData($args=array())
{
    static $timezoneData; // static var to store the timezone data once we grab it
    // initialize the timezoneData container
    if(!isset($timezoneData)) { $timezoneData = array(); }
    extract($args); unset($args);
    // TODO::Pull this from the default/site timezone
    if(!isset($timezone)) { 
        // TODO : return a user exception, we need a timezone name here
        xarExceptionSet(
            XAR_USER_EXCEPTION,
            xarML('This API function must be called with a timezone name.'),
            NULL
            );
    }
    // if this timezone data has not been loaded, grab it from the db and store it
    if(!isset($timezoneData[$timezone])) {
        
        $dbconn =& xarDBGetConn();
        $zoneID = null;
        $tables =& xarDBGetTables();
        
        // prep and quote the timezone var for the query
        $timezone = $dbconn->qstr($timezone,get_magic_quotes_gpc());
        
        // check the zones table first for the timezone
        $sql = "SELECT DISTINCT id
                FROM $tables[timezone_zones] 
                WHERE name = $timezone";
        $result =& $dbconn->SelectLimit($sql,1);
        if(!$result || $result->EOF) {
            // see if we have this in the links table
            $sql = "SELECT DISTINCT zl.zones_id
                    FROM $tables[timezone_links] AS l, 
                         $tables[timezone_zones_has_links] AS zl
                    WHERE l.id = zl.links_id 
                    AND l.name = $timezone";
            $result =& $dbconn->SelectLimit($sql,1);
            if(!$result || $result->EOF) {
                //TODO::Throw an User Exception - no valid timezone found
                return false;
            } else {
                // set up the next data pull
                $zoneID = $result->fields[0];
                $result->Close();
            }
        } else {
            // set up the next data pull
             $zoneID = $result->fields[0];
             $result->Close();
        }
        
        /* test sql for grabbing the timezone data 
        SELECT zd.id, zd.gmtoff, zd.format, zd.untilyear, 
               zd.untilmonth, zd.untilday, zd.untiltime,
               rd.rule_from, rd.rule_to, rd.rule_type,
               rd.rule_in, rd.rule_on, rd.rule_at,
               rd.rule_save, rd.rule_letter
        FROM xar_timezone_zones_data AS zd
        WHERE zd.zones_id = 132;
        */
        
        /* test sql for grabbing the rules for each zone
        SELECT rd.rule_from, rd.rule_to, rd.rule_type,
               rd.rule_in, rd.rule_on, rd.rule_at,
               rd.rule_save, rd.rule_letter
        FROM xar_timezone_zones_data_has_rules AS zdr
        LEFT JOIN xar_timezone_rules AS r ON r.id = zdr.rules_id
        LEFT JOIN xar_timezone_rules_data AS rd ON rd.rules_id = r.id
        WHERE zdr.zones_data_id = 588;
        
        */
        
        // Ok, we need to grab the data from the zones_data and rules_data tables
        $zonesql = "SELECT zd.id, zd.gmtoff, zd.rules, zd.format, zd.untilyear, 
                        zd.untilmonth, zd.untilday, zd.untiltime
                    FROM $tables[timezone_zones_data] AS zd
                    WHERE zd.zones_id = $zoneID";
        $resultZones =& $dbconn->Execute($zonesql);
        if(!$resultZones || $resultZones->EOF) {
            // we didn't find any zones, probably an error
            xarExceptionSet(
                XAR_USER_EXCEPTION,
                xarML('No TimeZone data was found'),
                NULL
                );
        }       
        
        // construct the timezoneData array
        for(; !$resultZones->EOF; $resultZones->MoveNext()) {
            unset($Rules);
            if(!isset($timezoneData[$timezone])) { 
                $timezoneData[$timezone] = array(); 
            }
            if(!preg_match('/[\d]+:[\d]+/',$resultZones->fields[2])) {
                // grab the rules for this zone
                $zoneDataID = $resultZones->fields[0];
                $rulessql = "SELECT rd.rule_from, rd.rule_to, rd.rule_type,
                                rd.rule_in, rd.rule_on, rd.rule_at,
                                rd.rule_save, rd.rule_letter
                             FROM $tables[timezone_zones_data_has_rules] AS zdr
                             LEFT JOIN $tables[timezone_rules] AS r ON r.id = zdr.rules_id
                             LEFT JOIN $tables[timezone_rules_data] AS rd ON rd.rules_id = r.id
                             WHERE zdr.zones_data_id = $zoneDataID";
                $resultRules =& $dbconn->Execute($rulessql);
                if(!$resultRules) {
                    // sql error
                    return false;
                }
            
                // container array for Rules
                $Rules = array();
                for(; !$resultRules->EOF; $resultRules->MoveNext()) {
                    $Rules[] =& new RuleData(
                                    $resultRules->fields[0],            // from (loyear)
                                    $resultRules->fields[1],            // to (hiyear)
                                    $resultRules->fields[2],            // type (yrtype = odd, even, uspres, nonpres|nonuspres, -)
                                    $resultRules->fields[3],            // in (month)
                                    $resultRules->fields[4],            // on (onday) 
                                    parseTime($resultRules->fields[5]), // at (tod) (return seconds)
                                    timeType($resultRules->fields[5]),  // todcode 
                                    parseTime($resultRules->fields[6]), // save (stdoff) convert this to seconds
                                    $resultRules->fields[7]             // letter (abbrvar)
                                    );
                }
            } else {
                $Rules = $resultZones->fields[2];
            }
            // create the zone object for this data and put it in the container
            $timezoneData[$timezone][] =& new ZoneData(
                                                getOffset($resultZones->fields[1]), // offset (gmtoff)
                                                $Rules,                             // rules
                                                $resultZones->fields[3],            // format (format)
                                                $resultZones->fields[4],            // untilyear (year)
                                                $resultZones->fields[5],            // untilmonth (month)
                                                $resultZones->fields[6],            // untilday (onday)
                                                parseTime($resultZones->fields[7]), // untiltime (tod)
                                                timeType($resultZones->fields[7])   // todcode
                                                ); 
            
            $resultRules->Close();
            // create a simple reference to make the code more manageable
        }
    }
    return $timezoneData[$timezone];
}  
?>