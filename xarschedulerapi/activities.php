<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * run all scheduled workflow activities (executed by the scheduler module)
 *
 * @author mikespub
 * @access public
 */
function workflow_schedulerapi_activities($args)
{
// We need to keep track of our own set of jobs here, because the scheduler won't know what
// workflow activities to run when. Other modules will typically have 1 job that corresponds
// to 1 API function, so they won't need this...

    $log = xarML('Starting scheduled workflow activities') . "\n";
    $serialjobs = xarModVars::get('workflow','jobs');
    if (!empty($serialjobs)) {
        $jobs = unserialize($serialjobs);
    } else {
        $jobs = array();
    }
    $hasrun = array();
    $now = time() + 60; // add some margin here
    foreach ($jobs as $id => $job) {
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
        $log .= xarML('Workflow activity #(1)',$job['activity']) . ' ';
        if (!xarModAPIFunc('workflow','user','run_activity',
                           array('activityId' => $job['activity']), 0)) {
            $jobs[$id]['result'] = xarML('failed');
            $log .= xarML('failed');
        } else {
            $jobs[$id]['result'] = xarML('OK');
            $log .= xarML('succeeded');
        }
        $jobs[$id]['lastrun'] = $now - 60; // remove the margin here
        $hasrun[] = $id;
        $log .= "\n";
    }
    $log .= xarML('Finished scheduled workflow activities');

    // we didn't run anything, so return now
    if (count($hasrun) == 0) {
        return $log;
    }

// Trick : make sure we're dealing with up-to-date information here,
//         because running all those jobs may have taken a while...
    xarVarDelCached('Mod.Variables.workflow', 'jobs');

    // get the current list of jobs
    $serialjobs = xarModVars::get('workflow','jobs');
    if (!empty($serialjobs)) {
        $newjobs = unserialize($serialjobs);
    } else {
        $newjobs = array();
    }
    // set the job information
    foreach ($hasrun as $id) {
        if (!isset($newjobs[$id])) continue;
        // make sure we're dealing with the same job here :)
        if ($newjobs[$id]['activity'] == $jobs[$id]['activity'] &&
            $newjobs[$id]['lastrun'] < $jobs[$id]['lastrun']) {

            $newjobs[$id]['result'] = $jobs[$id]['result'];
            $newjobs[$id]['lastrun'] = $jobs[$id]['lastrun'];
        }
    }
    // update the new jobs
    $serialjobs = serialize($newjobs);
    xarModVars::set('workflow','jobs',$serialjobs);

    return $log;
}

?>
