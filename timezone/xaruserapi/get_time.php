<?php
// $Id:$

function timezone_userapi_get_time($args=array())
{
    extract($args); unset($args);
    if(!isset($timezone)) {
        $timezone = 'Etc/UTC';
    }
    $timezones = array();
    // get the tzdata
    include('modules/timezone/tzdata.php');
    
    $zone_num = null;
    if(!isset($Zones[$timezone])) {
        if(isset($Links[$timezone])) {
            $Zones[$timezone] =& $Links[$timezone];
        } else {
            return null;
        }
    }
    
    $num_zones = count($Zones[$timezone]);
    for($i=0; $i<$num_zones; $i++) {
        $z =& $Zones[$timezone][$i];
        // check for the existence of the UNTIL parameter
        if(isset($z[3])) {
            // get the right rule to use
            $offset =& getoffset($z[0]);
            $year = xarLocaleFormatUTCDate('%Y',time()-$offset);
            if($year <= $z[3]) {
                $zone_num = $i;
                break;
            }
        } else {
            // this is the one to use if no others match
            $zone_num = $i;
        }
    }
    
    $zone =& $Zones[$timezone][$zone_num];

    // free up some memory
    unset($Zones,$Leaps,$Links);
    
    //mydump($zone);
    // Zone::   GMTOFF	RULES	FORMAT	[UNTIL]
    // Rule::   FROM	TO	TYPE	IN	ON	AT	SAVE	LETTER
    // What rule are we supposed to use
    $zone_offset =& $zone[0];
    $zone_rule =& $zone[1];
    $zone_code =& $zone[2];
    $offset =& getoffset($zone_offset);
    $year = xarLocaleFormatUTCDate('%Y',time()-$offset);
    $month = xarLocaleFormatUTCDate('%m',time()-$offset);
    $day = xarLocaleFormatUTCDate('%d',time()-$offset);
    
    // create a pointer to the rules we need to check
    $rules =& $Rules[$zone_rule];
    $num_rules = count($rules);
    $rule_nums = array();
    for($i=0; $i<$num_rules; $i++) {
        (int) $min =& $rules[$i][0];
        $max =& $rules[$i][1];
        if($min >= $year && (int)$max <= $year) {
            // the year falls in this rule's range
            array_push($rule_nums,$i);
        } elseif(stristr($max,'max') && (int)$year >= $min) {
            // the year falls on or after this starting year
            array_push($rule_nums,$i);
        } elseif(stristr($max,'only') && (int)$year == $min) {
            // this year is equal to the year for this rule
            array_push($rule_nums,$i);
        }
    }
    
    
    $thisRule = null;
    $useRule = null;
    
    $rule_timestamps = array();
    
    $month = 10;
    foreach($rule_nums as $r) {
        $thisRule = $rules[$r];
        //mydump($thisRule);
        array_push($rule_timestamps,
                    array('id'=>$r,
                          'timestamp'=>makeTimeStampFromRule($thisRule,$offset),
                          'textdate'=>xarLocaleFormatUTCDate('%Y%m%d %H:%M:%S',makeTimeStampFromRule($thisRule,$offset))
                         ));
    }
    usort($rule_timestamps,'sortts');
    //mydump($rule_timestamps);
    if(!empty($rule_timestamps)) {
        $dst_start = $rule_timestamps[0]['timestamp'];
        $dst_end = $rule_timestamps[1]['timestamp'];
        // we should now have a valid range for STANDARD time
        // what time is it
        $now = time();
        if($now < $dst_end && $now > $dst_start) {
            // we're in DST
            $useRule = $rules[$rule_timestamps[0]['id']];
        } else {
            // we're in standard time
            $useRule = $rules[$rule_timestamps[1]['id']];
        }
        $offset += parseTime($useRule[6]);
    } //else {
        //$offset = 0;
    //}
    //mydump($useRule);  
    unset($Rules);
    
    
    return time()+$offset;
}

function sortts($a,$b) 
{
    if($a['timestamp'] < $b['timestamp']) return -1;
    elseif($a['timestamp'] > $b['timestamp']) return 1;
    elseif($a['timestamp'] == $b['timestamp']) return 0;
}

/**
 *  This function will take the tzdata rule information
 *  and convert it into a unix timestamp for later use
 *  It needs to take into account the time savings and offset
 */
function makeTimeStampFromRule(&$rule,&$offset)
{
    // what year is it currently in UTC
    $year = xarLocaleFormatUTCDate('%Y',time());
    
    // let us determine what month this rule is supposed to occur in.
    $months = array('Jan'=>01,'Feb'=>2,'Mar'=>3,'Apr'=>4,'May'=>5,'Jun'=>6,
                    'Jul'=>7,'Aug'=>8,'Sep'=>9,'Oct'=>10,'Nov'=>11,'Dec'=>12);
    $month = $months[$rule[3]];
    
    // let us determine what day this rule occurs on
    $days = array('Sun'=>0,'Mon'=>1,'Tue'=>2,'Wed'=>3,'Thu'=>4,'Fri'=>5,'Sat'=>6);
    if(stristr($rule[4],'lastsun')) {
        // this rule executes on the last sunday
        $lastDay = date('t',gmmktime(0,0,0,$month,1,$year));
        $lastDayIs = date('w',gmmktime(0,0,0,$month,$lastDay,$year));
        $day = $lastDay - ($lastDayIs + 1);
    } elseif(strpos($rule[4],'>=') > 0) {
        // we need to parse it a bit more
        $args = explode('>=',$rule[4]);
        // what weekday are we looking for
        $weekDay =& $days[$args[0]];
        // on or after this date
        $onOrAfter =& $args[1];
        // what day of the week is the date from the parameter
        $weekDayIs = date('w',gmmktime(0,0,0,$month,$onOrAfter,$year));
        if($weekDay == $weekDayIs) {
            // if the weekDay ==  weekDayIs
            $day = $onOrAfter;
        } elseif($weekDay < $weekDayIs) {
            // if the weekDay is before the current weekDayIs
            $diff = 6 - $weekDayIs - $weekDay;
            $day = $onOrAfter + $diff;
        } else {
            // the weekDay is after the weekDayIs
            $diff = $weekDayIs - $weekDay;
            $day = $onOrAfter + $diff;
        }
    } else {
        $day = $rule[4];
    }
    
    // get the hours to feed to the mktime function.
    $time = parseTime($rule[5]);
    $savings = parseTime($rule[6]);
    $hours = floor($time/60/60);
    $remainder = $time % (60*60);
    $minutes = floor($remainder/60);
    
    // Ok, we need to create a unix timestamp to return.
    $timestamp = gmmktime($hours,$minutes,0,$month,$day,$year);
    // apply the rule's base gmt offset
    $timestamp -= $offset;
    // we need to compensate for possible DST on the server - damn date time bullshit!
    $inDST=date('I',$timestamp);
    if((bool)$inDST) {
        $timestamp -= $savings;
    }
    return $timestamp;
}

function parseTime($in)
{
    if($in <= 0) { return 0; }
    $in = explode(':',$in);
    $hours =& $in[0];
    $minutes =& $in[1];
    $seconds = $hours * 60 * 60;
    $seconds += $minutes * 60;
    return $seconds;
}

function parseStartTime($in)
{
    if($in <= 0) { return array(0,0); }
    return explode(':',$in);
}

function getoffset($in)
{
    if(!is_numeric($in)) {
        $offset = xarModAPIFunc('timezone','user','parseoffset',$in);
        return $offset['total'];
    } else {
        return $in;
    }
}

?>
