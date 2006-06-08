<?php

/**
 * calculate the next run time for a cron-like interval
 * 
 * @param  $args array specifying the custom interval
 * @returns timestamp
 */
function scheduler_userapi_nextrun($args = array())
{
    if (empty($args)) return 1;

    $newminutes = array();
    if (isset($args['minute']) && $args['minute'] !== '') {
        $oldminutes = explode(',',$args['minute']);
        foreach ($oldminutes as $minute) {
            if (preg_match('/^(\d+)-(\d+)$/',$minute,$matches)) {
                $j = $matches[1];
                $k = $matches[2];
                for ($i = $j; $i <= $k; $i++) {
                    $newminutes[] = $i;
                }
            } elseif (is_numeric($minute)) {
                $newminutes[] = $minute;
            }
        }
    }
    if (empty($newminutes)) {
        $newminutes = range(0, 59);
    }
    sort($newminutes,SORT_NUMERIC);

    $newhours = array();
    if (isset($args['hour']) && $args['hour'] !== '') {
        $oldhours = explode(',',$args['hour']);
        foreach ($oldhours as $hour) {
            if (preg_match('/^(\d+)-(\d+)$/',$hour,$matches)) {
                $j = $matches[1];
                $k = $matches[2];
                for ($i = $j; $i <= $k; $i++) {
                    $newhours[] = $i;
                }
            } elseif (is_numeric($hour)) {
                $newhours[] = $hour;
            }
        }
    }
    if (empty($newhours)) {
        $newhours = range(0, 23);
    }
    sort($newhours,SORT_NUMERIC);

    $newdays = array();
    if (!empty($args['day'])) {
        $olddays = explode(',',$args['day']);
        foreach ($olddays as $day) {
            if (preg_match('/^(\d+)-(\d+)$/',$day,$matches)) {
                $j = $matches[1];
                $k = $matches[2];
                for ($i = $j; $i <= $k; $i++) {
                    $newdays[] = $i;
                }
            } elseif (is_numeric($day)) {
                $newdays[] = $day;
            }
        }
    }
    // we don't pre-fill the days here
    sort($newdays,SORT_NUMERIC);

    $newmonths = array();
    if (!empty($args['month'])) {
        $oldmonths = explode(',',$args['month']);
        foreach ($oldmonths as $month) {
            if (preg_match('/^(\d+)-(\d+)$/',$month,$matches)) {
                $j = $matches[1];
                $k = $matches[2];
                for ($i = $j; $i <= $k; $i++) {
                    $newmonths[] = $i;
                }
            } elseif (is_numeric($month)) {
                $newmonths[] = $month;
            }
        }
    }
    if (empty($newmonths)) {
        $newmonths = range(1, 12);
    }
    sort($newmonths,SORT_NUMERIC);

    $newweekdays = array();
    if (isset($args['weekday']) && $args['weekday'] !== '') {
        $oldweekdays = explode(',',$args['weekday']);
        foreach ($oldweekdays as $weekday) {
            if (preg_match('/^(\d+)-(\d+)$/',$weekday,$matches)) {
                $j = $matches[1];
                $k = $matches[2];
                for ($i = $j; $i <= $k; $i++) {
                    $newweekdays[] = $i;
                }
            } elseif (is_numeric($weekday)) {
                $newweekdays[] = $weekday;
            }
        }
    }
    // we don't pre-fill the weekdays here
    sort($newweekdays,SORT_NUMERIC);

    // next tick is 60 seconds away in cron terms
    $now = time() + 60;
    $info = getdate($now);
    $curyear = $info['year'];
    $curmonth = $info['mon'];
    $curday = $info['mday'];
    $curhour = $info['hours'];
    $curminute = $info['minutes'];
    $cursecond = $info['seconds'];
    $curweekday = $info['wday'];

    // get the next of current month
    if (in_array($curmonth, $newmonths)) {
        // get the next of current day
        if ((empty($newdays) || in_array($curday,$newdays)) &&
            (empty($newweekdays) || in_array($curweekday,$newweekdays))) {
            // get the next of current hour
            if (in_array($curhour,$newhours)) {
                foreach ($newminutes as $nextminute) {
                     if ($nextminute >= $curminute) {
                         return mktime($curhour,$nextminute,$cursecond,$curmonth,$curday,$curyear);
                     }
                }
            }
            // get the first of next hour
            foreach ($newhours as $nexthour) {
                if ($nexthour > $curhour) {
                    $nextminute = array_shift($newminutes);
                    return mktime($nexthour,$nextminute,$cursecond,$curmonth,$curday,$curyear);
                }
            }
        }
        // get the first of next day
        $maxday = date('t', mktime(0,0,0,$curmonth,$curday,$curyear));
        for ($nextday = $curday + 1; $nextday <= $maxday; $nextday++) {
            $nextweekday = ($curweekday + $nextday - $curday) % 7;
            if ((empty($newdays) || in_array($nextday,$newdays)) &&
                (empty($newweekdays) || in_array($nextweekday,$newweekdays))) {
                $nexthour = array_shift($newhours);
                $nextminute = array_shift($newminutes);
                return mktime($nexthour,$nextminute,$cursecond,$curmonth,$nextday,$curyear);
            }
        }
        // we didn't find a suitable next day in current month
    }
    // get the first of next month
    foreach ($newmonths as $nextmonth) {
        if ($nextmonth > $curmonth) {
            $nextyear = $curyear;
            break;
        }
    }
    // get the first of next year
    if (empty($nextyear)) {
        $nextyear = $curyear + 1;
        $nextmonth = array_shift($newmonths);
    }
    $maxday = date('t', mktime(0,0,0,$nextmonth,1,$nextyear));
    $curweekday = date('w', mktime(0,0,0,$nextmonth,1,$nextyear));
    for ($nextday = 1; $nextday <= $maxday; $nextday++) {
        $nextweekday = ($curweekday + $nextday - 1) % 7;
        if ((empty($newdays) || in_array($nextday,$newdays)) &&
            (empty($newweekdays) || in_array($nextweekday,$newweekdays))) {
            $nexthour = array_shift($newhours);
            $nextminute = array_shift($newminutes);
            return mktime($nexthour,$nextminute,$cursecond,$nextmonth,$nextday,$nextyear);
        }
    }
    return 1;
}

?>
