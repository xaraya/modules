<?php
/**
 * File: $Id:
 */
function netquery_userapi_mainapi()
{ 
    $data = array(); 
    $data['results'] = '';
    xarVarFetch('domain', 'str:1:', $data['domain'], 'example', XARVAR_NOT_REQUIRED);
    xarVarFetch('whois_ext', 'str:1:', $data['whois_ext'], '.com', XARVAR_NOT_REQUIRED);
    xarVarFetch('addr', 'str:1:', $data['addr'], $_SERVER['REMOTE_ADDR'], XARVAR_NOT_REQUIRED);
    xarVarFetch('host', 'str:1:', $data['host'], $_SERVER['REMOTE_HOST'], XARVAR_NOT_REQUIRED);
    xarVarFetch('server', 'str:1:', $data['server'], $_SERVER['SERVER_NAME'], XARVAR_NOT_REQUIRED);
    xarVarFetch('maxp', 'int:1:', $data['maxp'], '4', XARVAR_NOT_REQUIRED);
    xarVarFetch('portnum', 'int:1:', $data['portnum'], '80', XARVAR_NOT_REQUIRED);
    xarVarFetch('request', 'str:1:', $data['request'], 'IPv4 BGP neighborship', XARVAR_NOT_REQUIRED);
    xarVarFetch('lgparam', 'int:1:', $data['lgparam'], '', XARVAR_NOT_REQUIRED);
    xarVarFetch('router', 'int:1:', $data['router'], 'ATT Public', XARVAR_NOT_REQUIRED);
    xarVarFetch('querytype', 'str:1:', $data['querytype'], 'none', XARVAR_NOT_REQUIRED);
    $data['whois_enabled'] = xarModGetVar('netquery', 'whois_enabled');
    $data['whoisip_enabled'] = xarModGetVar('netquery', 'whoisip_enabled');
    $data['dns_lookup_enabled'] = xarModGetVar('netquery', 'dns_lookup_enabled');
    $data['dns_dig_enabled'] = xarModGetVar('netquery', 'dns_dig_enabled');
    $data['ping_enabled'] = xarModGetVar('netquery', 'ping_enabled');
    $data['ping_remote_enabled'] = xarModGetVar('netquery', 'ping_remote_enabled');
    $data['trace_enabled'] = xarModGetVar('netquery', 'trace_enabled');
    $data['trace_remote_enabled'] = xarModGetVar('netquery', 'trace_remote_enabled');
    $data['port_check_enabled'] = xarModGetVar('netquery', 'port_check_enabled');
    $data['looking_glass_enabled'] = xarModGetVar('netquery', 'looking_glass_enabled');
    $data['capture_log_enabled'] = xarModGetVar('netquery', 'capture_log_enabled');
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
    $data['whoisiplabel'] = xarVarPrepForDisplay(xarML('Whois IP Address'));
    $data['lookuplabel'] = xarVarPrepForDisplay(xarML('Lookup IP Address or Host Name'));
    $data['diglabel'] = xarVarPrepForDisplay(xarML('Lookup (Dig) IP or Host Name'));
    $data['pinglabel'] = xarVarPrepForDisplay(xarML('Ping IP Address or Host Name'));
    $data['countlabel'] = xarVarPrepForDisplay(xarML('Count'));
    $data['pingremotelabel'] = xarVarPrepForDisplay(xarML('Ping IP or Host - Remote'));
    $data['tracelabel'] = xarVarPrepForDisplay(xarML('Traceroute IP or Host Name'));
    $data['traceremotelabel'] = xarVarPrepForDisplay(xarML('Traceroute IP or Host - Remote'));
    $data['serverlabel'] = xarVarPrepForDisplay(xarML('Query Port for Server'));
    $data['portnumlabel'] = xarVarPrepForDisplay(xarML('Port'));
    $data['lgrequestlabel'] = xarVarPrepForDisplay(xarML('Looking Glass Query'));
    $data['lgparamlabel'] = xarVarPrepForDisplay(xarML('Query Parameter'));
    $data['lgrouterlabel'] = xarVarPrepForDisplay(xarML('Router'));
    $data['clrlink'] = Array('url' => xarModURL('netquery', 'user', 'main'),
                             'title' => xarML('Clear results and return'),
                             'label' => xarML('Clear'));
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

    $query = "SELECT exec_id,
                     exec_type,
                     exec_local,
                     exec_winsys,
                     exec_remote,
                     exec_remote_t
                FROM $ExecTable
               WHERE exec_type = ?";
    $result =& $dbconn->Execute($query, array((string) $exec_type));
    if (!$result) return;
    list($exec_id, $exec_type, $exec_local, $exec_winsys, $exec_remote, $exec_remote_t) = $result->fields;
    if(!xarSecurityCheck('OverviewNetquery')) return;
    $exec = array('id'        => $exec_id,
                  'type'      => $exec_type,
                  'local'     => $exec_local,
                  'winsys'    => $exec_winsys,
                  'remote'    => $exec_remote,
                  'remote_t'  => $exec_remote_t);
    $result->Close();
    return $exec;
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
    $query = "SELECT whois_id,
                     whois_ext,
                     whois_server
            FROM $WhoisTable
            ORDER BY whois_ext";
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
    $query = "SELECT whois_id,
                     whois_ext,
                     whois_server
                FROM $WhoisTable
               WHERE whois_ext = ?";
    $result =& $dbconn->Execute($query, array((string) $whois_ext));
    if (!$result) return;
    list($whois_id, $whois_ext, $whois_server) = $result->fields;
    $link = array('whois_id'     => $whois_id,
                  'whois_ext'    => $whois_ext,
                  'whois_server' => $whois_server);
    $result->Close();
    return $link;
}
function Netquery_userapi_getlgrequests($args)
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
    $query = "SELECT request_id,
                     request,
                     command,
                     handler,
                     argc
              FROM $LGRequestTable
              ORDER BY request_id";
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
function Netquery_userapi_getlgrequest($args)
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
    $query = "SELECT request_id,
                     request,
                     command,
                     handler,
                     argc
                FROM $LGRequestTable
               WHERE request = ?";
    $result =& $dbconn->Execute($query, array((int) $request));
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
function Netquery_userapi_getlgrouters($args)
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
    $query = "SELECT router_id,
                     router,
                     address,
                     username,
                     password,
                     zebra,
                     zebra_port,
                     zebra_password,
                     ripd,
                     ripd_port,
                     ripd_password,
                     ripngd,
                     ripngd_port,
                     ripngd_password,
                     ospfd,
                     ospfd_port,
                     ospfd_password,
                     bgpd,
                     bgpd_port,
                     bgpd_password,
                     ospf6d,
                     ospf6d_port,
                     ospf6d_password,
                     use_argc
              FROM $LGRouterTable
              WHERE router != 'default'
              ORDER BY router_id";
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
function Netquery_userapi_getlgrouter($args)
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
    $query = "SELECT router_id,
                     router,
                     address,
                     username,
                     password,
                     zebra,
                     zebra_port,
                     zebra_password,
                     ripd,
                     ripd_port,
                     ripd_password,
                     ripngd,
                     ripngd_port,
                     ripngd_password,
                     ospfd,
                     ospfd_port,
                     ospfd_password,
                     bgpd,
                     bgpd_port,
                     bgpd_password,
                     ospf6d,
                     ospf6d_port,
                     ospf6d_password,
                     use_argc
                FROM $LGRouterTable
               WHERE router = ?";
    $result =& $dbconn->Execute($query, array((int) $router));
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
