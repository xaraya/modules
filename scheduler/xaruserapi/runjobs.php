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
    $now = time() + 60; // add some margin here
    foreach ($jobs as $id => $job) {
        $log .= "<br/>\n" . $job['module'] . ' ' . $job['type'] . ' ' . $job['func'] . ' ';
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
            $log .= xarML('succeeded') . " : <br/>\n";
            $log .= $output;
        }
        $jobs[$id]['lastrun'] = $now - 60; // remove the margin here
    }
    $serialjobs = serialize($jobs);
// FIXME: this may overwrite changes done via modifyconfig
    xarModSetVar('scheduler','jobs',$serialjobs);
    xarModDelVar('scheduler','running');

    $log .= "<br/>\n" . xarML('Done');
    return $log; 
}

?>
