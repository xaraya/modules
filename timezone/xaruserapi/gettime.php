<?php
// $Id:$

// some Globals for later
$SecsPerMin  = 60;
$MinsPerHour = 60;
$HoursPerDay = 24;
$SecsPerHour = $SecsPerMin * $MinsPerHour;
$SecsPerDay  = $SecsPerHour * $HoursPerDay;
$MonthLens   = array(array(31,28,31,30,31,30,31,31,30,31,30,31),
                     array(31,29,31,30,31,30,31,31,30,31,30,31));

/**
 *  isLeap helps determine if the year we're evaluating is a leap year
 */
function isLeap($y)
{
    return (($y%4) == 0 && (($y%100) != 0 || ($y%400) == 0));
}

/*
function parseTime($in)
{
    if((int)$in <= 0) { return 0; }
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

function getoffset($offset)
{
    // check to see if it's a number already
    if(is_int($offset)) {
        return $offset;
    } else {
        $seconds = xarModAPIFunc('timezone','user','parseoffset',array('offset'=>$offset));
        return $seconds['total'];
    }
}

// determines the type of time calculation to perform
function timeType($in)
{
    switch(substr($in,-1)) {
        case 'u':
            return 'u';
            break;
        case 's':
            return 's';
            break;
        default:
            return 'w';
    }
}
*/
function compareTime(&$obj,$y,$m,$d,$h,$mi,$s,$w)
{
    global $MonthLens;
    global $SecsPerHour;
    global $SecsPerMin;

    if($m < $obj->month) { return -1; }
    if($m > $obj->month) { return 1; }
    
    $ml = $MonthLens[isleap($y)][$m-1];
    $onday = getOnDay($obj,$y);
    if($obj->onday < 0) { return -1; }
    if($obj->onday > 0) { return 1; }
    
    # check the time
    $tod = $h*$SecsPerHour + $mi*$SecsPerMin + $s;
    if($tod < $obj->tod) { return -1; }
    if($tod > $obj->tod) { return 1; }
    
    return 0;
}

function dayValue($dayname)
{
    $days = array('sun'=>0,'mon'=>1,'tue'=>3,'wed'=>4,'thu'=>5,'fri'=>6,'sat'=>7);
    return $days[strtolower($dayname)];
}

// parses the rule's day data and returns the actual date
// requires us to pass in the current year we're processing
function getOnDay(&$rule,$y)
{
    // posible values
    // integer
    // last[Mon,Tue,Wed,Thu,Fri,Sat,Sun]
    // [Mon,Tue,Wed,Thu,Fri,Sat,Sun]>=#
    // [Mon,Tue,Wed,Thu,Fri,Sat,Sun]<=#
    
    // if we have an integer, just return it
    if(is_int($rule->onday)) { return $rule->onday; }
    
    // check to see if we're looking for the last day and find it
    if(substr($rule->onday,0,4) == 'last') {
        $dow = dayValue(substr($rule->onday,-3));
        // what's the last day of the month
        $ld = gmdate('t',gmmktime(0,0,0,$rule->month,1,$y));
        // what day of the week is the last day?
        $ldow = gmdate('w',gmmktime(0,0,0,$rule->month,$ld,$y));
        if($dow < $ldow) {
            // the day of the week we're looking for is greater than the last day
            $diff = $ldow - $dow;
            $day = $ldow - $diff;
        } elseif($dow > $ldow) {
            // the day of the week we're looking for is less than the last day
            $diff = 7 - $dow + $ldow;
            $day = $ldow - $diff;
        }
        // return the day
        return $day;
    }
    
    if(strstr($rule->onday,'>=')) {
        $type = 'gte';
        $parts = explode('>=',$rule->onday);        
    } elseif(strstr($rule['on'],'<=')) {
        $type = 'lte';
        $parts = explode('<=',$rule->onday);
    }
    
    // ok, we're looking for a day On, Before or After a certain day of the month
    $lf = dayValue($parts[0]); // looking for [0-6, Sun-Sat]
    $on = $parts[1]; // on|before|after this date
    $ondow = gmdate('w',gmmktime(0,0,0,$rule->month,$on,$y));
    // if the day of week we're looking for matches 
    // the day of the week for the on,before,after day
    // return the $on day
    if($ondow == $lf) {
        return $on;
    }
    // otherwise we need to do some simple math.
    switch($type) {
        case 'gte':
            if($lf > $ondow) {
                return $on + ($lf - $ondow);
            } elseif( $lf < $ondow) {
                
            }
            break;
            if($lf < $ondow) {
                return $on - ($ondow - $lf);
            } elseif( $lf > $ondow) {
                
            }
        case 'lte':
            break;
    }
}

