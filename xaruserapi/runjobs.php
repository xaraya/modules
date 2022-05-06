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
    	// An ID is passed: we will run a single job
        $job = xarMod::apiFunc('scheduler','user','get',$args);

        if(empty($job)) {
            $message =  xarML('Invalid job ID');
			xarLog::message($message, xarLog::LEVEL_WARNING);  
        	return $message;
        }
        // CHECKME: there are no calls to this function with a parameter $trigger or $triggers
        if((int)$job['job_trigger'] != $trigger) {
            $message =  xarML('This job has a trigger (#(1)) other than the one specified (#(2))', $triggers[(int)$job['trigger']], $triggers[$trigger]);
			xarLog::message($message, xarLog::LEVEL_WARNING);  
        	return $message;
        	}

        $jobs[$job['id']] = $job;
    } else {
    	// Get all the jobs 
        $jobs = xarMod::apiFunc('scheduler','user','getall',$args);
    }

# --------------------------------------------------------
#
# Create a jobs object instance for easy updating
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
    $log_identifier = 'Scheduler runjobs:';
    $log = xarML('#(1) Starting jobs', $log_identifier);
	$logs[] = $log;
	xarLog::message($log, xarLog::LEVEL_NOTICE);  
	
    $hasrun = array();
    $now = time();
    foreach ($jobs as $id => $job) {

        $jobname = $job['module'] . "_xar" . $job['type'] . "_" . $job['function'] . " itemid=" . $id;

        $log = xarML('#(2) Starting: #(1)', $jobname, $log_identifier);
        $logs[] = $log;
		xarLog::message($log, xarLog::LEVEL_NOTICE);  
	  
		$log = xarML('#(2) Trigger is: #(1)', (int)$job['job_trigger'], $log_identifier);
		$logs[] = $log;
		xarLog::message($log, xarLog::LEVEL_NOTICE);  

		$log = xarML('#(2) Interval is: #(1)', $job['job_interval'], $log_identifier);
		$logs[] = $log;
		xarLog::message($log, xarLog::LEVEL_NOTICE);  

		$log = xarML('#(2) Start date is: #(1), end date is: #(3)', (int)$job['start_date'], $log_identifier, (int)$job['end_date']);
		$logs[] = $log;
		xarLog::message($log, xarLog::LEVEL_NOTICE);  

        if((int)$job['job_trigger'] == 0) {
            // Ignore disabled jobs
            $log = xarML('#(2) Skipped: #(1)', $jobname, $log_identifier);
			$logs[] = $log;
			xarLog::message($log, xarLog::LEVEL_NOTICE);  
            continue;

        } elseif ((int)$job['job_trigger'] != 1) {
# --------------------------------------------------------
#
# Checks for jobs not called by an external scheduler, such as a scheduler block or the sheduler main user page
#
            
            // If the interval is 'never', always skip this job
            if ($job['job_interval'] == '0t') {
                $log = xarML('#(2) Skipped: #(1) because interval is never', $jobname, $log_identifier);
				$logs[] = $log;
				xarLog::message($log, xarLog::LEVEL_NOTICE);  
                continue;
    
            // if we are outside the start- or end-date, skip it
            } elseif ((!empty($job['start_date']) && $now < $job['start_date']) ||
                      (!empty($job['end_date']) && $now > $job['end_date'])) {
                $log = xarML('#(2) Skipped: #(1) because not within time limits', $jobname, $log_identifier);
				$logs[] = $log;
				xarLog::message($log, xarLog::LEVEL_NOTICE);  
                continue;
    
            // if this is a crontab job and the next run is later, skip it
            } elseif ($job['job_interval'] == '0c' && !empty($job['crontab']) &&
                      !empty($job['crontab']['nextrun']) && $now < $job['crontab']['nextrun'] + 60) {
                $log = xarML('#(2) Skipped: #(1) because next cron defined run is later', $jobname, $log_identifier);
				$logs[] = $log;
				xarLog::message($log, xarLog::LEVEL_NOTICE);  
                continue;
    
            // if this is the first time we run this job and it's not a crontab job, always run it
            } elseif (empty($job['last_run']) && $job['job_interval'] != '0c') {
                $log = xarML('#(2) First run for #(1). Will send.', $jobname, $log_identifier);
				$logs[] = $log;
				xarLog::message($log, xarLog::LEVEL_NOTICE);  
    
            // if the job already ran, check if we need to run it again
            } else {
                if (!preg_match('/(\d+)(\w)/',$job['job_interval'],$matches)) {
                    $log = xarML('#(1) invalid interval', $log_identifier);
					$logs[] = $log;
					xarLog::message($log, xarLog::LEVEL_WARNING);  
                    continue;
                }
                $log = xarML('#(2) Recurring run for #(1). Checking interval since last run.', $jobname, $log_identifier);
				$logs[] = $log;
				xarLog::message($log, xarLog::LEVEL_NOTICE);
				
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
                        if ($now - $job['last_run'] < $count * 60) {
                            $skip = 1;
                        }
                        break;
                    case 'h':    // Hours
                        if ($now - $job['last_run'] < $count * 60 * 60) {
                            $skip = 1;
                        }
                        break;
                    case 'd':    // Days
                        if ($now - $job['last_run'] < $count * 24 * 60 * 60) {
                            $skip = 1;
                        }
                        break;
                    case 'w':    // Weeks
                        if ($now - $job['last_run'] < $count * 7 * 24 * 60 * 60) {
                            $skip = 1;
                        }
                        break;
                    case 'm':	// work with day of the month here
                        $new = getdate($now);
                        $old = getdate($job['last_run']);
                        $new['mon'] += 12 * ($new['year'] - $old['year']);
                        if ($new['mon'] < $old['mon'] + $count) {
                            $skip = 1;
                        } elseif ($new['mon'] == $old['mon'] + $count && $new['mday'] < $old['mday']) {
                            $skip = 1;
                        }
                        break;
                    case 'c':	// crontab
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
					$log = xarML('#(2) Skipped: #(1)', $jobname, $log_identifier);
					$logs[] = $log;
					xarLog::message($log, xarLog::LEVEL_NOTICE);  
                    continue;
                }
            }
        } else {
# --------------------------------------------------------
#
# Checks for jobs called by an external scheduler, such as linux crontab
#
            $sourcetype = (int)$job['source_type'];  // Localhost, IP with or without proxy, host name
            $source = $job['source'];           // IP or host name

            $isvalid = false;
            switch ($sourcetype) {
                case 1: // Localhost
                    if (empty($proxy) && !empty($ip) && $ip == '127.0.0.1') {
                        $hostname = 'localhost';
                        $isvalid = true;
                    }
                    $log = xarML('#(1) Source type: localhost', $log_identifier);
					$logs[] = $log;
					xarLog::message($log, xarLog::LEVEL_NOTICE);  
                    break;
                case 2: // IP direct connection
                    if (empty($proxy) && !empty($ip) && $ip == $source) {
                        $isvalid = true;
                    }
                    $log = xarML('#(1) Source type: IP direct connection', $log_identifier);
					$logs[] = $log;
					xarLog::message($log, xarLog::LEVEL_NOTICE);  
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
                        $log = xarML('#(2) Source type: host #(1)', $hostname, $log_identifier);
						$logs[] = $log;
						xarLog::message($log, xarLog::LEVEL_NOTICE);  
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
                }

                if (empty($hostname)) {
                    $hostname = 'unknown';
                } else {
                    $isvalid = true;
                }
            }
            if (!$isvalid) {
                $log = xarML('#(2) Skipped: #(1)', $jobname, $log_identifier);
				$logs[] = $log;
				xarLog::message($log, xarLog::LEVEL_NOTICE);  
                continue;
            } else {
                $log = xarML('#(2) Host: #(1)', $hostname, $log_identifier);
				$logs[] = $log;
				xarLog::message($log, xarLog::LEVEL_NOTICE);  
            }
        }

        xarModVars::set('scheduler','running.' . $job['id'], 1);
        // Don't run jobs of modules that are not installed
        if (!xarMod::isAvailable($job['module'])) {
            $log = xarML('#(2) Skipped: #(1)', $jobname, $log_identifier);
			$logs[] = $log;
			xarLog::message($log, xarLog::LEVEL_NOTICE);  
            continue;
        }
        if (!empty($job['parameters'])) {
            @eval('$output = xarMod::apiFunc("'.$job['module'].'", "'.$job['type'].'", "'.$job['function'].'", '.$job['parameters'].', 0);');
        } else {
            try {
                $output = xarMod::apiFunc($job['module'], $job['type'], $job['function']);
            } catch (Exception $e) {
            	// If we are debugging, then show an error here
            	if (xarModVars::get('scheduler','debugmode') && in_array(xarUser::getVar('id'),xarConfigVars::get(null, 'Site.User.DebugAdmins'))) {
            		print_r($e->getMessage());exit;
            	}
            }
        }
        if (empty($output)) {
            $jobs[$id]['result'] = xarML('failed');
            $log = xarML('#(2) Failed: #(1)', $jobname, $log_identifier);
			$logs[] = $log;
			xarLog::message($log, xarLog::LEVEL_NOTICE);  
            $log = isset($output) ? $output : xarML('No output');
			$logs[] = $log;
			xarLog::message($log, xarLog::LEVEL_NOTICE);  
        } else {
            $jobs[$id]['result'] = xarML('OK');
            $log = xarML('#(2) Succeeded: #(1)', $jobname, $log_identifier);
			$logs[] = $log;
			xarLog::message($log, xarLog::LEVEL_NOTICE);  
            $log = $output;
			$logs[] = $log;
			xarLog::message($log, xarLog::LEVEL_NOTICE);  
        }
        $job['last_run'] = $now;
        $hasrun[$id] = $job;

# --------------------------------------------------------
#
# Update this job
#
        $jobobject->setFieldValues($job);
        $jobobject->updateItem(array('itemid' => $job['id']));
        $log = xarML('#(2) Updated: #(1)', $jobname, $log_identifier);
		$logs[] = $log;
		xarLog::message($log, xarLog::LEVEL_NOTICE);  

    }
    $log = xarML('#(1) Done', $log_identifier);
	$logs[] = $log;
	xarLog::message($log, xarLog::LEVEL_NOTICE);  

    // we didn't run anything, so return now
    if (count($hasrun) == 0) {
        xarModVars::delete('scheduler','running');
        return $logs;
    }

// Trick : make sure we're dealing with up-to-date information here,
//         because running all those jobs may have taken a while...
//    xarVarDelCached('Mod.Variables.scheduler', 'jobs');

    return $logs;
}

?>