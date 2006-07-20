<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * run scheduler jobs
 *
 * @return string The log of the jobs
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
        if (!empty($job['config'])) {
            $config = $job['config'];
        } else {
            $config = array();
        }

        // if the interval is 'never', always skip this job
        if ($job['interval'] == '0t') {
            $log .= xarML('skipped');
            continue;

        // if we are outside the start- or end-date, skip it
        } elseif ((!empty($config['startdate']) && $now < $config['startdate']) ||
                  (!empty($config['enddate']) && $now > $config['enddate'])) {
            $log .= xarML('skipped');
            continue;

        // if this is a crontab job and the next run is later, skip it
        } elseif ($job['interval'] == '0c' && !empty($config['crontab']) &&
                  !empty($config['crontab']['nextrun']) && $now < $config['crontab']['nextrun'] + 60) {
            $log .= xarML('skipped');
            continue;

        // if this is the first time we run this job and it's not a crontab job, always run it
        } elseif (empty($lastrun) && $job['interval'] != '0c') {

        // if the job already ran, check if we need to run it again
        } else {
            if (!preg_match('/(\d+)(\w)/',$job['interval'],$matches)) {
                $log .= xarML('invalid interval');
                continue;
            }
            $count = $matches[1];
            $interval = $matches[2];
            $skip = 0;
            switch ($interval) {
                case 't':    // Schedular trigger/tick
                    if ($count <> 1) {
                        // zero count is never - effectively disables a job without removing it
                        // TODO: for count > 1, a countdown of scheduler clock ticks
                        // i.e. every Nth time the scheduler is triggered.
                        $skip = 1;
                    }
                    break;
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
                case 'c': // crontab
                    if (empty($config['crontab'])) {
                        $config['crontab'] = array();
                    }
                    // check the next run for the cron-like
                    if (!empty($config['crontab']['nextrun'])) {
                        if ($now < $config['crontab']['nextrun'] + 60) {
                            $skip = 1; // in fact, this case is already handled above
                        } else {
                            // run it now, and calculate the next run for this job
                            $jobs[$id]['config']['crontab']['nextrun'] = xarModAPIFunc('scheduler','user','nextrun',
                                                                                       $config['crontab']);
                        }
                    } else {
                        // run it now, and calculate the next run for this job
                        $jobs[$id]['config']['crontab']['nextrun'] = xarModAPIFunc('scheduler','user','nextrun',
                                                                                   $config['crontab']);
                    }
                    break;
            }
            if ($skip) {
                $log .= xarML('skipped');
                continue;
            }
        }
        if (!empty($config['params'])) {
            @eval('$output = xarModAPIFunc("'.$job['module'].'", "'.$job['type'].'", "'.$job['func'].'", '.$config['params'].', 0);');
        } else {
            $output = xarModAPIFunc($job['module'], $job['type'], $job['func'], array(), 0);
        }
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
            if (isset($jobs[$id]['config'])) {
                $newjobs[$id]['config'] = $jobs[$id]['config'];
            }
        }
    }
    // update the new jobs
    $serialjobs = serialize($newjobs);
    xarModSetVar('scheduler','jobs',$serialjobs);
    xarModDelVar('scheduler','running');

    return $log;
}

?>
