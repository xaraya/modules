<?php
/**
 * File: $Id:
 */

function netquery_userapi_mainapi()
{ 
    $data = array(); 

    xarVarFetch('querytype', 'str:1:', $data['querytype'], 'none', XARVAR_NOT_REQUIRED);
    xarVarFetch('domain', 'str:1:', $data['domain'], 'example', XARVAR_NOT_REQUIRED);
    xarVarFetch('ext', 'str:1:', $data['ext'], '.com', XARVAR_NOT_REQUIRED);
    xarVarFetch('addr', 'str:1:', $data['addr'], $_SERVER['REMOTE_ADDR'], XARVAR_NOT_REQUIRED);
    xarVarFetch('host', 'str:1:', $data['host'], $_SERVER['REMOTE_HOST'], XARVAR_NOT_REQUIRED);
    xarVarFetch('server', 'str:1:', $data['server'], $_SERVER['SERVER_NAME'], XARVAR_NOT_REQUIRED);
    xarVarFetch('maxp', 'int:1:', $data['maxp'], '4', XARVAR_NOT_REQUIRED);
    xarVarFetch('portnum', 'int:1:', $data['portnum'], '80', XARVAR_NOT_REQUIRED);

    $data['windows_system'] = xarModGetVar('netquery', 'windows_system');
    $data['localexec_enabled'] = xarModGetVar('netquery', 'localexec_enabled');
    $data['whois_enabled'] = xarModGetVar('netquery', 'whois_enabled');
    $data['whoisip_enabled'] = xarModGetVar('netquery', 'whoisip_enabled');
    $data['dns_lookup_enabled'] = xarModGetVar('netquery', 'dns_lookup_enabled');
    $data['dns_dig_enabled'] = xarModGetVar('netquery', 'dns_dig_enabled');
    $data['ping_enabled'] = xarModGetVar('netquery', 'ping_enabled');
    $data['ping_remote_enabled'] = xarModGetVar('netquery', 'ping_remote_enabled');
    $data['trace_enabled'] = xarModGetVar('netquery', 'trace_enabled');
    $data['trace_remote_enabled'] = xarModGetVar('netquery', 'trace_remote_enabled');
    $data['port_check_enabled'] = xarModGetVar('netquery', 'port_check_enabled');
    $data['capture_log_enabled'] = xarModGetVar('netquery', 'capture_log_enabled');

    $data['pingexec'] = xarModAPIFunc('netquery', 'user', 'getexec', array('exec_type' => 'ping'));
    $data['traceexec'] = xarModAPIFunc('netquery', 'user', 'getexec', array('exec_type' => 'trace'));
    $data['logfile'] = xarModAPIFunc('netquery', 'user', 'getexec', array('exec_type' => 'log'));
    $data['all_tlds'] = xarModAPIFunc('netquery', 'user', 'gettlds'); 
    $data['results'] = '';

    $data['maintitle']  = xarVarPrepForDisplay(xarML('Netquery'));
    $data['subtitle']   = xarVarPrepForDisplay(xarML('Click "Go" for any Netquery option'));
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

    return $data;
} 

function netquery_userapi_getexec($args)
{
    extract($args);
    if (!isset($exec_type)) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
              WHERE exec_type = '" . xarVarPrepForStore($exec_type) . "'";
    $result =& $dbconn->Execute($query);
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

function netquery_userapi_gettlds()
{
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];
    $query = "SELECT whois_id,
                     whois_ext,
                     whois_server
              FROM $WhoisTable
              ORDER BY whois_ext";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    $i=0;
    while (!$result->EOF) {
            list($whois_id, $whois_ext, $whois_server) = $result->fields;
            $tlds[$i] = $whois_ext;
            $i++;
            $result->MoveNext();
    }
    $result->Close();
    return $tlds;
}

function netquery_userapi_getlink($args)
{
    extract($args);
    if (!isset($whois_ext)) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];

    $query = "SELECT whois_id,
                     whois_ext,
                     whois_server
            FROM $WhoisTable
            WHERE whois_ext = '" . xarVarPrepForStore($whois_ext) . "'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    list($whois_id, $whois_ext, $whois_server) = $result->fields;
    if(!xarSecurityCheck('OverviewNetquery')) return;
    $link = array('whois_id'     => $whois_id,
                  'whois_ext'    => $whois_ext,
                  'whois_server' => $whois_server);

    $result->Close();
    return $link;
}

?>
