<?php
/**
 * File: $Id:
 */

function netquery_userapi_mainapi()
{ 
    $settings = array(); 
    $settings['maintitle']  = xarVarPrepForDisplay(xarML('Netquery'));
    $settings['subtitle']   = xarVarPrepForDisplay(xarML('Click "Go" for any Netquery option'));
    $settings['domainlabel'] = xarVarPrepForDisplay(xarML('Whois Domain Name (No www.)'));
    $settings['extlabel'] = xarVarPrepForDisplay(xarML('Domain'));
    $settings['whoisiplabel'] = xarVarPrepForDisplay(xarML('Whois IP Address'));
    $settings['lookuplabel'] = xarVarPrepForDisplay(xarML('Lookup IP Address or Host Name'));
    $settings['diglabel'] = xarVarPrepForDisplay(xarML('Lookup (Dig) IP or Host Name'));
    $settings['pinglabel'] = xarVarPrepForDisplay(xarML('Ping IP Address or Host Name'));
    $settings['countlabel'] = xarVarPrepForDisplay(xarML('Count'));
    $settings['pingremotelabel'] = xarVarPrepForDisplay(xarML('Ping IP or Host - Remote'));
    $settings['tracelabel'] = xarVarPrepForDisplay(xarML('Traceroute IP or Host Name'));
    $settings['traceremotelabel'] = xarVarPrepForDisplay(xarML('Traceroute IP or Host - Remote'));
    $settings['serverlabel'] = xarVarPrepForDisplay(xarML('Query Port for Server'));
    $settings['portnumlabel'] = xarVarPrepForDisplay(xarML('Port'));
    $settings['windows_system'] = xarModGetVar('netquery', 'windows_system');
    $settings['localexec_enabled'] = xarModGetVar('netquery', 'localexec_enabled');
    $settings['whois_enabled'] = xarModGetVar('netquery', 'whois_enabled');
    $settings['whoisip_enabled'] = xarModGetVar('netquery', 'whoisip_enabled');
    $settings['dns_lookup_enabled'] = xarModGetVar('netquery', 'dns_lookup_enabled');
    $settings['dns_dig_enabled'] = xarModGetVar('netquery', 'dns_dig_enabled');
    $settings['ping_enabled'] = xarModGetVar('netquery', 'ping_enabled');
    $settings['ping_remote_enabled'] = xarModGetVar('netquery', 'ping_remote_enabled');
    $settings['trace_enabled'] = xarModGetVar('netquery', 'trace_enabled');
    $settings['trace_remote_enabled'] = xarModGetVar('netquery', 'trace_remote_enabled');
    $settings['port_check_enabled'] = xarModGetVar('netquery', 'port_check_enabled');
    $settings['capture_log_enabled'] = xarModGetVar('netquery', 'capture_log_enabled');
    $settings['results'] = '';
    return $settings;
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
