<?php
function netquery_userapi_mainapi()
{
    $data = array();
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
    $data['clrlink'] = Array('url' => xarModURL('netquery', 'user', 'main'),
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
function netquery_userapi_getexec($args)
{
    extract($args);
    if (!isset($exec_type)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ExecTable = $xartable['netquery_exec'];
    $query = "SELECT * FROM $ExecTable WHERE exec_type = ?";
    $bindvars = array($exec_type);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($exec_id, $exec_type, $exec_local, $exec_winsys, $exec_remote, $exec_remote_t) = $result->fields;
    if(!xarSecurityCheck('OverviewNetquery')) return;
    $exec = array('exec_id'        => $exec_id,
                  'exec_type'      => $exec_type,
                  'exec_local'     => $exec_local,
                  'exec_winsys'    => $exec_winsys,
                  'exec_remote'    => $exec_remote,
                  'exec_remote_t'  => $exec_remote_t);
    $result->Close();
    return $exec;
}
function netquery_userapi_getflags($args)
{
    extract($args);
    if ((!isset($startnum)) || (!is_numeric($startnum))) {
        $startnum = 1;
    }
    if ((!isset($numitems)) || (!is_numeric($numitems))) {
        $numitems = -1;
    }
    $flags = array();
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    $FlagsTable = $xartable['netquery_flags'];
    $query = "SELECT * FROM $FlagsTable ORDER BY flagnum";
    $result =& $dbconn->SelectLimit($query, (int)$numitems, (int)$startnum-1);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($flag_id, $flagnum, $keyword, $fontclr, $backclr, $lookup_1, $lookup_2) = $result->fields;
        $flags[] = array('flag_id'  => $flag_id,
                         'flagnum'  => $flagnum,
                         'keyword'  => $keyword,
                         'fontclr'  => $fontclr,
                         'backclr'  => $backclr,
                         'lookup_1' => $lookup_1,
                         'lookup_2' => $lookup_2);
    }
    $result->Close();
    return $flags;
}
function netquery_userapi_getflagdata($args)
{
    extract($args);
    if (!isset($flagnum)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $FlagsTable = $xartable['netquery_flags'];
    $query = "SELECT * FROM $FlagsTable WHERE flagnum = ?";
    $bindvars = array($flagnum);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($flag_id, $flagnum, $keyword, $fontclr, $backclr, $lookup_1, $lookup_2) = $result->fields;
    if(!xarSecurityCheck('OverviewNetquery')) return;
    $flagdata = array('flag_id'  => $flag_id,
                      'flagnum'  => $flagnum,
                      'keyword'  => $keyword,
                      'fontclr'  => $fontclr,
                      'backclr'  => $backclr,
                      'lookup_1' => $lookup_1,
                      'lookup_2' => $lookup_2);
    $result->Close();
    return $flagdata;
}
function netquery_userapi_getlinks($args)
{
    extract($args);
    if ((!isset($startnum)) || (!is_numeric($startnum))) {
        $startnum = 1;
    }
    if ((!isset($numitems)) || (!is_numeric($numitems))) {
        $numitems = -1;
    }
    $links = array();
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];
    $query = "SELECT * FROM $WhoisTable ORDER BY whois_ext";
    $result =& $dbconn->SelectLimit($query, (int)$numitems, (int)$startnum-1);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($whois_id, $whois_ext, $whois_server) = $result->fields;
        $links[] = array('whois_id' => $whois_id,
                         'whois_ext' => $whois_ext,
                         'whois_server' => $whois_server);
    }
    $result->Close();
    return $links;
}
function netquery_userapi_getlink($args)
{
    extract($args);
    if (!isset($whois_ext)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];
    $query = "SELECT * FROM $WhoisTable WHERE whois_ext = ?";
    $bindvars = array($whois_ext);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($whois_id, $whois_ext, $whois_server) = $result->fields;
    $link = array('whois_id'     => $whois_id,
                  'whois_ext'    => $whois_ext,
                  'whois_server' => $whois_server);
    $result->Close();
    return $link;
}
function netquery_userapi_getportdata($args)
{
    extract($args);
    if (!isset($port)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $portdata = array();
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $PortsTable = $xartable['netquery_ports'];
    $query = "SELECT * FROM $PortsTable WHERE flag < 99 AND port = ?";
    $bindvars = array($port);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($port_id, $port, $protocol, $service, $comment, $flag) = $result->fields;
        $portdata[] = array('port_id'  => $port_id,
                            'port'     => $port,
                            'protocol' => $protocol,
                            'service'  => $service,
                            'comment'  => $comment,
                            'flag'     => $flag);
    }
    $result->Close();
    return $portdata;
}
function netquery_userapi_getlgrequests($args)
{
    extract($args);
    if ((!isset($startnum)) || (!is_numeric($startnum))) {
        $startnum = 1;
    }
    if ((!isset($numitems)) || (!is_numeric($numitems))) {
        $numitems = -1;
    }
    $lgrequests = array();
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    $LGRequestTable = $xartable['netquery_lgrequest'];
    $query = "SELECT * FROM $LGRequestTable ORDER BY request_id";
    $result =& $dbconn->SelectLimit($query, (int)$numitems, (int)$startnum-1);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($request_id, $request, $command, $handler, $argc) = $result->fields;
        $lgrequests[] = array('request_id' => $request_id,
                              'request'    => $request,
                              'command'    => $command,
                              'handler'    => $handler,
                              'argc'       => $argc);
    }
    $result->Close();
    return $lgrequests;
}
function netquery_userapi_getlgrequest($args)
{
    extract($args);
    if (!isset($request)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    $LGRequestTable = $xartable['netquery_lgrequest'];
    $query = "SELECT * FROM $LGRequestTable WHERE request = ?";
    $bindvars = array($request);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($request_id, $request, $command, $handler, $argc) = $result->fields;
    $lgrequest = array('request_id' => $request_id,
                       'request'    => $request,
                       'command'    => $command,
                       'handler'    => $handler,
                       'argc'       => $argc);
    $result->Close();
    return $lgrequest;
}
function netquery_userapi_getlgrouters($args)
{
    extract($args);
    if ((!isset($startnum)) || (!is_numeric($startnum))) {
        $startnum = 1;
    }
    if ((!isset($numitems)) || (!is_numeric($numitems))) {
        $numitems = -1;
    }
    $lgrouters = array();
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    $LGRouterTable = $xartable['netquery_lgrouter'];
    $query = "SELECT * FROM $LGRouterTable WHERE router != 'default' ORDER BY router_id";
    $result =& $dbconn->SelectLimit($query, (int)$numitems, (int)$startnum-1);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($router_id,
             $router,
             $address,
             $username,
             $password,
             $zebra,
             $zebra_port,
             $zebra_password,
             $ripd,
             $ripd_port,
             $ripd_password,
             $ripngd,
             $ripngd_port,
             $ripngd_password,
             $ospfd,
             $ospfd_port,
             $ospfd_password,
             $bgpd,
             $bgpd_port,
             $bgpd_password,
             $ospf6d,
             $ospf6d_port,
             $ospf6d_password,
             $use_argc) = $result->fields;
        $lgrouters[] = array('router_id'       => $router_id,
                             'router'          => $router,
                             'address'         => $address,
                             'username'        => $username,
                             'password'        => $username,
                             'zebra'           => $zebra,
                             'zebra_port'      => $zebra_port,
                             'zebra_password'  => $zebra_password,
                             'ripd'            => $ripd,
                             'ripd_port'       => $ripd_port,
                             'ripd_password'   => $zebra_password,
                             'ripngd'          => $ripngd,
                             'ripngd_port'     => $ripngd_port,
                             'ripngd_password' => $ripngd_password,
                             'ospfd'           => $ospfd,
                             'ospfd_port'      => $ospfd_port,
                             'ospfd_password'  => $ospfd_password,
                             'bgpd'            => $bgpd,
                             'bgpd_port'       => $bgpd_port,
                             'bgpd_password'   => $bgpd_password,
                             'ospf6d'          => $ospf6d,
                             'ospf6d_port'     => $ospf6d_port,
                             'ospf6d_password' => $ospf6d_password,
                             'use_argc'        => $use_argc);
    }
    $result->Close();
    return $lgrouters;
}
function netquery_userapi_getlgrouter($args)
{
    extract($args);
    if (!isset($router)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    $LGRouterTable = $xartable['netquery_lgrouter'];
    $query = "SELECT * FROM $LGRouterTable WHERE router = ?";
    $bindvars = array($router);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($router_id,
         $router,
         $address,
         $username,
         $password,
         $zebra,
         $zebra_port,
         $zebra_password,
         $ripd,
         $ripd_port,
         $ripd_password,
         $ripngd,
         $ripngd_port,
         $ripngd_password,
         $ospfd,
         $ospfd_port,
         $ospfd_password,
         $bgpd,
         $bgpd_port,
         $bgpd_password,
         $ospf6d,
         $ospf6d_port,
         $ospf6d_password,
         $use_argc) = $result->fields;
    $lgrouter = array('router_id'       => $router_id,
                      'router'          => $router,
                      'address'         => $address,
                      'username'        => $username,
                      'password'        => $username,
                      'zebra'           => $zebra,
                      'zebra_port'      => $zebra_port,
                      'zebra_password'  => $zebra_password,
                      'ripd'            => $ripd,
                      'ripd_port'       => $ripd_port,
                      'ripd_password'   => $zebra_password,
                      'ripngd'          => $ripngd,
                      'ripngd_port'     => $ripngd_port,
                      'ripngd_password' => $ripngd_password,
                      'ospfd'           => $ospfd,
                      'ospfd_port'      => $ospfd_port,
                      'ospfd_password'  => $ospfd_password,
                      'bgpd'            => $bgpd,
                      'bgpd_port'       => $bgpd_port,
                      'bgpd_password'   => $bgpd_password,
                      'ospf6d'          => $ospf6d,
                      'ospf6d_port'     => $ospf6d_port,
                      'ospf6d_password' => $ospf6d_password,
                      'use_argc'        => $use_argc);
    $result->Close();
    return $lgrouter;
}
?>
