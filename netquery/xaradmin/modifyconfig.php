<?php

function netquery_admin_modifyconfig()
{ 
    if (!xarSecurityCheck('EditRole')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;
    switch (strtolower($phase)) {
        case 'modify':
        default:
            $data['authid'] = xarSecGenAuthKey(); 
            $data['windows_system'] = xarModGetVar('netquery', 'windows_system');
            $data['localexec_enabled'] = xarModGetVar('netquery', 'localexec_enabled');
            $data['whois_enabled'] = xarModGetVar('netquery', 'whois_enabled');
            $data['whoisip_enabled'] = xarModGetVar('netquery', 'whoisip_enabled');
            $data['dns_lookup_enabled'] = xarModGetVar('netquery', 'dns_lookup_enabled');
            $data['dns_dig_enabled'] = xarModGetVar('netquery', 'dns_dig_enabled');
            $data['ping_enabled'] = xarModGetVar('netquery', 'ping_enabled');
            $data['trace_enabled'] = xarModGetVar('netquery', 'trace_enabled');
            $data['port_check_enabled'] = xarModGetVar('netquery', 'port_check_enabled');
            $data['capture_log_enabled'] = xarModGetVar('netquery', 'capture_log_enabled');
            $data['submitlabel'] = xarML('Submit');
            break;
        case 'update':
            if (!xarVarFetch('windows_system', 'checkbox', $windows_system, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('localexec_enabled', 'checkbox', $localexec_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('whois_enabled', 'checkbox', $whois_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('whoisip_enabled', 'checkbox', $whoisip_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('dns_lookup_enabled', 'checkbox', $dns_lookup_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('dns_dig_enabled', 'checkbox', $dns_dig_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('ping_enabled', 'checkbox', $ping_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('trace_enabled', 'checkbox', $trace_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('port_check_enabled', 'checkbox', $port_check_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('capture_log_enabled', 'checkbox', $capture_log_enabled, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarSecConfirmAuthKey()) return;
            if ($windows_system) {
                xarModSetVar('netquery', 'windows_system', $windows_system);
            } else {
                xarModSetVar('netquery', 'windows_system', '0');
            }
            if ($localexec_enabled) {
                xarModSetVar('netquery', 'localexec_enabled', $localexec_enabled);
            } else {
                xarModSetVar('netquery', 'localexec_enabled', '0');
            }
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
            if ($trace_enabled) {
                xarModSetVar('netquery', 'trace_enabled', $trace_enabled);
            } else {
                xarModSetVar('netquery', 'trace_enabled', '0');
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
            xarResponseRedirect(xarModURL('netquery', 'admin', 'modifyconfig'));
            return true;
            break;
    } 
    return $data;
} 
?>
