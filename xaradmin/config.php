<?php
function netquery_admin_config()
{
    if (!xarSecurityCheck('EditRole')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED)) return;
    switch (strtolower($phase)) {
        case 'modify':
        default:
            $data = xarModAPIFunc('netquery', 'admin', 'configapi');
            break;
        case 'update':
            if ((!isset($Submit)) || ($Submit != 'Submit')) {
                xarResponseRedirect(xarModURL('netquery', 'admin', 'main'));
            }
            if (!xarVarFetch('capture_log_enabled', 'checkbox', $capture_log_enabled, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('logfile_local', 'str:1:100', $logfile_local, 'var/logs/netquery.log', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('logfile_remote', 'str:1:100', $logfile_remote, 'Y-m-d H:i:s', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('logfile_remote_t', 'str:1:10', $logfile_remote_t, 'whois', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('whois_enabled', 'checkbox', $whois_enabled, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('whoisip_enabled', 'checkbox', $whoisip_enabled, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('dns_lookup_enabled', 'checkbox', $dns_lookup_enabled, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('dns_dig_enabled', 'checkbox', $dns_dig_enabled, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('port_check_enabled', 'checkbox', $port_check_enabled, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('http_req_enabled', 'checkbox', $http_req_enabled, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('ping_enabled', 'checkbox', $ping_enabled, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pingexec_local', 'str:1:100', $pingexec_local, 'ping.exe', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('ping_remote_enabled', 'checkbox', $ping_remote_enabled, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pingexec_remote', 'str:1:100', $pingexec_remote, 'http://noc.thunderworx.net/cgi-bin/public/ping.pl', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('pingexec_remote_t', 'str:1:10', $pingexec_remote_t, 'target', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('trace_enabled', 'checkbox', $trace_enabled, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('traceexec_local', 'str:1:100', $traceexec_local, 'tracert.exe', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('trace_remote_enabled', 'checkbox', $trace_remote_enabled, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('traceexec_remote', 'str:1:100', $traceexec_remote, 'http://noc.thunderworx.net/cgi-bin/public/traceroute.pl', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('traceexec_remote_t', 'str:1:10', $traceexec_remote_t, 'target', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('looking_glass_enabled', 'checkbox', $looking_glass_enabled, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('whois_max_limit', 'int:1:10', $whois_max_limit, '3', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('user_submissions', 'int:1:10', $user_submissions, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_username', 'str:1:20', $lgdefault_username, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_password', 'str:1:20', $lgdefault_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_zebra', 'checkbox', $lgdefault_zebra, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_zebra_port', 'int:1:100000', $lgdefault_zebra_port, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_zebra_password', 'str:1:20', $lgdefault_zebra_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_ripd', 'checkbox', $lgdefault_ripd, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_ripd_port', 'int:1:100000', $lgdefault_ripd_port, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_ripd_password', 'str:1:20', $lgdefault_ripd_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_ripngd', 'checkbox', $lgdefault_ripngd, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_ripngd_port', 'int:1:100000', $lgdefault_ripngd_port, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_ripngd_password', 'str:1:20', $lgdefault_ripngd_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_ospfd', 'checkbox', $lgdefault_ospfd, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_ospfd_port', 'int:1:100000', $lgdefault_ospfd_port, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_ospfd_password', 'str:1:20', $lgdefault_ospfd_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_bgpd', 'checkbox', $lgdefault_bgpd, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_bgpd_port', 'int:1:100000', $lgdefault_bgpd_port, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_bgpd_password', 'str:1:20', $lgdefault_bgpd_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_ospf6d', 'checkbox', $lgdefault_ospf6d, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_ospf6d_port', 'int:1:100000', $lgdefault_ospf6d_port, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_ospf6d_password', 'str:1:20', $lgdefault_ospf6d_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lgdefault_use_argc', 'checkbox', $lgdefault_use_argc, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarSecConfirmAuthKey()) return;
            xarModSetVar('netquery', 'capture_log_enabled', $capture_log_enabled);
            xarModSetVar('netquery', 'whois_enabled', $whois_enabled);
            xarModSetVar('netquery', 'whoisip_enabled', $whoisip_enabled);
            xarModSetVar('netquery', 'dns_lookup_enabled', $dns_lookup_enabled);
            xarModSetVar('netquery', 'dns_dig_enabled', $dns_dig_enabled);
            xarModSetVar('netquery', 'port_check_enabled', $port_check_enabled);
            xarModSetVar('netquery', 'http_req_enabled', $http_req_enabled);
            xarModSetVar('netquery', 'ping_enabled', $ping_enabled);
            xarModSetVar('netquery', 'ping_remote_enabled', $ping_remote_enabled);
            xarModSetVar('netquery', 'trace_enabled', $trace_enabled);
            xarModSetVar('netquery', 'trace_remote_enabled', $trace_remote_enabled);
            xarModSetVar('netquery', 'looking_glass_enabled', $looking_glass_enabled);
            xarModSetVar('netquery', 'whois_max_limit', $whois_max_limit);
            xarModSetVar('netquery', 'user_submissions', $user_submissions);
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $ExecTable = $xartable['netquery_exec'];
            $LGRouterTable = $xartable['netquery_lgrouter'];
            $query = "UPDATE $ExecTable
                SET exec_local    = ?,
                    exec_remote   = ?,
                    exec_remote_t = ?
                WHERE exec_type = 'log'";
            $bindvars = array($logfile_local, $logfile_remote, $logfile_remote_t);
            $result =& $dbconn->Execute($query,$bindvars);
            $query = "UPDATE $ExecTable
                SET exec_local    = ?,
                    exec_remote   = ?,
                    exec_remote_t = ?
                WHERE exec_type = 'ping'";
            $bindvars = array($pingexec_local, $pingexec_remote, $pingexec_remote_t);
            $result =& $dbconn->Execute($query,$bindvars);
            $query = "UPDATE $ExecTable
                SET exec_local    = ?,
                    exec_remote   = ?,
                    exec_remote_t = ?
                WHERE exec_type = 'trace'";
            $bindvars = array($traceexec_local, $traceexec_remote, $traceexec_remote_t);
            $result =& $dbconn->Execute($query,$bindvars);
            $query = "UPDATE $LGRouterTable
                SET username        = ?,
                    password        = ?,
                    zebra           = ?,
                    zebra_port      = ?,
                    zebra_password  = ?,
                    ripd            = ?,
                    ripd_port       = ?,
                    ripd_password   = ?,
                    ripngd          = ?,
                    ripngd_port     = ?,
                    ripngd_password = ?,
                    ospfd           = ?,
                    ospfd_port      = ?,
                    ospfd_password  = ?,
                    bgpd            = ?,
                    bgpd_port       = ?,
                    bgpd_password   = ?,
                    ospf6d          = ?,
                    ospf6d_port     = ?,
                    ospf6d_password = ?,
                    use_argc        = ?
                WHERE router = 'default'";
            $bindvars = array($lgdefault_username, $lgdefault_password,
                             (int)$lgdefault_zebra, (int)$lgdefault_zebra_port, $lgdefault_zebra_password,
                             (int)$lgdefault_ripd, (int)$lgdefault_ripd_port, $lgdefault_ripd_password,
                             (int)$lgdefault_ripngd, (int)$lgdefault_ripngd_port, $lgdefault_ripngd_password,
                             (int)$lgdefault_ospfd, (int)$lgdefault_ospfd_port, $lgdefault_ospfd_password,
                             (int)$lgdefault_bgpd, (int)$lgdefault_bgpd_port, $lgdefault_bgpd_password,
                             (int)$lgdefault_ospf6d, (int)$lgdefault_ospf6d_port, $lgdefault_ospf6d_password,
                             (int)$lgdefault_use_argc);
            $result =& $dbconn->Execute($query,$bindvars);
            $result->Close();
            xarResponseRedirect(xarModURL('netquery', 'admin', 'config'));
            return true;
            break;
    }
    return $data;
}
?>