<?php

/**
 * run scheduler jobs
 * 
 * @returns string
 */
function scheduler_userapi_runjobs($args)
{
    // check when we last ran the scheduler
    $lastrun = xarModGetVar('scheduler', 'lastrun');
    if (!empty($lastrun) && $lastrun > time() - 60*60) {
        $diff = time() - $lastrun;
        return xarML('Last run was #(1) minutes #(2) seconds ago', intval($diff / 60), $diff % 60);
    }

    // let's run without interruptions for a while :)
    @ignore_user_abort(true);
    @set_time_limit(15*60);

    // update the last run time
    xarModSetVar('scheduler','lastrun',time());
    xarModSetVar('scheduler','running',1);

    // run the jobs
    $log = xarML('Starting jobs') . "<br/>\n";
    $serialjobs = xarModGetVar('scheduler','jobs');
    if (!empty($serialjobs)) {
        $jobs = unserialize($serialjobs);
    } else {
        $jobs = array();
    }
    foreach ($jobs as $id => $job) {
        $now = time();
        $lastrun = $job['lastrun'];
        if (!empty($lastrun)) {
            if (!preg_match('/(\d+)(\w)/',$job['interval'],$matches)) {
                continue;
            }
            $count = $matches[1];
            $interval = $matches[2];
            $skip = 0;
            switch ($interval) {
                case 'h':
                    if ($now - $lastrun < $count * 60 * 60) {
                        $skip = 1;
                    }
                    break;
                case 'd':
                    if ($now - $lastrun < $count * 24 * 60 * 60) {
                        $skip = 1;
                    }
                    break;
                case 'w':
                    if ($now - $lastrun < $count * 7 * 24 * 60 * 60) {
                        $skip = 1;
                    }
                    break;
                case 'm': // work with day of the month here
                    $new = getdate($now);
                    $old = getdate($lastrun);
                    $new['mon'] += 12 * ($new['year'] - $old['year']);
                    if ($new['mon'] < $old['mon'] + $count) {
                        $skip = 1;
                    } elseif ($new['mon'] == $old['mon'] + $count && $new['mday'] < $old['mday']) {
                        $skip = 1;
                    }
                    break;
            }
            if ($skip) {
                continue;
            }
        }
        $log .= $job['module'] . ' ' . $job['type'] . ' ' . $job['func'] . ' ';
// TODO: handle arguments ?
        if (!xarModAPIFunc($job['module'], $job['type'], $job['func'], array(), 0)) {
            $jobs[$id]['result'] = xarML('failed');
            $log .= xarML('failed');
        } else {
            $jobs[$id]['result'] = xarML('OK');
            $log .= xarML('succeeded');
        }
        $jobs[$id]['lastrun'] = $now;
        $log .= "<br/>\n";
    }
    $serialjobs = serialize($jobs);
    xarModSetVar('scheduler','jobs',$serialjobs);
    xarModDelVar('scheduler','running');

    $log .= xarML('Done');

    return $log; 
}

?>
