<?php

/**
 * run scheduler jobs
 * 
 * @returns string
 */
function scheduler_userapi_runjobs($args = array())
{
    // run the jobs
    $log = xarML('Starting jobs');
    $serialjobs = xarModGetVar('scheduler','jobs');
    if (!empty($serialjobs)) {
        $jobs = unserialize($serialjobs);
    } else {
        $jobs = array();
    }
    $hasrun = array();
    $now = time() + 60; // add some margin here
    foreach ($jobs as $id => $job) {
        $log .= "\n" . $job['module'] . ' ' . $job['type'] . ' ' . $job['func'] . ' ';
        $lastrun = $job['lastrun'];
        if (!empty($lastrun)) {
            if (!preg_match('/(\d+)(\w)/',$job['interval'],$matches)) {
                $log .= xarML('invalid interval');
                continue;
            }
            $count = $matches[1];
            $interval = $matches[2];
            $skip = 0;
            switch ($interval) {
                case 'n':    // Minutes
                    if ($now - $lastrun < $count * 60) {
                        $skip = 1;
                    }
                    break;
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
                $log .= xarML('skipped');
                continue;
            }
        }
// TODO: handle arguments ?
        $output = xarModAPIFunc($job['module'], $job['type'], $job['func'], array(), 0);
        if (empty($output)) {
            $jobs[$id]['result'] = xarML('failed');
            $log .= xarML('failed');
        } else {
            $jobs[$id]['result'] = xarML('OK');
            $log .= xarML('succeeded') . " : \n";
            $log .= $output;
        }
        $jobs[$id]['lastrun'] = $now - 60; // remove the margin here
        $hasrun[] = $id;
    }
    $log .= "\n" . xarML('Done');

    // we didn't run anything, so return now
    if (count($hasrun) == 0) {
        xarModDelVar('scheduler','running');
        return $log;
    }

// Trick : make sure we're dealing with up-to-date information here,
//         because running all those jobs may have taken a while...
//    xarVarDelCached('Mod.Variables.scheduler', 'jobs');

    // get the current list of jobs
    $serialjobs = xarModGetVar('scheduler','jobs');
    if (!empty($serialjobs)) {
        $newjobs = unserialize($serialjobs);
    } else {
        $newjobs = array();
    }
    
    // set the job information
    foreach ($hasrun as $id) {
        if (!isset($newjobs[$id])) continue;
        // make sure we're dealing with the same job here :)
        if ($newjobs[$id]['module'] == $jobs[$id]['module'] &&
            $newjobs[$id]['type'] == $jobs[$id]['type'] &&
            $newjobs[$id]['func'] == $jobs[$id]['func'] &&
            $newjobs[$id]['lastrun'] < $jobs[$id]['lastrun']) {

            $newjobs[$id]['result'] = $jobs[$id]['result'];
            $newjobs[$id]['lastrun'] = $jobs[$id]['lastrun'];
        }
    }
    // update the new jobs
    $serialjobs = serialize($newjobs);
    xarModSetVar('scheduler','jobs',$serialjobs);
    xarModDelVar('scheduler','running');

    return $log; 
}

?>
