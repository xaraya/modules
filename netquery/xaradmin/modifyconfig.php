<?php
/**
 * File: $Id:
 */

function netquery_admin_modifyconfig()
{ 
    if (!xarSecurityCheck('EditRole')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;
    switch (strtolower($phase)) {
        case 'modify':
        default:
            $data['authid'] = xarSecGenAuthKey(); 
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
            $data['submitlabel'] = xarML('Submit');
            $data['pingexec'] = xarModAPIFunc('netquery', 'admin', 'getexec', array('exec_type' => 'ping'));
            $data['traceexec'] = xarModAPIFunc('netquery', 'admin', 'getexec', array('exec_type' => 'trace'));
            $data['helplink'] = Array('url'   => xarML('modules/netquery/xardocs/manual.html'),
                                      'title' => xarML('Netquery online administration manual'),
                                      'label' => xarML('Online Manual'));
            break;
        case 'update':
            if (!xarVarFetch('whois_enabled', 'checkbox', $whois_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('whoisip_enabled', 'checkbox', $whoisip_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('dns_lookup_enabled', 'checkbox', $dns_lookup_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('dns_dig_enabled', 'checkbox', $dns_dig_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('ping_enabled', 'checkbox', $ping_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pingexec_local', 'str:1:100', $pingexec_local, 'ping.exe', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pingexec_winsys', 'checkbox', $pingexec_winsys, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('ping_remote_enabled', 'checkbox', $ping_remote_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pingexec_remote', 'str:1:100', $pingexec_remote, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pingexec_remote_t', 'str:1:100', $pingexec_remote_t, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('trace_enabled', 'checkbox', $trace_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('traceexec_local', 'str:1:100', $traceexec_local, 'tracert.exe', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('traceexec_winsys', 'checkbox', $traceexec_winsys, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('trace_remote_enabled', 'checkbox', $trace_remote_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('traceexec_remote', 'str:1:100', $traceexec_remote, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('traceexec_remote_t', 'str:1:100', $traceexec_remote_t, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('port_check_enabled', 'checkbox', $port_check_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('capture_log_enabled', 'checkbox', $capture_log_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarSecConfirmAuthKey()) return;
            if ($whois_enabled) {
                xarModSetVar('netquery', 'whois_enabled', $whois_enabled);
            } else {
                xarModSetVar('netquery', 'whois_enabled', '0');
            }
            if ($whoisip_enabled) {
                xarModSetVar('netquery', 'whoisip_enabled', $whoisip_enabled);
            } else {
                xarModSetVar('netquery', 'whoisip_enabled', '0');
            }
            if ($dns_lookup_enabled) {
                xarModSetVar('netquery', 'dns_lookup_enabled', $dns_lookup_enabled);
            } else {
                xarModSetVar('netquery', 'dns_lookup_enabled', '0');
            }
            if ($dns_dig_enabled) {
                xarModSetVar('netquery', 'dns_dig_enabled', $dns_dig_enabled);
            } else {
                xarModSetVar('netquery', 'dns_dig_enabled', '0');
            }
            if ($ping_enabled) {
                xarModSetVar('netquery', 'ping_enabled', $ping_enabled);
            } else {
                xarModSetVar('netquery', 'ping_enabled', '0');
            }
            if ($ping_remote_enabled) {
                xarModSetVar('netquery', 'ping_remote_enabled', $ping_remote_enabled);
            } else {
                xarModSetVar('netquery', 'ping_remote_enabled', '0');
            }
            if ($trace_enabled) {
                xarModSetVar('netquery', 'trace_enabled', $trace_enabled);
            } else {
                xarModSetVar('netquery', 'trace_enabled', '0');
            }
            if ($trace_remote_enabled) {
                xarModSetVar('netquery', 'trace_remote_enabled', $trace_remote_enabled);
            } else {
                xarModSetVar('netquery', 'trace_remote_enabled', '0');
            }
            if ($port_check_enabled) {
                xarModSetVar('netquery', 'port_check_enabled', $port_check_enabled);
            } else {
                xarModSetVar('netquery', 'port_check_enabled', '0');
            }
            if ($capture_log_enabled) {
                xarModSetVar('netquery', 'capture_log_enabled', $capture_log_enabled);
            } else {
                xarModSetVar('netquery', 'capture_log_enabled', '0');
            }

            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $ExecTable = $xartable['netquery_exec'];

            $query = "UPDATE $ExecTable
                SET exec_local    = '" . xarVarPrepForStore($pingexec_local) . "',
                    exec_winsys   = '" . xarVarPrepForStore($pingexec_winsys) . "',
                    exec_remote   = '" . xarVarPrepForStore($pingexec_remote) . "',
                    exec_remote_t = '" . xarVarPrepForStore($pingexec_remote_t) . "'
                WHERE exec_type = 'ping'";
            $result =& $dbconn->Execute($query);

            $query = "UPDATE $ExecTable
                SET exec_local    = '" . xarVarPrepForStore($traceexec_local) . "',
                    exec_winsys   = '" . xarVarPrepForStore($traceexec_winsys) . "',
                    exec_remote   = '" . xarVarPrepForStore($traceexec_remote) . "',
                    exec_remote_t = '" . xarVarPrepForStore($traceexec_remote_t) . "'
                WHERE exec_type = 'trace'";
            $result =& $dbconn->Execute($query);

            $result->Close();
            xarResponseRedirect(xarModURL('netquery', 'admin', 'modifyconfig'));
            return true;
            break;
    } 
    return $data;
} 
?>
