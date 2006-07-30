<?php

/**
 * Find the rules that apply for a particular timestamp
 *
 * @param $args['rules'] array the list of rules we're looking at, and
 * @param $args['offset'] integer the timezone offset that applies, or
 * @param $args['timezone'] string the timezone we're looking for
 * @param $args['timestamp'] integer the time period we're interested in
 * @return array Array of (dst start time, dst end time, dst offset, std code, dst code, dst start rule, dst end rule)
 */
function timezone_userapi_findrules($args=array())
{
    extract($args);
    if (empty($timestamp)) {
        $timestamp = time();
    }
    if (empty($rules)) {
        if (empty($timezone)) {
            $timezone = 'Etc/UTC';
        }
        $zone = xarModAPIFunc('timezone','user','findzone',
                               array('timezone'  => $timezone,
                                     'timestamp' => $timestamp));
        if (empty($zone)) {
            return array();
        }
        $rules =& $zone->rules;
        $offset = $zone->gmtoff;
    }

    // $sSecs contains the gmt offset for standard time
    // for the zone we're perfoming this lookup on

    $useRule = NULL;

    // determine the current year for this zone's standard time
    $year = gmdate('Y',$timestamp);

    // some simple things to hopefully figure out when DST starts and ends for this zone
    $dst_start = null;
    $dst_start_offset = null;
    $dst_start_rule = null;

    $dst_end   = null;
    $dst_end_rule = null;

    foreach($rules as $r)
    {
        if($r->hiyear == 'only') {
            $hiyear = $r->loyear;
        } elseif($r->hiyear == 'max') {
            // just set this year to something in the immediate future
            $hiyear = $year+10;
        } else {
            $hiyear = $r->hiyear;
        }

        // ok, let's check the rules for possible candidates

        if($r->loyear <= $year && $hiyear >= $year) {
            // this rule is a possible candidate
            // what we need to determineis if the zone this rule
            // applies to is currently in DST.  This is not a
            // particularly easy task.

            if($r->stdoff != 0) {
                // this rule belongs to a DST rule.
                // calculate the year this rule would be in
                $year = gmdate('Y',($timestamp - ($offset - $r->stdoff)));
                if($r->loyear <= $year && $hiyear >= $year) {
                    // if we still fall in this date range we can continue processing
                } else {
                    // we need to check the next rule
                    continue;
                }

                // since we're in the DST ruleset, let's see if the zone's date fits

                $dst_start = gmmktime(0,0,0,$r->month,timezone_findrules_getOnDay($r,$year),$year);
                $dst_start += $r->tod;
                if ($r->todcode == 'w' || $r->todcode == 's') {
                    // back to UTC time
                    $dst_start -= $offset;
                }
                $dst_start_offset = $r->stdoff;
                $dst_start_rule = $r;
            } else {
                $dst_end = gmmktime(0,0,0,$r->month,timezone_findrules_getOnDay($r,$year),$year);
                $dst_end += $r->tod;
                if ($r->todcode == 'w' || $r->todcode == 's') {
                    // back to UTC time
                    $dst_end -= $offset;
                }
                $dst_end_rule = $r;
            }
        }
    }
    if ($dst_end_rule->todcode == 'w') {
        // small fix to dst end
        $dst_end -= $dst_start_offset;
    }
    // Note: this function returns start and end dates in UTC
    return array($dst_start,$dst_end,$dst_start_offset,$dst_end_rule->addrvar,$dst_start_rule->addrvar,$dst_start_rule,$dst_end_rule);
}

function timezone_findrules_dayValue($dayname)
{
    $days = array('sun'=>0,'mon'=>1,'tue'=>3,'wed'=>4,'thu'=>5,'fri'=>6,'sat'=>7);
    return $days[strtolower($dayname)];
}

// parses the rule's day data and returns the actual date
// requires us to pass in the current year we're processing
function timezone_findrules_getOnDay(&$rule,$y)
{
    // posible values
    // integer
    // last[Mon,Tue,Wed,Thu,Fri,Sat,Sun]
    // [Mon,Tue,Wed,Thu,Fri,Sat,Sun]>=#
    // [Mon,Tue,Wed,Thu,Fri,Sat,Sun]<=#

    // if we have an integer, just return it
    if(is_numeric($rule->onday)) { return $rule->onday; }

    // check to see if we're looking for the last day and find it
    if(substr($rule->onday,0,4) == 'last') {
        $dow = timezone_findrules_dayValue(substr($rule->onday,-3));
        // what's the last day of the month
        $ld = gmdate('t',gmmktime(0,0,0,$rule->month,1,$y));
        // what day of the week is the last day?
        $ldow = gmdate('w',gmmktime(0,0,0,$rule->month,$ld,$y));
        if($dow == $ldow) {
            return $ld;
        } elseif($dow < $ldow) {
            // the day of the week we're looking for is greater than the last day
            $diff = $ldow - $dow;
            $day = $ld - $diff;
        } elseif($dow > $ldow) {
            // the day of the week we're looking for is less than the last day
            $diff = 7 - $dow + $ldow;
            $day = $ld - $diff;
        }
        // return the day
        return $day;
    }

    if(strstr($rule->onday,'>=')) {
        $type = 'gte';
        $parts = explode('>=',$rule->onday);
    } elseif(strstr($rule->onday,'<=')) {
        $type = 'lte';
        $parts = explode('<=',$rule->onday);
    }

    // ok, we're looking for a day On, Before or After a certain day of the month
    $lf = timezone_findrules_dayValue($parts[0]); // looking for [0-6, Sun-Sat]
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
                return $on + (7-$ondow+$lf);
            }
            break;
        case 'lte':
            if($lf < $ondow) {
                return $on - ($ondow - $lf);
            } elseif( $lf > $ondow) {
                return $on - (7-$lf+$onday);
            }
            break;
    }
}

?>