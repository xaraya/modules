<?php
function netquery_userapi_mainapi()
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
    $data['user_submissions'] = xarModGetVar('netquery', 'user_submissions');
    $data['pingexec'] = xarModAPIFunc('netquery', 'user', 'getexec', array('exec_type' => 'ping'));
    $data['traceexec'] = xarModAPIFunc('netquery', 'user', 'getexec', array('exec_type' => 'trace'));
    $data['logfile'] = xarModAPIFunc('netquery', 'user', 'getexec', array('exec_type' => 'log'));
    $data['links'] = xarModAPIFunc('netquery', 'user', 'getlinks');
    $data['lgrequests'] = xarModAPIFunc('netquery', 'user', 'getlgrequests');
    $data['lgrouters'] = xarModAPIFunc('netquery', 'user', 'getlgrouters');
    $data['lgdefault'] = xarModAPIFunc('netquery', 'user', 'getlgrouter', array('router' => 'default'));
    $data['maintitle']  = xarVarPrepForDisplay(xarML('Netquery'));
    $data['subtitle']   = xarVarPrepForDisplay(xarML('Netquery User Options'));
    $data['domainlabel'] = xarVarPrepForDisplay(xarML('Whois Domain Name (No www.)'));
    $data['extlabel'] = xarVarPrepForDisplay(xarML('Domain'));
    $data['whoisiplabel'] = xarVarPrepForDisplay(xarML('Whois IP Address or AS#####'));
    $data['lookuplabel'] = xarVarPrepForDisplay(xarML('Lookup IP Address or Host Name'));
    $data['diglabel'] = xarVarPrepForDisplay(xarML('Lookup (Dig) IP or Host Name'));
    $data['digparamlabel'] = xarVarPrepForDisplay(xarML('Parameter'));
    $data['serverlabel'] = xarVarPrepForDisplay(xarML('Port Check Host (Optional)'));
    $data['portnumlabel'] = xarVarPrepForDisplay(xarML('Port'));
    $data['httpurllabel'] = xarVarPrepForDisplay(xarML('HTTP Request Object URL'));
    $data['httpreqlabel'] = xarVarPrepForDisplay(xarML('Request'));
    $data['pinglabel'] = xarVarPrepForDisplay(xarML('Ping IP Address or Host Name'));
    $data['maxplabel'] = xarVarPrepForDisplay(xarML('Count'));
    $data['pingremotelabel'] = xarVarPrepForDisplay(xarML('Ping IP or Host Name - Remote'));
    $data['tracelabel'] = xarVarPrepForDisplay(xarML('Traceroute IP or Host Name'));
    $data['traceremotelabel'] = xarVarPrepForDisplay(xarML('Traceroute IP or Host - Remote'));
    $data['lgrequestlabel'] = xarVarPrepForDisplay(xarML('Looking Glass Query'));
    $data['lgparamlabel'] = xarVarPrepForDisplay(xarML('Parameter'));
    $data['lgrouterlabel'] = xarVarPrepForDisplay(xarML('Router'));
    $data['j'] = 0;
    $data['results'] = '';
    $wiexample = 'example';
    $j = 1;
    while ($j <= $data['whois_max_limit']) {
        $dom = "domain_".$j;
        $tld = "whois_ext_".$j;
        xarVarFetch($dom, 'str:1:', $domain[$j], $wiexample, XARVAR_NOT_REQUIRED);
        xarVarFetch($tld, 'str:1:', $whois_ext[$j], '.com', XARVAR_NOT_REQUIRED);
        $wiexample = '';
        $j++;
    }
    $data['domain'] = $domain;
    $data['whois_ext'] = $whois_ext;
    $data['winsys'] = (DIRECTORY_SEPARATOR == '\\');
    $data['maxpoptions'] = array(4, 5, 6, 7, 8, 9, 10);
    $data['httpoptions'] = array('HEAD', 'GET');
    $digoptions = array();
      $digoptions[] = array('name' => 'ANY', 'value' => 'ANY');
      $digoptions[] = array('name' => 'Mail eXchanger', 'value' => 'MX');
      $digoptions[] = array('name' => 'Start Of Authority', 'value' => 'SOA');
      $digoptions[] = array('name' => 'Name Servers', 'value' => 'NS');
    $data['digoptions'] = $digoptions;
    xarVarFetch('maxp', 'int:1:10', $data['maxp'], '4', XARVAR_NOT_REQUIRED);
    xarVarFetch('host', 'str:1:', $data['host'], $_SERVER['REMOTE_ADDR'], XARVAR_NOT_REQUIRED);
    xarVarFetch('server', 'str:1:', $data['server'], 'None', XARVAR_NOT_REQUIRED);
    xarVarFetch('portnum', 'int:1:100000', $data['portnum'], '80', XARVAR_NOT_REQUIRED);
    xarVarFetch('httpurl', 'str:1:', $data['httpurl'], 'http://'.$_SERVER['SERVER_NAME'].'/', XARVAR_NOT_REQUIRED);
    xarVarFetch('httpreq', 'str:1:', $data['httpreq'], 'HEAD', XARVAR_NOT_REQUIRED);
    xarVarFetch('request', 'str:1:', $data['request'], 'IPv4 BGP neighborship', XARVAR_NOT_REQUIRED);
    xarVarFetch('lgparam', 'str:1:', $data['lgparam'], '', XARVAR_NOT_REQUIRED);
    xarVarFetch('digparam', 'str:1:', $data['digparam'], 'ANY', XARVAR_NOT_REQUIRED);
    xarVarFetch('router', 'str:1:', $data['router'], 'ATT Public', XARVAR_NOT_REQUIRED);
    xarVarFetch('querytype', 'str:1:', $data['querytype'], 'none', XARVAR_NOT_REQUIRED);
    xarVarFetch('formtype', 'str:1:', $data['formtype'], $data['querytype'], XARVAR_NOT_REQUIRED);
    if (isset($_REQUEST['b1']) || isset($_REQUEST['b1_x'])) {$data['formtype'] = 'whois';}
    if (isset($_REQUEST['b2']) || isset($_REQUEST['b2_x'])) {$data['formtype'] = 'whoisip';}
    if (isset($_REQUEST['b3']) || isset($_REQUEST['b3_x'])) {$data['formtype'] = 'lookup';}
    if (isset($_REQUEST['b4']) || isset($_REQUEST['b4_x'])) {$data['formtype'] = 'dig';}
    if (isset($_REQUEST['b5']) || isset($_REQUEST['b5_x'])) {$data['formtype'] = 'port';}
    if (isset($_REQUEST['b6']) || isset($_REQUEST['b6_x'])) {$data['formtype'] = 'http';}
    if (isset($_REQUEST['b7']) || isset($_REQUEST['b7_x'])) {$data['formtype'] = 'ping';}
    if (isset($_REQUEST['b8']) || isset($_REQUEST['b8_x'])) {$data['formtype'] = 'pingrem';}
    if (isset($_REQUEST['b9']) || isset($_REQUEST['b9_x'])) {$data['formtype'] = 'trace';}
    if (isset($_REQUEST['b10']) || isset($_REQUEST['b10_x'])) {$data['formtype'] = 'tracerem';}
    if (isset($_REQUEST['b11']) || isset($_REQUEST['b11_x'])) {$data['formtype'] = 'lgquery';}
    $logfile = $data['logfile'];
    if ($data['formtype'] == 'none') {$data['formtype'] = $logfile['exec_remote_t'];}
    $data['b1class']  = ($data['formtype'] == 'whois') ? 'inset' : 'outset';
    $data['b2class']  = ($data['formtype'] == 'whoisip') ? 'inset' : 'outset';
    $data['b3class']  = ($data['formtype'] == 'lookup') ? 'inset' : 'outset';
    $data['b4class']  = ($data['formtype'] == 'dig') ? 'inset' : 'outset';
    $data['b5class']  = ($data['formtype'] == 'port') ? 'inset' : 'outset';
    $data['b6class']  = ($data['formtype'] == 'http') ? 'inset' : 'outset';
    $data['b7class']  = ($data['formtype'] == 'ping') ? 'inset' : 'outset';
    $data['b8class']  = ($data['formtype'] == 'pingrem') ? 'inset' : 'outset';
    $data['b9class']  = ($data['formtype'] == 'trace') ? 'inset' : 'outset';
    $data['b10class'] = ($data['formtype'] == 'tracerem') ? 'inset' : 'outset';
    $data['b11class'] = ($data['formtype'] == 'lgquery') ? 'inset' : 'outset';
    $data['clrlink'] = Array('url' => xarModURL('netquery', 'user', 'main', array('formtype' => $data['formtype'])),
                             'title' => xarML('Clear results and return'),
                             'label' => xarML('Clear'));
    $data['submitlink'] = Array('url' => xarModURL('netquery', 'user', 'submit', array('portnum' => $data['portnum'])),
                             'title' => xarML('Submit new service/exploit'),
                             'label' => xarML('Submit'));
    $data['hlplink'] = Array('url' => xarML('modules/netquery/xardocs/manual.html#using'),
                             'title' => xarML('Netquery online user manual'),
                             'label' => xarML('Online Manual'));
    return $data;
}
?>