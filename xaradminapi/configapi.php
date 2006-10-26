<?php
function netquery_adminapi_configapi()
{
    $data = array();
    $data['authid'] = xarSecGenAuthKey();
    $data['querytype_default'] = xarModGetVar('netquery', 'querytype_default');
    $data['exec_timer_enabled'] = xarModGetVar('netquery', 'exec_timer_enabled');
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    $data['buttondir'] = ((list($testdir) = split('[._-]', $data['stylesheet'])) && (!empty($testdir)) && (file_exists('modules/netquery/xarimages/'.$testdir))) ? 'modules/netquery/xarimages/'.$testdir : 'modules/netquery/xarimages/blbuttons';
    $data['capture_log_enabled'] = xarModGetVar('netquery', 'capture_log_enabled');
    $data['capture_log_allowuser'] = xarModGetVar('netquery', 'capture_log_allowuser');
    $data['capture_log_filepath'] = xarModGetVar('netquery', 'capture_log_filepath');
    $data['capture_log_dtformat'] = xarModGetVar('netquery', 'capture_log_dtformat');
    $data['clientinfo_enabled'] = xarModGetVar('netquery', 'clientinfo_enabled');
    $data['mapping_site'] = xarModGetVar('netquery', 'mapping_site');
    $data['topcountries_limit'] = xarModGetVar('netquery', 'topcountries_limit');
    $data['whois_enabled'] = xarModGetVar('netquery', 'whois_enabled');
    $data['whois_max_limit'] = xarModGetVar('netquery', 'whois_max_limit');
    $data['whois_default'] = xarModGetVar('netquery', 'whois_default');
    $data['whoisip_enabled'] = xarModGetVar('netquery', 'whoisip_enabled');
    $data['dns_lookup_enabled'] = xarModGetVar('netquery', 'dns_lookup_enabled');
    $data['dns_dig_enabled'] = xarModGetVar('netquery', 'dns_dig_enabled');
    $data['digexec_local'] = xarModGetVar('netquery', 'digexec_local');
    $data['email_check_enabled'] = xarModGetVar('netquery', 'email_check_enabled');
    $data['query_email_server'] = xarModGetVar('netquery', 'query_email_server');
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
    $data['cssfiles'] = xarModAPIFunc('netquery', 'admin', 'getcssfiles', './modules/netquery/xarstyles');
    $data['portsubmits'] = xarModAPIFunc('netquery', 'admin', 'countportflag', array('flag' => '99'));
    $data['bbstats'] = xarModAPIFunc('netquery', 'user', 'bb2_stats');
    $data['bbsettings'] = xarModAPIFunc('netquery', 'user', 'bb2_settings');
    $mappingsites = array();
      $mappingsites[] = array('name' => 'None', 'value' => 0);
      $mappingsites[] = array('name' => 'MapQuest', 'value' => 1);
      $mappingsites[] = array('name' => 'MultiMap', 'value' => 2);
    $data['mappingsites'] = $mappingsites;
    $topcountries = array();
      $topcountries[] = array('name' => '5', 'value' => 5);
      $topcountries[] = array('name' => '10', 'value' => 10);
      $topcountries[] = array('name' => '15', 'value' => 15);
      $topcountries[] = array('name' => '20', 'value' => 20);
      $topcountries[] = array('name' => '25', 'value' => 25);
      $topcountries[] = array('name' => 'All', 'value' => 100000);
    $data['topcountries'] = $topcountries;
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
    if (file_exists('modules/netquery/xaradmin/xageoip.php'))
    {
        $data['xaglink'] = Array('url'   => xarModURL('netquery', 'admin', 'xageoip', array('step' => '1')),
                                 'title' => xarML('Install new GeoIP data table'),
                                 'label' => xarML('Install GeoIP Data'));
    }
    else
    {
        $data['xaglink'] = Array('url'   => 'http://www.virtech.org/tools/',
                                 'title' => xarML('Get GeoIP data installer option'),
                                 'label' => xarML('Get GeoIP Data'));
    }
    $data['flvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'flview'),
                             'title' => xarML('Edit service/exploit category flags'),
                             'label' => xarML('Edit Category Flags'));
    $data['wivlink'] = Array('url'   => xarModURL('netquery', 'admin', 'wiview'),
                             'title' => xarML('Edit whois TLD/server links'),
                             'label' => xarML('Edit Whois TLDs'));
    $data['lgvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'lgview'),
                             'title' => xarML('Edit looking glass settings'),
                             'label' => xarML('Edit LG Settings'));
    $data['ptvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'ptview'),
                             'title' => xarML('Edit services/exploits'),
                             'label' => xarML('Edit Port Services'));
    $data['bbllink'] = Array('url'   => xarModURL('netquery', 'admin', 'bblogedit'),
                             'title' => xarML('Manage spambot blocker Log'),
                             'label' => xarML('Manage Blocker Log'));
    $data['p99link'] = Array('url'   => xarModURL('netquery', 'admin', 'ptview', array('pflag' => '99')),
                             'title' => $data['portsubmits'].' '. xarML('New for Reflagging'),
                             'label' => xarML('None for Reflagging'));
    if (file_exists('modules/netquery/xaradmin/xaports.php'))
    {
        $data['xaplink'] = Array('url'   => xarModURL('netquery', 'admin', 'xaports', array('step' => '1')),
                                 'title' => xarML('Install new ports data table'),
                                 'label' => xarML('Install Ports Data'));
    }
    else
    {
        $data['xaplink'] = Array('url'   => 'http://www.virtech.org/tools/',
                                 'title' => xarML('Get ports data installer option'),
                                 'label' => xarML('Get Ports Data'));
    }
    $data['hlplink'] = Array('url'   => 'modules/netquery/xardocs/manual.html#admin',
                             'title' => xarML('Netquery online manual'),
                             'label' => xarML('Online Manual'));
    if (file_exists($data['capture_log_filepath']))
    {
        $data['loglink'] = Array('url'   => xarML($data['capture_log_filepath']),
                                 'title' => xarML('View operations logfile'),
                                 'label' => xarML('View Log'));
        $data['clearlog'] = Array('url'  => xarModURL('netquery', 'admin', 'clearlog'),
                                 'title' => xarML('Clear operations log data'),
                                 'label' => xarML('Clear Log'));
    }
    else
    {
        $data['loglink'] = '';
        $data['clearlog'] = '';
    }
    return $data;
}
?>