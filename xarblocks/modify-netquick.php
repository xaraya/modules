<?php
function netquery_netquickblock_modify($blockinfo)
{
    $vars = @unserialize($blockinfo['content']);
    if (!isset($vars['blockquery'])) {
        $vars['blockquery'] = 'whois';
    }
    $winsys = (DIRECTORY_SEPARATOR == '\\');
    $output = array(
                     'blockquery'            => $vars['blockquery'],
                     'authid'                => xarSecGenAuthKey(),
                     'capture_log_enabled'   => xarModGetVar('netquery', 'capture_log_enabled'),
                     'whois_enabled'         => xarModGetVar('netquery', 'whois_enabled'),
                     'whoisip_enabled'       => xarModGetVar('netquery', 'whoisip_enabled'),
                     'dns_lookup_enabled'    => xarModGetVar('netquery', 'dns_lookup_enabled'),
                     'dns_dig_enabled'       => xarModGetVar('netquery', 'dns_dig_enabled'),
                     'port_check_enabled'    => xarModGetVar('netquery', 'port_check_enabled'),
                     'http_req_enabled'      => xarModGetVar('netquery', 'http_req_enabled'),
                     'ping_enabled'          => xarModGetVar('netquery', 'ping_enabled'),
                     'ping_remote_enabled'   => xarModGetVar('netquery', 'ping_remote_enabled'),
                     'trace_enabled'         => xarModGetVar('netquery', 'trace_enabled'),
                     'trace_remote_enabled'  => xarModGetVar('netquery', 'trace_remote_enabled'),
                     'looking_glass_enabled' => xarModGetVar('netquery', 'looking_glass_enabled'),
                     'whois_max_limit'       => xarModGetVar('netquery', 'whois_max_limit'),
                     'user_submissions'      => xarModGetVar('netquery', 'user_submissions'),
                     'winsys'                => $winsys
                    );
    return $output;
}
function netquery_netquickblock_update($blockinfo)
{
    $vars['blockquery'] = xarVarCleanFromInput('blockquery');
    $blockinfo['content'] = serialize($vars);
    return $blockinfo;
}
?>