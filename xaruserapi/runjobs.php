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
 * @param int trigger
 * @param array jobs
 * @return string The log of the jobs
 */
function scheduler_userapi_runjobs($args)
{
	extract($args);
	
	$triggers = xarModAPIFunc('scheduler','user','triggers');

	if(!isset($trigger) || $isset($triggers[$trigger])) {
		return xarML('Trigger not specified');
	}
	if($trigger == 0) {
		return xarML('Cannot run disabled jobs');
	}
    if(!empty($itemid)) {
        $job = xarModAPIFunc('scheduler','user','get',array('itemid' => $itemid));

        if(empty($job)) {
            return xarML('Invalid job ID');
        }
        if($job['job_trigger'] != $trigger) {
            return xarML('This job has a trigger (#(1)) other than the one specified (#(2))', $triggers[$job['trigger']], $triggers[$trigger]);
        }

        $jobs[$job['id']] = $job;
    } else {
        $jobs = xarModAPIFunc('scheduler','user','getall',array('trigger' => $trigger));
    }

    // let's run without interruptions for a while :)
    @ignore_user_abort(true);
    @set_time_limit(15*60);

    // run the jobs
    $log = xarML('Starting jobs');

    $hasrun = array();
    foreach ($jobs as $id => $job) {
	    $now = time() + 60; // add some margin here
        $log .= "\n" . $job[$id] . ': ' . $job['module'] . ' ' . $job['functype'] . ' ' . $job['func'] . ' ';
        $lastrun = $job['lastrun'];
        if (!empty($job['config'])) {
            $config = $job['config'];
        } else {
            $config = array();
        }

		// checks for jobs not called by external scheduler
		if($trigger != 1) {
	        // if the interval is 'never', always skip this job
	        if ($job['job_interval'] == '0t') {
	            $log .= xarML('skipped');
	            continue;
	
	        // if we are outside the start- or end-date, skip it
	        } elseif ((!empty($config['startdate']) && $now < $config['startdate']) ||
	                  (!empty($config['enddate']) && $now > $config['enddate'])) {
	            $log .= xarML('skipped');
	            continue;
	
	        // if this is a crontab job and the next run is later, skip it
	        } elseif ($job['job_interval'] == '0c' && !empty($config['crontab']) &&
	                  !empty($config['crontab']['nextrun']) && $now < $config['crontab']['nextrun'] + 60) {
	            $log .= xarML('skipped');
	            continue;
	
	        // if this is the first time we run this job and it's not a crontab job, always run it
	        } elseif (empty($lastrun) && $job['job_interval'] != '0c') {
	
	        // if the job already ran, check if we need to run it again
	        } else {
	            if (!preg_match('/(\d+)(\w)/',$job['job_interval'],$matches)) {
	                $log .= xarML('invalid interval');
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
		} else {
			// checks for jobs called by external scheduler
		    // get the IP
		    $ip = xarServerGetVar('REMOTE_ADDR');
		    $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');
		    if (!empty($forwarded)) {
		        $proxy = $ip;
		        $ip = preg_replace('/,.*/', '', $forwarded);
		    }

		    $checktype = $jobs[$id]['checktype'];
		    $checkvalue = $jobs[$id]['checkvalue'];

			// TODO: allow IP range or domain here if that's what people want (insecure)
		    $isvalid = 0;
		    switch ($checktype) {
		        case 1: // local
		            if (empty($proxy) && !empty($ip) && $ip == '127.0.0.1') {
		                $isvalid = 1;
		            }
		            break;
		        case 2: // IP direct connection
		            if (empty($proxy) && !empty($ip) && $ip == $checkvalue) {
		                $isvalid = 1;
		            }
		            break;
		        case 3: // IP behind proxy
		            if (!empty($proxy) && !empty($ip) && $ip == $checkvalue) {
		                $isvalid = 1;
		            }
		            break;
		        case 4: // host name
		            if (!empty($ip)) {
		                $hostname = @gethostbyaddr($ip);
		                // same player, shoot again...
		                if (empty($hostname)) {
		                    $hostname = @gethostbyaddr($ip);
		                }
		                if (!empty($hostname) && $hostname == $checkvalue) {
		                    $isvalid = 1;
		                }
		            }
		            break;
		    }
		    if (!$isvalid) {
		        if (!empty($ip)) {
		            $hostname = @gethostbyaddr($ip);
		            // same player, shoot again...
		            if (empty($hostname)) {
		                $hostname = @gethostbyaddr($ip);
		            }

		            if (empty($hostname)) {
		                $hostname = 'unknown';
		            }
		        }
		        xarLogMessage("scheduler: Failed trigger attempt from host $ip ($hostname).");
		        $log .= xarML("Failed trigger attempt from host #(1) (#(2)) at #(3)", $ip, $hostname, date('j', time()));
		        continue;
		    }
		}

        xarModVars::set('running.' . $job['id'], 1);
        if (!empty($config['params'])) {
            @eval('$output = xarModAPIFunc("'.$job['module'].'", "'.$job['functype'].'", "'.$job['func'].'", '.$config['params'].', 0);');
        } else {
            $output = xarModAPIFunc($job['module'], $job['functype'], $job['func'], array(), 0);
        }
        xarModVars::set('running.' . $job['id'], 0);
        if (empty($output)) {
            $job['result'] = xarML('failed');
            $log .= xarML('failed');
        } else {
            $jobs[$id]['result'] = xarML('OK');
            $log .= xarML('succeeded') . " : \n";
            $log .= $output;
        }
        $job['lastrun'] = time();
        $hasrun[$id] = $job;
    }
    $log .= "\n" . xarML('Done');

    // we didn't run anything, so return now
    if (count($hasrun) == 0) {
        return $log;
    }

// Trick : make sure we're dealing with up-to-date information here,
//         because running all those jobs may have taken a while...
//    xarVarDelCached('Mod.Variables.scheduler', 'jobs');

    // get the current list of jobs
    $newjobs = xarModAPIFunc('scheduler','user','getall');

    // update the jobs
    foreach ($hasrun as $job -> $id) {
        if (!isset($newjobs[$id])) continue;
        // make sure we're dealing with the same job here :)
        if ($newjobs[$id]['module'] == $hasrun[$id]['module'] &&
            $newjobs[$id]['functype'] == $hasrun[$id]['functype'] &&
            $newjobs[$id]['func'] == $hasrun[$id]['func'] &&
            $newjobs[$id]['lastrun'] < $hasrun[$id]['lastrun']) {

            $newjobs[$id]['result'] = $hasrun[$id]['result'];
            $newjobs[$id]['lastrun'] = $hasrun[$id]['lastrun'];
            if (isset($hasrun[$id]['config'])) {
                $newjobs[$id]['config'] = $hasrun[$id]['config'];
            }
        }
	    // update the job
	    xarModAPIFunc('scheduler','admin','update',$newjobs[$id]);
    }

    return $log;
}

?>
