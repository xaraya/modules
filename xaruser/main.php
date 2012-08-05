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
 * the main user function - only used for external triggers
 * @param  $args ['itemid'] job id (optional)
 */
function scheduler_user_main()
{
/*
    // check if we have the right trigger
    $trigger = xarModVars::get('scheduler','trigger');
    if (empty($trigger) || $trigger != 'external') {
        return xarML('Wrong trigger');
    }

    // get the IP
    $ip = xarServer::getVar('REMOTE_ADDR');
    $forwarded = xarServer::getVar('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $proxy = $ip;
        $ip = preg_replace('/,.* /', '', $forwarded);
    }

    $checktype = xarModVars::get('scheduler','checktype');
    $checkvalue = xarModVars::get('scheduler','checkvalue');

// TODO: allow IP range or domain here if that's what people want (insecure)
    $isvalid = 0;
    switch ($checktype) {
        case 'local':
            if (empty($proxy) && !empty($ip) && $ip == '127.0.0.1') {
                $isvalid = 1;
            }
            break;
        case 'ip':
            if (empty($proxy) && !empty($ip) && $ip == $checkvalue) {
                $isvalid = 1;
            }
            break;
        case 'proxy':
            if (!empty($proxy) && !empty($ip) && $ip == $checkvalue) {
                $isvalid = 1;
            }
            break;
        case 'host':
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
        return xarML('Wrong trigger')." $ip ($hostname) at " . date('j', time());
    }
*/
    // check when we last ran the scheduler
    $lastrun = xarModVars::get('scheduler', 'lastrun');
    $now = time();
/*
    if (!empty($lastrun) && $lastrun > $now - ((60*5)-1) )  // Make sure it's been at least five minutes
    {
        $diff = time() - $lastrun;
        return xarML('Last run was #(1) minutes #(2) seconds ago', intval($diff / 60), $diff % 60);
    }
*/

    // update the last run time
    xarModVars::set('scheduler','lastrun',$now - 60); // remove the margin here
    
    xarModVars::set('scheduler','running',1);
    $data['output'] = xarMod::apiFunc('scheduler','user','runjobs');
    xarModVars::delete('scheduler','running');
    if (xarIsParent('Administrators', xarUserGetVar('uname'))) {
        return $data;
    } else {
        return xarController::$response->NotFound();
    }
}

?>