function getTimeVars($timestamp)
{
    $data = explode('|',gmdate('Y|m|d|H|i|s|w|z|I',$timestamp));
    return $data;
}

function findRule(&$rules,$uSecs,$sSecs)
{
    $useRule = NULL;
    $yearData = array(array(),array(),array());
    
    // figure out which rules may apply to this time
    list($saveY,$m,$d,$h,$mi,$s,$w,$J,$D) = getTimeVars($sSecs);
    foreach($rules as $r) {
        // last year array
        $y = $saveY - 1;
        if($y >= $r['from'] && $y <= $r['to']) {
            $yearData[0][] = $r;
        }
        // next year array
        $y = $saveY + 1;
        if($y >= $r['from'] && $y <= $r['to']) {
            $yearData[2][] = $r;
        }
        // this year array
        $y = $saveY;
        if($y >= $r['from'] && $y <= $r['to']) {
            $yearData[1][] = $r;
        }
    }
    
    // The lastYear, thisYear, and nextYear arrays now have
    // the rules that could apply to the given time. Figure
    // out which one it is - assume that wall time is the
    // same as standard time when we enter the loop
    $wSecs = $sSecs;
    $lastRule = NULL;
    for($i=0; $i<3;$i++)
    {
        foreach($yearData[$i] as $r) {
            if(isset($useRule)) {
                // get out of here if we have a rule to use
                break;
            }
            $doSecs = $uSecs;
            if(timeType($r['at']) == 's') {
                $doSecs = $sSecs;
            } elseif(timeType($r['at']) == 'w') {
                $doSecs = $wSecs;
            }
            list($saveY,$m,$d,$h,$mi,$s,$w,$J,$D) = getTimeVars($doSecs);
            if($y > ($saveY + $i -1)) {
                $wSecs = $sSecs + parseTime($r['save']);
            } elseif($y == ($saveY + $i -1)) {
                $ct = compareTime($r,$y,$m,$d,$h,$mi,$s,$w);
                if($ct > 0) {
                    $wSecs = $sSecs  + parseTime($r['save']);
                } elseif($ct == 0) {
                    $useRule = $r;
                } else {
                    $useRule = $lastRule;
                }
            } else {
                $useRule = $lastRule;
            }
            $lastRule = $r;
            if(isset($useRule)) {
                // get out of here if we have a rule to use
                break;
            }
        }
    }
    return $useRule;
}
/*
// $zone = timezone data
// $secs = seconds since the epoch
function zonetime(&$zone, $secs)
{
    global $MonthLens;
    
    $useZone = NULL;
    $useRule = NULL;
    $uSecs = $secs;
    // find the zoneData structure for the zone that
    // matches the secs argument that was given.
    // After that, find the rule that is applicable
    foreach($zones as $z) {
    
        if(!isset($z->year)) {
            $useZone = $z;
            break;
        }
        // if there are rules for this zone entry then go
        // through and figure out if any of them apply
        // we need to do this because whoever came up
        // with the crazy idea of allowing wall-clock
        // time to be used in the UNTIL section of the
        // zone entries means that we have to figure out
        // what rules are in effect for a zone before
        // we can figure out what the wall time for that
        // zone is, and after the wall time is calculated
        // THEN we can look at whether this rule is in
        // effect.
        // Of course, this is only required if the UNTIL
        // time uses wall time.  Otherwise, we are fine
        // with just the rule info...
        $sSecs = $uSecs + $z->gmtoff;
        $wSecs = $sSecs;
        if($z->todcode == 'w') {
            // what does this do, exactly???
            if(!is_array($z->rules)) {
                $wSecs = $sSecs + $z->rules;
            } else {
                $useRule = findRule($z,$uSecs,$sSecs)
                $wSecs = $sSecs + $useRule->stdoff;
            }
        }
        // now look to see if the zone entry matches
        $doSecs = $uSecs;
        if($z->todcode == 's') {
            $doSecs = $sSecs;
        } elseif($z->todcode == 'w') {
            $doSecs = $wSecs;
        }
        list($y,$m,$d,$h,$mi,$s,$w,$J,$D) = getTimeVars($doSecs);
        $ml = $MonthLens[isLeap($y)][$m-1];
        if($y < $z->year || ($y == $z->year && compareTime($z,$y,$m,$d,$h,$mi,$s,$w) <= 0)) {
            $useZone = $z;
            break;
        }
    }
    // at this point we should have a zone entry that
    // matches in the useZone variable.  We may also
    // have a rule that matches in useRule
    if(!isset($useZone)) {
        // raise an error xarML('No valid zone entry found for given zone');
        return false;
    }
    
    $sSecs = $uSecs + $useZone->gmtoff;
    $wSecs = $sSecs;
    
}
*/
// NOTE:: If we do not get a timestamp passed in, 
// we need to call time() right before returning
function timezone_userapi_getTime($args=array())
{
    extract($args); unset($args);
    if(!isset($timezone)) {
        $timezone = 'Etc/UTC';
    }
    if(!isset($timestamp)) {
        $timestamp = NULL;
    }
    
    $timezoneData =& xarModAPIFunc('timezone','user','getTimezoneData',array('timezone'=>$timezone));
    
    echo '<pre>'; var_dump($timezoneData); echo '</pre>';
    return true;
    
    //======================================================================
    //  Determine the rule sets we will be using
    //======================================================================
    $rules = array();
    for($i=0, $max=count($timezoneData); $i<$max; $i++) {
        $tz =& $timezoneData[$i]; // just making it a bit nicer to read
        $offset = getoffset($tz['offset']);
        // year using this default offset (probably not necessary)
        $year = xarLocaleFormatUTCDate('%Y',time()-$offset);
        // if we have an until year see if we're in range
        if(isset($tz['untilyear'])) {
            if($year <= $tz['untilyear']) {
                // we can include this one
                $rules[] =& $tz;
            } else {
                // we just go to the next rule
                continue;
            }
        } else {
            // we need to check the from and to range
            if(!isset($tz['from'])) {
                // this zone only has one setting
                $rules[] =& $tz;
            } elseif($year >= $tz['from']) {
                // ok, we know the from year is correct
                if($tz['to'] == 'only') {
                    if($year == $tz['from']) {
                        // this is an ONLY year?
                        $rules[] =& $tz;
                    } else {
                        // we just move along
                        continue;
                    }
                } elseif($year <= $tz['to'] || $tz['to'] == 'max') {
                    // this year false on or before the to year or it's a MAX range
                    $rules[] =& $tz;
                }
            }
        }
    }
    //  if the rules array is empty, chances are we want to pump in the last 
    //  valid timezone definition and ignore the ST=>DST=>ST rules
    //  TODO : <iansym> confirm this
    if(empty($rules)) {
        $rules[0] = $tz;
        // remove the dst rules 
        $rules[0]['from'] = NULL;
        $rules[0]['to']   = NULL;
        $rules[0]['in']   = NULL;
        $rules[0]['on']   = NULL;
        $rules[0]['at']   = NULL;
        $rules[0]['save'] = NULL;
    }
    
    // this value should be a reference to the zone we're using
    //echo '<pre>'; print_r($rules); echo '</pre>';
    //return ;
    
    //======================================================================
    //  Determine the rule we will be using 
    //  (possibly wrap this into the above)
    //
    //  we need to determine what rule is the correct one to use
    //  the following parameters will determine this
    //
    //  in = the month it occurs in
    //  on = what day it occurs on
    //  at = what time the transition happens
    //
    //  if we don't have any of this information, then we should just apply
    //  the rule as long as there are no other possible rules.  In which 
    //  case we need to look at something else (not sure what yet)
    //======================================================================
    
    $thisRule = null;
    $useRule = null;
    $offset = 0;
    $rule_timestamps = array();
    // let's get a count of the rules we have
    $numRules = count($rules);
    
    if($numRules == 0) {
        // we don't have a rule to apply, return the timestamp
        if(isset($timestamp)) {
            return $timestamp;
        } else {
            return time();
        }
    } elseif($numRules == 1) {
        // we only have one rule to use, so we should apply the offset
        return calculateTime($rules[0],$timestamp);
    }
    
    // if we've gotten this far, we have more to do
    // we have more than one rule to check...
    // we need to determine which rule to use by determining
    // which rule the current timestamp relates to the most
    // not the simplest of tasks.
    
    // figure out what month we're in
    if(isset($timestamp)) {
        $inyear = gmdate('Y',$timestamp);
        $inmonth = gmdate('m',$timestamp);
    } else {
        $inyear = gmdate('Y',time());
        $inmonth = gmdate('m',time());
    }
    
    // sort the rules by the IN month - this should help us make a range
    usort($rules,'sortts');
    
    
    
    // go through the rules and see when the time trigger is
    for($i=0; $i<$numRules; $i++) {
            $rules[$i]['utctrigger'] = makeTimeStampFromRule($rules[$i]);
            $rules[$i]['utctrigger_text'] = xarLocaleFormatUTCDate('%Y%m%d %H:%M:%S',$rules[$i]['utctrigger']);
    }
    echo '<pre>'; print_r($rules); echo '</pre>';
    return 0;
    
    //$month = 10;
    foreach($rules as $key=>$rule) {
        //mydump($thisRule);
        $rule_timestamp = makeTimeStampFromRule($rule);
        $rule_timestamps[] = array('id'=>$key,
                                   'timestamp'=>$rule_timestamp,
                                   'textdate'=>xarLocaleFormatUTCDate('%Y%m%d %H:%M:%S',$rule_timestamp)
                                  );
    }
    
    usort($rule_timestamps,'sortts');
    echo '<pre>'; print_r($rule_timestamps); echo '</pre>';
    
    if(!empty($rule_timestamps)) {
        $dst_start = $rule_timestamps[0]['timestamp'];
        $dst_end = $rule_timestamps[1]['timestamp'];
        // we should now have a valid range for STANDARD time
        // what time is it
        if(isset($timestamp)) {
            $now = $timestamp;
        } else {
            $now = time();
        }
        if($now < $dst_end && $now > $dst_start) {
            // we're in DST
            $useRule = $rules[$rule_timestamps[0]['id']];
        } else {
            // we're in standard time
            $useRule = $rules[$rule_timestamps[1]['id']];
        }
        $offset = getoffset($useRule['offset']);
    } else {
        $offset = 0;
    }
    
    var_dump($useRule);  
    
    if(isset($timestamp)) {
        return $timestamp + $offset;
    } else {
        return time()+$offset;
    }
}

