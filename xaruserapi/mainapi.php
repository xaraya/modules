<?php
function netquery_userapi_mainapi()
{
    include_once "modules/netquery/xarincludes/nqSniff.class.php";
    include_once "modules/netquery/xarincludes/nqTimer.class.php";
    $data = array();
    $data['timer'] =& new nqTimer();
    $data['timer']->start('main');
    $data['authid'] = xarSecGenAuthKey();
    $data['maintitle']  = xarVarPrepForDisplay(xarML('Netquery'));
    $data['subtitle']   = xarVarPrepForDisplay(xarML('Netquery User Options'));
    $data['domainlabel'] = xarVarPrepForDisplay(xarML('Whois Domain Name (No www.)'));
    $data['extlabel'] = xarVarPrepForDisplay(xarML('Domain'));
    $data['whoisiplabel'] = xarVarPrepForDisplay(xarML('Whois IP Address or AS#####'));
    $data['lookuplabel'] = xarVarPrepForDisplay(xarML('Lookup IP Address or Host Name'));
    $data['diglabel'] = xarVarPrepForDisplay(xarML('Lookup (Dig) IP or Host Name'));
    $data['digparamlabel'] = xarVarPrepForDisplay(xarML('Parameter'));
    $data['emaillabel'] = xarVarPrepForDisplay(xarML('Validate Email Address'));
    $data['serverlabel'] = xarVarPrepForDisplay(xarML('Port Check Host (Optional)'));
    $data['portnumlabel'] = xarVarPrepForDisplay(xarML('Port'));
    $data['httpurllabel'] = xarVarPrepForDisplay(xarML('HTTP Request Object URL'));
    $data['httpreqlabel'] = xarVarPrepForDisplay(xarML('Request'));
    $data['pinglabel'] = xarVarPrepForDisplay(xarML('Ping IP Address or Host Name'));
    $data['maxplabel'] = xarVarPrepForDisplay(xarML('Count'));
    $data['maxtlabel'] = xarVarPrepForDisplay(xarML('Count'));
    $data['pingremotelabel'] = xarVarPrepForDisplay(xarML('Ping IP or Host Name - Remote'));
    $data['tracelabel'] = xarVarPrepForDisplay(xarML('Traceroute IP or Host Name'));
    $data['traceremotelabel'] = xarVarPrepForDisplay(xarML('Traceroute IP or Host - Remote'));
    $data['lgrequestlabel'] = xarVarPrepForDisplay(xarML('Looking Glass Query'));
    $data['lgparamlabel'] = xarVarPrepForDisplay(xarML('Parameter'));
    $data['lgrouterlabel'] = xarVarPrepForDisplay(xarML('Router'));
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
    $data['browserinfo'] =& new nqSniff();
    $data['geoip'] = xarModAPIFunc('netquery', 'user', 'getgeoip', array('ip' => $data['browserinfo']->property('ip')));
    $data['countries'] = xarModAPIFunc('netquery', 'user', 'getcountries', array('numitems' => $data['topcountries_limit']));
    $data['links'] = xarModAPIFunc('netquery', 'user', 'getlinks');
    $data['lgrouters'] = xarModAPIFunc('netquery', 'user', 'getlgrouters', array('startnum' => '2'));
    $data['lgdefault'] = xarModAPIFunc('netquery', 'user', 'getlgrouter', array('router' => 'default'));
    $data['results'] = '';
    $data['j'] = 0;
    $data['winsys'] = (DIRECTORY_SEPARATOR == '\\');
    $data['maxpoptions'] = array(4, 5, 6, 7, 8, 9, 10);
    $data['maxtoptions'] = array(10, 20, 30, 40, 50, 60);
    $data['httpoptions'] = array('HEAD', 'GET');
    $digoptions = array();
      $digoptions[] = array('name' => 'ANY', 'value' => 'ANY');
      $digoptions[] = array('name' => 'Mail eXchanger', 'value' => 'MX');
      $digoptions[] = array('name' => 'Start Of Authority', 'value' => 'SOA');
      $digoptions[] = array('name' => 'Name Servers', 'value' => 'NS');
    $data['digoptions'] = $digoptions;
    $lgrequests = array();
      $lgrequests[] = array('request' => 'IPv4 OSPF neighborship', 'command' => 'show ip ospf neighbor', 'handler' => 'ospfd', 'argc' => '0');
      $lgrequests[] = array('request' => 'IPv4 BGP neighborship', 'command' => 'show ip bgp summary', 'handler' => 'bgpd', 'argc' => '0');
      $lgrequests[] = array('request' => 'IPv4 OSPF RT', 'command' => 'show ip ospf route', 'handler' => 'ospfd', 'argc' => '0');
      $lgrequests[] = array('request' => 'IPv4 BGP RR to...', 'command' => 'show ip bgp', 'handler' => 'bgpd', 'argc' => '1');
      $lgrequests[] = array('request' => 'IPv4 any RR to...', 'command' => 'show ip route', 'handler' => 'zebra', 'argc' => '1');
      $lgrequests[] = array('request' => 'Interface info on...', 'command' => 'show interface', 'handler' => 'zebra', 'argc' => '1');
      $lgrequests[] = array('request' => 'IPv6 OSPF neighborship', 'command' => 'show ipv6 ospf neighbor', 'handler' => 'ospf6d', 'argc' => '0');
      $lgrequests[] = array('request' => 'IPv6 BGP neighborship', 'command' => 'show ipv6 bgp summary', 'handler' => 'ripngd', 'argc' => '0');
      $lgrequests[] = array('request' => 'IPv6 OSPF RT', 'command' => 'show ipv6 ospf route', 'handler' => 'ospf6d', 'argc' => '0');
      $lgrequests[] = array('request' => 'IPv6 BGP route to...', 'command' => 'show ipv6 bgp', 'handler' => 'ripngd', 'argc' => '1');
      $lgrequests[] = array('request' => 'IPv6 any route to...', 'command' => 'show ipv6 route', 'handler' => 'zebra', 'argc' => '1');
    $data['lgrequests'] = $lgrequests;
    $wiexample = 'example';
    $j = 1;
    while ($j <= $data['whois_max_limit']) {
      $dom = "domain_".$j;
      $tld = "whois_tld_".$j;
      xarVarFetch($dom, 'str:1:', $domain[$j], $wiexample, XARVAR_NOT_REQUIRED);
      xarVarFetch($tld, 'str:1:', $whois_tld[$j], $data['whois_default'], XARVAR_NOT_REQUIRED);
      $wiexample = '';
      $j++;
    }
    $data['domain'] = $domain;
    $data['whois_tld'] = $whois_tld;
    xarVarFetch('maxp', 'int:1:10', $data['maxp'], '4', XARVAR_NOT_REQUIRED);
    xarVarFetch('maxt', 'int:1:100', $data['maxt'], '30', XARVAR_NOT_REQUIRED);
    xarVarFetch('host', 'str:1:', $data['host'], $_SERVER['REMOTE_ADDR'], XARVAR_NOT_REQUIRED);
    xarVarFetch('email', 'str:1:', $data['email'], 'someone@'.gethostbyaddr($_SERVER['REMOTE_ADDR']), XARVAR_NOT_REQUIRED);
    xarVarFetch('server', 'str:1:', $data['server'], 'None', XARVAR_NOT_REQUIRED);
    xarVarFetch('portnum', 'int:1:100000', $data['portnum'], '80', XARVAR_NOT_REQUIRED);
    xarVarFetch('httpurl', 'str:1:', $data['httpurl'], 'http://'.$_SERVER['SERVER_NAME'].'/', XARVAR_NOT_REQUIRED);
    xarVarFetch('httpreq', 'str:1:', $data['httpreq'], 'HEAD', XARVAR_NOT_REQUIRED);
    xarVarFetch('request', 'int:1:100000', $data['request'], '1', XARVAR_NOT_REQUIRED);
    xarVarFetch('lgparam', 'str:1:', $data['lgparam'], '', XARVAR_NOT_REQUIRED);
    xarVarFetch('digparam', 'str:1:', $data['digparam'], 'ANY', XARVAR_NOT_REQUIRED);
    xarVarFetch('router', 'str:1:', $data['router'], 'ATT Public', XARVAR_NOT_REQUIRED);
    xarVarFetch('querytype', 'str:1:', $data['querytype'], 'none', XARVAR_NOT_REQUIRED);
    xarVarFetch('formtype', 'str:1:', $data['formtype'], $data['querytype'], XARVAR_NOT_REQUIRED);
    if ($data['formtype'] == 'none' || $data['formtype'] == 'countries') {$data['formtype'] = $data['querytype_default'];}
    if (isset($_REQUEST['b1']) || isset($_REQUEST['b1_x'])) {$data['formtype'] = 'whois'; $data['querytype'] = 'none';}
    if (isset($_REQUEST['b2']) || isset($_REQUEST['b2_x'])) {$data['formtype'] = 'whoisip'; $data['querytype'] = 'none';}
    if (isset($_REQUEST['b3']) || isset($_REQUEST['b3_x'])) {$data['formtype'] = 'lookup'; $data['querytype'] = 'none';}
    if (isset($_REQUEST['b4']) || isset($_REQUEST['b4_x'])) {$data['formtype'] = 'dig'; $data['querytype'] = 'none';}
    if (isset($_REQUEST['b5']) || isset($_REQUEST['b5_x'])) {$data['formtype'] = 'port'; $data['querytype'] = 'none';}
    if (isset($_REQUEST['b6']) || isset($_REQUEST['b6_x'])) {$data['formtype'] = 'http'; $data['querytype'] = 'none';}
    if (isset($_REQUEST['b7']) || isset($_REQUEST['b7_x'])) {$data['formtype'] = 'ping'; $data['querytype'] = 'none';}
    if (isset($_REQUEST['b8']) || isset($_REQUEST['b8_x'])) {$data['formtype'] = 'pingrem'; $data['querytype'] = 'none';}
    if (isset($_REQUEST['b9']) || isset($_REQUEST['b9_x'])) {$data['formtype'] = 'trace'; $data['querytype'] = 'none';}
    if (isset($_REQUEST['b10']) || isset($_REQUEST['b10_x'])) {$data['formtype'] = 'tracerem'; $data['querytype'] = 'none';}
    if (isset($_REQUEST['b11']) || isset($_REQUEST['b11_x'])) {$data['formtype'] = 'lgquery'; $data['querytype'] = 'none';}
    if (isset($_REQUEST['b12']) || isset($_REQUEST['b12_x'])) {$data['formtype'] = 'email'; $data['querytype'] = 'none';}
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
    $data['b12class'] = ($data['formtype'] == 'email') ? 'inset' : 'outset';
    $data['clrlink'] = Array('url' => xarModURL('netquery', 'user', 'main', array('formtype' => $data['formtype'])),
                             'title' => xarML('Clear results and return'),
                             'label' => xarML('Clear'));
    $data['countrieslink'] = Array('url' => xarModURL('netquery', 'user', 'main', array('querytype' => 'countries')),
                             'title' => xarML('Display top user countries'),
                             'label' => xarML('Top Countries'));
    $data['returnlink'] = Array('url' => xarModURL('netquery', 'user', 'main'),
                             'title' => xarML('Return to main user interface'),
                             'label' => xarML('User Interface'));
    $data['submitlink'] = Array('url' => xarModURL('netquery', 'user', 'submit', array('portnum' => $data['portnum'])),
                             'title' => xarML('Submit new service/exploit'),
                             'label' => xarML('Submit'));
    $data['hlplink'] = Array('url' => 'modules/netquery/xardocs/manual.html#using',
                             'title' => xarML('Netquery online user manual'),
                             'label' => xarML('Online Manual'));
    $data['manlink'] = 'modules/netquery/xardocs/manual.html';
    if (file_exists($data['capture_log_filepath'])) {
        $data['loglink'] = Array('url'   => xarML($data['capture_log_filepath']),
                                 'title' => xarML('View operations logfile'),
                                 'label' => xarML('View Log'));
    } else {
        $data['loglink'] = '';
    }
    return $data;
}
function sanitizeSysString($string, $min = '', $max = '')
{
  $pattern = '/(;|\||`|>|<|&|^|"|'."\n|\r|'".'|{|}|[|]|\)|\()/i';
  $string = preg_replace($pattern, '', $string);
  $string = preg_replace('/\$/', '\\\$', $string);
  $len = strlen($string);
  if((($min != '') && ($len < $min)) || (($max != '') && ($len > $max)))
    return FALSE;
  return $string;
}
if (!function_exists('checkdnsrr'))
{
  function checkdnsrr($host, $type = '')
  {
    $digexec_local = xarModGetVar('netquery', 'digexec_local');
    if(!empty($host)) {
      if($type == '') $type = "MX";
      $output = '';
      $k = '';
      $line = '';
      @exec("$digexec_local -type=$type $host", $output);
      while(list($k, $line) = each($output)) {
        if(eregi("^$host", $line)) {
          return true;
        }
      }
      return false;
    }
  }
}
if (!function_exists('getmxrr'))
{
  function getmxrr($hostname, &$mxhosts)
  {
    $digexec_local = xarModGetVar('netquery', 'digexec_local');
    if (!is_array($mxhosts)) $mxhosts = array();
    if (!empty($hostname )) {
      $output = '';
      $ret = '';
      $k = '';
      $line = '';
      @exec("$digexec_local -type=MX $hostname", $output, $ret);
      while (list($k, $line) = each($output)) {
        if (ereg("^$hostname\tMX preference = ([0-9]+), mail exchanger = (.*)$", $line, $parts)) {
          $mxhosts[$parts[1]]=$parts[2];
        }
      }
      if (count($mxhosts)) {
        reset($mxhosts);
        ksort($mxhosts);
        $i = 0;
        while (list($pref,$host) = each($mxhosts)) {
          $mxhosts2[$i] = $host;
          $i++;
        }
        $mxhosts = $mxhosts2;
        return true;
      } else {
        return false;
      }
    }
  }
}
?>