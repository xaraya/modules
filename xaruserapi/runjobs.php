<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * run scheduler jobs
 *
 * @param int trigger
 * @param array jobs
 * @return string The log of the jobs
 */
function scheduler_userapi_runjobs($args)
{
    extract($args);

# --------------------------------------------------------
#
# Get the IP of the caller
#
    $ip = xarServer::getVar('REMOTE_ADDR');
    // Hackish way to convert IPv4 to IPv6
    if ($ip == "::1") $ip = "127.0.0.1";
    $forwarded = xarServer::getVar('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $proxy = $ip;
        $ip = preg_replace('/,.* /', '', $forwarded);
    }

# --------------------------------------------------------
#
# Get the current jobs
#
    if(!empty($itemid)) {
        $job = xarMod::apiFunc('scheduler','user','get',$args);

        if(empty($job)) {
            return xarML('Invalid job ID');
        }
        if($job['job_trigger'] != $trigger) {
            return xarML('This job has a trigger (#(1)) other than the one specified (#(2))', $triggers[$job['trigger']], $triggers[$trigger]);
        }

        $jobs[$job['id']] = $job;
    } else {
        $jobs = xarMod::apiFunc('scheduler','user','getall',$args);
    }

# --------------------------------------------------------
#
# Get the jobs object for easy updating
#
    sys::import('modules.dynamicdata.class.objects.master');
    $jobobject = DataObjectMaster::getObject(array('name' => 'scheduler_jobs'));

# --------------------------------------------------------
#
# let's run without interruptions for a while :)
#
    @ignore_user_abort(true);
    @set_time_limit(15*60);

# --------------------------------------------------------
#
# Run the jobs: we go through the loop
#
    $log[] = xarML('Starting jobs');
    $hasrun = array();
    foreach ($jobs as $id => $job) {

        $jobname = $job['module'] . "_xar" . $job['type'] . "_" . $job['function'];

        $log[] = xarML('Starting: ') . $jobname;
        
        if($job['job_trigger'] == 0) {
            // Ignore disabled jobs
            $log[] = xarML('Skipped: ') . $jobname;
            continue;

# --------------------------------------------------------
#
# Checks for jobs not called by an external scheduler, such as a scheduler block
#
        } elseif($job['job_trigger'] != 1) {
            
            // If the interval is 'never', always skip this job
            if ($job['job_interval'] == '0t') {
                $log[] = xarML('Skipped: ') . $jobname;
                continue;
    
            // if we are outside the start- or end-date, skip it
            } elseif ((!empty($job['startdate']) && $now < $job['startdate']) ||
                      (!empty($job['enddate']) && $now > $job['enddate'])) {
                $log[] = xarML('Skipped: ') . $jobname;
                continue;
    
            // if this is a crontab job and the next run is later, skip it
            } elseif ($job['job_interval'] == '0c' && !empty($job['crontab']) &&
                      !empty($job['crontab']['nextrun']) && $now < $job['crontab']['nextrun'] + 60) {
                $log[] = xarML('Skipped: ') . $jobname;
                continue;
    
            // if this is the first time we run this job and it's not a crontab job, always run it
            } elseif (empty($lastrun) && $job['job_interval'] != '0c') {
    
            // if the job already ran, check if we need to run it again
            } else {
                if (!preg_match('/(\d+)(\w)/',$job['job_interval'],$matches)) {
                    $log[] = xarML('invalid interval');
                    continue;
                }
                $count = $matches[1];
                $interval = $matches[2];
                $skip = 0;
                switch ($interval) {
                    case 't':    // Scheduler trigger/tick
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
                        if (empty($job['crontab'])) {
                            $job['crontab'] = array();
                        }
                        // check the next run for the cron-like
                        if (!empty($job['crontab']['nextrun'])) {
                            if ($now < $job['crontab']['nextrun'] + 60) {
                                $skip = 1; // in fact, this case is already handled above
                            } else {
                                // run it now, and calculate the next run for this job
                                $jobs[$id]['crontab']['nextrun'] = xarMod::apiFunc('scheduler','user','nextrun',$job['crontab']);
                            }
                        } else {
                            // run it now, and calculate the next run for this job
                            $jobs[$id]['crontab']['nextrun'] = xarMod::apiFunc('scheduler','user','nextrun',$job['crontab']);
                        }
                        break;
                }
                if ($skip) {
                    $log[] = xarML('Skipped: ') . $jobname;
                    continue;
                }
            }
# --------------------------------------------------------
#
# Checks for jobs called by an external scheduler
#
        } else {
            
            $sourcetype = $job['source_type'];  // Localhost, IP with or without proxy, host name
            $source = $job['source'];           // IP or host name

            $isvalid = false;
            switch ($sourcetype) {
                case 1: // Localhost
                    if (empty($proxy) && !empty($ip) && $ip == '127.0.0.1') {
                        $isvalid = true;
                    }
                    $log[] = xarML('Source type: localhost');
                    break;
                case 2: // IP direct connection
                    if (empty($proxy) && !empty($ip) && $ip == $source) {
                        $isvalid = true;
                    }
                    $log[] = xarML('Source type: IP direct connection');
                    break;
                case 3: // IP behind proxy
                    if (!empty($proxy) && !empty($ip) && $ip == $source) {
                        $isvalid = true;
                    }
                    break;
                case 4: // Host name
                    if (!empty($ip)) {
                        $hostname = @gethostbyaddr($ip);
                        // same player, shoot again...
                        if (empty($hostname)) {
                            $hostname = @gethostbyaddr($ip);
                        }
                        $log[] = xarML('Source type: host #(1)', $hostname);
                        if (!empty($hostname) && $hostname == $source) {
                            $isvalid = true;
                        }
                    }
                    break;
            }
            
            // Try and get the host via the IP
            if (!$isvalid) {
                if (!empty($ip)) {
                    $hostname = @gethostbyaddr($ip);
                    // same player, shoot again...
                    if (empty($hostname)) {
                        $hostname = @gethostbyaddr($ip);
                    }

                    if (empty($hostname)) {
                        $hostname = 'unknown';
                    } else {
                        $isvalid = true;
                    }
                }
            }
            if (!$isvalid) {
                $log[] = xarML('Skipped: ') . $jobname;
                continue;
            } else {
                $log[] = xarML('Host: ') . $hostname;
            }
        }

        xarModVars::set('scheduler','running.' . $job['id'], 1);
        if (!xarMod::isAvailable($job['module'])) {
            $log[] = xarML('Skipped: ') . $jobname;
            continue;
        }
        if (!empty($job['params'])) {
            @eval('$output = xarMod::apiFunc("'.$job['module'].'", "'.$job['type'].'", "'.$job['function'].'", '.$job['params'].', 0);');
        } else {
            try {
                $output = xarMod::apiFunc($job['module'], $job['type'], $job['function']);
            } catch (Exception $e) {}
        }
        if (empty($output)) {
            $jobs[$id]['result'] = xarML('failed');
            $log[] = xarML('Failed: ') . $jobname;
            $log[] = $output;
        } else {
            $jobs[$id]['result'] = xarML('OK');
            $log[] = xarML('Succeeded: ') . $jobname;
            $log[] = $output;
        }
        $jobs[$id]['last_run'] = time();
        $hasrun[$id] = $job;

# --------------------------------------------------------
#
# Update this job
#
        $jobobject->setFieldValues($job);
        $jobobject->updateItem(array('itemid' => $job['id']));
        $log[] = xarML('Updated: ') . $jobname;

    }
    $log[] = xarML('Done');

    // we didn't run anything, so return now
    if (count($hasrun) == 0) {
        xarModVars::delete('scheduler','running');
        return $log;
    }

// Trick : make sure we're dealing with up-to-date information here,
//         because running all those jobs may have taken a while...
//    xarVarDelCached('Mod.Variables.scheduler', 'jobs');

    return $log;
}

?>