function calculateTime(&$rule,$timestamp=NULL)
{
    $offset = getoffset($rule['offset']);
    $save   = parseTime($rule['save']);
    
    if(isset($timestamp)) {
        return $timestamp+$offset-$save;
    } else {
        return time()+$offset-$save;
    }
}

function sortts($a,$b) 
{
    if($a['in'] < $b['in']) return -1;
    elseif($a['in'] > $b['in']) return 1;
    elseif($a['in'] == $b['in']) return 0;
}

/**
 *  This function will take the tzdata rule information
 *  and convert it into a unix timestamp for later use
 *  It needs to take into account the time savings and offset
 */

function makeTimeStampFromRule(&$rule)
{
    // what year is it currently in UTC
    $year = xarLocaleFormatUTCDate('%Y',time());
    
    // check for actual set values
    // if the values are not set we can just return something
    
    $month = $rule['in'];
    
    // let us determine what day this rule occurs on
    $days = array('Sun'=>0,'Mon'=>1,'Tue'=>2,'Wed'=>3,'Thu'=>4,'Fri'=>5,'Sat'=>6);
    if(isset($rule['on'])) {
        if(stristr($rule['on'],'lastsun')) {
            // this rule executes on the last sunday
            $lastDay = date('t',gmmktime(0,0,0,$month,1,$year));
            $lastDayIs = date('w',gmmktime(0,0,0,$month,$lastDay,$year));
            $day = $lastDay - ($lastDayIs + 1);
        } elseif(strpos($rule['on'],'>=') > 0) {
            // we need to parse it a bit more
            $args = explode('>=',$rule['on']);
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
            $day = $rule['on'];
        }
    } else {
        $day = 0;
    }
    
    // get the hours to feed to the mktime function.
    $time = isset($rule['at']) ? parseTime($rule['at']) : 0;
    $savings = isset($rule['save']) ? parseTime($rule['save']) : 0;
    $hours = floor($time/60/60);
    $remainder = $time % (60*60);
    $minutes = floor($remainder/60);
    
    // Ok, we need to create a unix timestamp to return.
    $timestamp = gmmktime($hours,$minutes,0,$month,$day,$year);
    // apply the rule's base gmt offset and savings for this rule
    $timestamp -= getoffset($rule['offset']) + $savings;
    // we need to compensate for possible DST on the server - damn date time bullshit!
    $inDST=date('I',$timestamp);
    if((bool)$inDST) {
        $timestamp -= $savings;
    }
    return $timestamp;
}


?>