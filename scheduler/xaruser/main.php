<?php

/**
 * the main user function - only used for external triggers
 */
function scheduler_user_main()
{
    // check if we have the right trigger
    $trigger = xarModGetVar('scheduler','trigger');
    if (empty($trigger) || $trigger != 'external') {
        return xarML('Wrong trigger');
    }

    // get the IP
    $ip = xarServerGetVar('REMOTE_ADDR');
    $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $proxy = $ip;
        $ip = preg_replace('/,.*/', '', $forwarded);
    }

    $checktype = xarModGetVar('scheduler','checktype');
    $checkvalue = xarModGetVar('scheduler','checkvalue');

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
        return xarML('Wrong trigger');
    }

    $output = xarModAPIFunc('scheduler','user','runjobs');

// TODO: dump exceptions ?
    return $output;
}

?>
