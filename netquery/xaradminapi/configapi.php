<?php
function netquery_adminapi_configapi()
{
    $data = array();
    $data['authid'] = xarSecGenAuthKey();
    $data['capture_log_enabled'] = xarModGetVar('netquery', 'capture_log_enabled');
    $data['whois_enabled'] = xarModGetVar('netquery', 'whois_enabled');
    $data['whoisip_enabled'] = xarModGetVar('netquery', 'whoisip_enabled');
    $data['dns_lookup_enabled'] = xarModGetVar('netquery', 'dns_lookup_enabled');
    $data['dns_dig_enabled'] = xarModGetVar('netquery', 'dns_dig_enabled');
    $data['port_check_enabled'] = xarModGetVar('netquery', 'port_check_enabled');
    $data['http_req_enabled'] = xarModGetVar('netquery', 'http_req_enabled');
    $data['ping_enabled'] = xarModGetVar('netquery', 'ping_enabled');
    $data['ping_remote_enabled'] = xarModGetVar('netquery', 'ping_remote_enabled');
    $data['trace_enabled'] = xarModGetVar('netquery', 'trace_enabled');
    $data['trace_remote_enabled'] = xarModGetVar('netquery', 'trace_remote_enabled');
    $data['looking_glass_enabled'] = xarModGetVar('netquery', 'looking_glass_enabled');
    $data['whois_max_limit'] = xarModGetVar('netquery', 'whois_max_limit');
    $data['submitlabel'] = xarML('Submit');
    $data['cancellabel'] = xarML('Cancel');
    $data['pingexec'] = xarModAPIFunc('netquery', 'admin', 'getexec', array('exec_type' => 'ping'));
    $data['traceexec'] = xarModAPIFunc('netquery', 'admin', 'getexec', array('exec_type' => 'trace'));
    $data['logfile'] = xarModAPIFunc('netquery', 'admin', 'getexec', array('exec_type' => 'log'));
    $data['lgdefault'] = xarModAPIFunc('netquery', 'admin', 'getlgrdata', array('router' => 'default'));
    $data['whoislimits'] = Array(1, 2, 3, 4, 5);
    $data['cfglink'] = Array('url'   => xarModURL('netquery', 'admin', 'config'),
                             'title' => xarML('Return to main configuration'),
                             'label' => xarML('Modify Configuration'));
    $data['flvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'flview'),
                             'title' => xarML('Edit service/exploit flags'),
                             'label' => xarML('Edit Service Flags'));
    $data['wivlink'] = Array('url'   => xarModURL('netquery', 'admin', 'wiview'),
                             'title' => xarML('Edit whois TLD/server links'),
                             'label' => xarML('Edit Whois Links'));
    $data['lgvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'lgview'),
                             'title' => xarML('Edit looking glass routers'),
                             'label' => xarML('Edit LG Routers'));
    $data['ptvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'ptview'),
                             'title' => xarML('Edit services and exploits'),
                             'label' => xarML('Edit Port Services'));
    $data['hlplink'] = Array('url'   => xarML('modules/netquery/xardocs/manual.html#admin'),
                             'title' => xarML('Netquery online manual'),
                             'label' => xarML('Online Manual'));
    $logfile = $data['logfile'];
    $logfile = $logfile['exec_local'];
    if (file_exists($logfile)) {
        $data['loglink'] = Array('url'   => xarML($logfile),
                                 'title' => xarML('View operations logfile'),
                                 'label' => xarML('View Log'));
    } else {
        $data['loglink'] = '';
    }
    return $data;
}
?>
