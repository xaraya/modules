<?php
function netquery_adminapi_configapi()
{
    $data = array();
    $data['authid'] = xarSecGenAuthKey();
    $data['querytype_default'] = xarModGetVar('netquery', 'querytype_default');
    $data['capture_log_enabled'] = xarModGetVar('netquery', 'capture_log_enabled');
    $data['capture_log_filepath'] = xarModGetVar('netquery', 'capture_log_filepath');
    $data['capture_log_dtformat'] = xarModGetVar('netquery', 'capture_log_dtformat');
    $data['clientinfo_enabled'] = xarModGetVar('netquery', 'clientinfo_enabled');
    $data['whois_enabled'] = xarModGetVar('netquery', 'whois_enabled');
    $data['whois_max_limit'] = xarModGetVar('netquery', 'whois_max_limit');
    $data['whois_default'] = xarModGetVar('netquery', 'whois_default');
    $data['whoisip_enabled'] = xarModGetVar('netquery', 'whoisip_enabled');
    $data['dns_lookup_enabled'] = xarModGetVar('netquery', 'dns_lookup_enabled');
    $data['dns_dig_enabled'] = xarModGetVar('netquery', 'dns_dig_enabled');
    $data['email_check_enabled'] = xarModGetVar('netquery', 'email_check_enabled');
    $data['query_email_server'] = xarModGetVar('netquery', 'query_email_server');
    $data['use_win_nslookup'] = xarModGetVar('netquery', 'use_win_nslookup');
    $data['port_check_enabled'] = xarModGetVar('netquery', 'port_check_enabled');
    $data['user_submissions'] = xarModGetVar('netquery', 'user_submissions');
    $data['http_req_enabled'] = xarModGetVar('netquery', 'http_req_enabled');
    $data['ping_enabled'] = xarModGetVar('netquery', 'ping_enabled');
    $data['pingexec_local'] = xarModGetVar('netquery', 'pingexec_local');
    $data['ping_remote_enabled'] = xarModGetVar('netquery', 'ping_remote_enabled');
    $data['pingexec_remote'] = xarModGetVar('netquery', 'pingexec_remote');
    $data['pingexec_remote_t'] = xarModGetVar('netquery', 'pingexec_remote_t');
    $data['trace_enabled'] = xarModGetVar('netquery', 'trace_enabled');
    $data['traceexec_local'] = xarModGetVar('netquery', 'traceexec_local');
    $data['trace_remote_enabled'] = xarModGetVar('netquery', 'trace_remote_enabled');
    $data['traceexec_remote'] = xarModGetVar('netquery', 'traceexec_remote');
    $data['traceexec_remote_t'] = xarModGetVar('netquery', 'traceexec_remote_t');
    $data['looking_glass_enabled'] = xarModGetVar('netquery', 'looking_glass_enabled');
    $data['submitlabel'] = xarML('Submit');
    $data['cancellabel'] = xarML('Cancel');
    $data['links'] = xarModAPIFunc('netquery', 'user', 'getlinks');
    $data['portsubmits'] = xarModAPIFunc('netquery', 'admin', 'countportflag', array('flag' => '99'));
    $data['whoislimits'] = Array(1, 2, 3, 4, 5);
    $startoptions = array();
      $startoptions[] = array('name' => xarML('Whois'), 'value' => 'whois');
      $startoptions[] = array('name' => xarML('Whois IP'), 'value' => 'whoisip');
      $startoptions[] = array('name' => xarML('DNS Lookup'), 'value' => 'lookup');
      $startoptions[] = array('name' => xarML('DNS Dig'), 'value' => 'dig');
      $startoptions[] = array('name' => xarML('Port Check'), 'value' => 'port');
      $startoptions[] = array('name' => xarML('HTTP Request'), 'value' => 'http');
      $startoptions[] = array('name' => xarML('Ping'), 'value' => 'ping');
      $startoptions[] = array('name' => xarML('Ping Remote'), 'value' => 'pingrem');
      $startoptions[] = array('name' => xarML('Traceroute'), 'value' => 'trace');
      $startoptions[] = array('name' => xarML('Trace Remote'), 'value' => 'tracerem');
      $startoptions[] = array('name' => xarML('Looking Glass'), 'value' => 'lgquery');
    $data['startoptions'] = $startoptions;
    $data['cfglink'] = Array('url'   => xarModURL('netquery', 'admin', 'config'),
                             'title' => xarML('Return to main configuration'),
                             'label' => xarML('Modify Configuration'));
    $data['flvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'flview'),
                             'title' => xarML('Edit service/exploit category flags'),
                             'label' => xarML('Edit Category Flags'));
    $data['wivlink'] = Array('url'   => xarModURL('netquery', 'admin', 'wiview'),
                             'title' => xarML('Edit whois TLD/server links'),
                             'label' => xarML('Edit Whois Links'));
    $data['lgvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'lgview'),
                             'title' => xarML('Edit looking glass settings'),
                             'label' => xarML('Edit LG Settings'));
    $data['ptvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'ptview'),
                             'title' => xarML('Edit services/exploits'),
                             'label' => xarML('Edit Port Services'));
    $data['p99link'] = Array('url'   => xarModURL('netquery', 'admin', 'ptview', array('pflag' => '99')),
                             'title' => '<font color="red">'.xarML($data['portsubmits'].' New for Reflagging').'</font>',
                             'label' => xarML('None for Reflagging'));
    $data['xaplink'] = Array('url'   => xarModURL('netquery', 'admin', 'xaports'),
                             'title' => xarML('Build port services/exploits table'),
                             'label' => xarML('Build New Table'));
    $data['hlplink'] = Array('url'   => 'modules/netquery/xardocs/manual.html#admin',
                             'title' => xarML('Netquery online manual'),
                             'label' => xarML('Online Manual'));
    if (file_exists($data['capture_log_filepath'])) {
        $data['loglink'] = Array('url'   => xarML($data['capture_log_filepath']),
                                 'title' => xarML('View operations logfile'),
                                 'label' => xarML('View Log'));
    } else {
        $data['loglink'] = '';
    }
    return $data;
}
?>