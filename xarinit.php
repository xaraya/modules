<?php
include_once ("modules/netquery/xarinitdata.php");
function netquery_init()
{
    $varLogsDir = xarCoreGetVarDirPath() . '/logs';
    if (!is_writable($varLogsDir)) {
        $msg = xarML('Netquery module installation has failed. Please make #(1) writable by the web server process.', $varLogsDir);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FUNCTION_FAILED', new SystemException($msg));
        return false;
    }
    if (DIRECTORY_SEPARATOR == '\\') {
      $digexec = 'nslookup.exe';
      $traceexec = 'tracert.exe';
    } else {
      $digexec = 'dig';
      $traceexec = 'traceroute';
    }
    xarModSetVar('netquery', 'querytype_default', 'whois');
    xarModSetVar('netquery', 'exec_timer_enabled', 1);
    xarModSetVar('netquery', 'capture_log_enabled', 0);
    xarModSetVar('netquery', 'capture_log_allowuser', 0);
    xarModSetVar('netquery', 'capture_log_filepath', 'var/logs/nq_log.txt');
    xarModSetVar('netquery', 'capture_log_dtformat', 'Y-m-d H:i:s');
    xarModSetVar('netquery', 'clientinfo_enabled', 1);
    xarModSetVar('netquery', 'topcountries_limit', 10);
    xarModSetVar('netquery', 'whois_enabled', 1);
    xarModSetVar('netquery', 'whois_max_limit', 3);
    xarModSetVar('netquery', 'whois_default', '.com');
    xarModSetVar('netquery', 'whoisip_enabled', 1);
    xarModSetVar('netquery', 'dns_lookup_enabled', 1);
    xarModSetVar('netquery', 'dns_dig_enabled', 1);
    xarModSetVar('netquery', 'digexec_local', $digexec);
    xarModSetVar('netquery', 'email_check_enabled', 1);
    xarModSetVar('netquery', 'query_email_server', 0);
    xarModSetVar('netquery', 'port_check_enabled', 1);
    xarModSetVar('netquery', 'user_submissions', 1);
    xarModSetVar('netquery', 'http_req_enabled', 1);
    xarModSetVar('netquery', 'ping_enabled', 1);
    xarModSetVar('netquery', 'pingexec_local', 'ping.exe');
    xarModSetVar('netquery', 'ping_remote_enabled', 1);
    xarModSetVar('netquery', 'pingexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
    xarModSetVar('netquery', 'pingexec_remote_t', 'target');
    xarModSetVar('netquery', 'trace_enabled', 1);
    xarModSetVar('netquery', 'traceexec_local', $traceexec);
    xarModSetVar('netquery', 'trace_remote_enabled', 1);
    xarModSetVar('netquery', 'traceexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
    xarModSetVar('netquery', 'traceexec_remote_t', 'target');
    xarModSetVar('netquery', 'looking_glass_enabled', 1);
    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type', array('modName' => 'netquery', 'blockType' => 'netquick'))) return;
    xarRegisterMask('ReadNetqueryBlock', 'All', 'netquery', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('OverviewNetquery','All','netquery','All','All','ACCESS_READ');
    xarRegisterMask('ReadNetquery','All','netquery','All','All','ACCESS_READ');
    xarRegisterMask('EditNetquery','All','netquery','All','All','ACCESS_EDIT');
    xarRegisterMask('AddNetquery','All','netquery','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteNetquery','All','netquery','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminNetquery','All','netquery','All','All','ACCESS_ADMIN');
    create_whoistable();
    create_lgroutertable();
    create_flagstable();
    create_portstable();
    create_geocctable();
    create_geoiptable();
    return true;
}
function netquery_upgrade($oldversion)
{
    $varLogsDir = xarCoreGetVarDirPath() . '/logs';
    if (!is_writable($varLogsDir)) {
        $msg = xarML('Netquery module upgrade has failed. Please make #(1) writable by the web server process.', $varLogsDir);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FUNCTION_FAILED', new SystemException($msg));
        return false;
    }
    if (DIRECTORY_SEPARATOR == '\\') {
      $digexec = 'nslookup.exe';
      $traceexec = 'tracert.exe';
    } else {
      $digexec = 'dig';
      $traceexec = 'traceroute';
    }
    switch ($oldversion) {
        case '1.0.0':
            xarModSetVar('netquery', 'querytype_default', 'whois');
            xarModSetVar('netquery', 'exec_timer_enabled', 1);
            xarModSetVar('netquery', 'capture_log_allowuser', 0);
            xarModSetVar('netquery', 'capture_log_filepath', 'var/logs/nq_log.txt');
            xarModSetVar('netquery', 'capture_log_dtformat', 'Y-m-d H:i:s');
            xarModSetVar('netquery', 'clientinfo_enabled', 1);
            xarModSetVar('netquery', 'topcountries_limit', 10);
            xarModSetVar('netquery', 'whois_max_limit', 3);
            xarModSetVar('netquery', 'whois_default', '.com');
            xarModSetVar('netquery', 'digexec_local', $digexec);
            xarModSetVar('netquery', 'email_check_enabled', 1);
            xarModSetVar('netquery', 'query_email_server', 0);
            xarModSetVar('netquery', 'user_submissions', 1);
            xarModSetVar('netquery', 'http_req_enabled', 1);
            xarModSetVar('netquery', 'pingexec_local', 'ping.exe');
            xarModSetVar('netquery', 'pingexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'pingexec_remote_t', 'target');
            xarModSetVar('netquery', 'traceexec_local', $traceexec);
            xarModSetVar('netquery', 'traceexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'tracexec_remote_t', 'target');
            xarModSetVar('netquery', 'looking_glass_enabled', 1);
            if (!xarModAPIFunc('blocks', 'admin', 'register_block_type', array('modName' => 'netquery', 'blockType' => 'netquick'))) return;
            xarRegisterMask('ReadNetqueryBlock', 'All', 'netquery', 'Block', 'All', 'ACCESS_OVERVIEW');
            create_lgroutertable();
            create_flagstable();
            create_portstable();
            create_geocctable();
            create_geoiptable();
            drop_exectable();
            break;
        case '1.1.0':
            xarModSetVar('netquery', 'querytype_default', 'whois');
            xarModSetVar('netquery', 'exec_timer_enabled', 1);
            xarModSetVar('netquery', 'capture_log_allowuser', 0);
            xarModSetVar('netquery', 'capture_log_filepath', 'var/logs/nq_log.txt');
            xarModSetVar('netquery', 'capture_log_dtformat', 'Y-m-d H:i:s');
            xarModSetVar('netquery', 'clientinfo_enabled', 1);
            xarModSetVar('netquery', 'topcountries_limit', 10);
            xarModSetVar('netquery', 'whois_max_limit', 3);
            xarModSetVar('netquery', 'whois_default', '.com');
            xarModSetVar('netquery', 'digexec_local', $digexec);
            xarModSetVar('netquery', 'email_check_enabled', 1);
            xarModSetVar('netquery', 'query_email_server', 0);
            xarModSetVar('netquery', 'user_submissions', 1);
            xarModSetVar('netquery', 'http_req_enabled', 1);
            xarModSetVar('netquery', 'pingexec_local', 'ping.exe');
            xarModSetVar('netquery', 'pingexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'pingexec_remote_t', 'target');
            xarModSetVar('netquery', 'traceexec_local', $traceexec);
            xarModSetVar('netquery', 'traceexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'tracexec_remote_t', 'target');
            if (!xarModAPIFunc('blocks', 'admin', 'register_block_type', array('modName' => 'netquery', 'blockType' => 'netquick'))) return;
            xarRegisterMask('ReadNetqueryBlock', 'All', 'netquery', 'Block', 'All', 'ACCESS_OVERVIEW');
            create_flagstable();
            create_portstable();
            create_geocctable();
            create_geoiptable();
            drop_lgrequesttable();
            drop_exectable();
            break;
        case '1.2.0':
            xarModSetVar('netquery', 'querytype_default', 'whois');
            xarModSetVar('netquery', 'exec_timer_enabled', 1);
            xarModSetVar('netquery', 'capture_log_allowuser', 0);
            xarModSetVar('netquery', 'capture_log_filepath', 'var/logs/nq_log.txt');
            xarModSetVar('netquery', 'capture_log_dtformat', 'Y-m-d H:i:s');
            xarModSetVar('netquery', 'clientinfo_enabled', 1);
            xarModSetVar('netquery', 'topcountries_limit', 10);
            xarModSetVar('netquery', 'whois_max_limit', 3);
            xarModSetVar('netquery', 'whois_default', '.com');
            xarModSetVar('netquery', 'digexec_local', $digexec);
            xarModSetVar('netquery', 'email_check_enabled', 1);
            xarModSetVar('netquery', 'query_email_server', 0);
            xarModSetVar('netquery', 'user_submissions', 1);
            xarModSetVar('netquery', 'pingexec_local', 'ping.exe');
            xarModSetVar('netquery', 'pingexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'pingexec_remote_t', 'target');
            xarModSetVar('netquery', 'traceexec_local', $traceexec);
            xarModSetVar('netquery', 'traceexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'tracexec_remote_t', 'target');
            if (!xarModAPIFunc('blocks', 'admin', 'register_block_type', array('modName' => 'netquery', 'blockType' => 'netquick'))) return;
            xarRegisterMask('ReadNetqueryBlock', 'All', 'netquery', 'Block', 'All', 'ACCESS_OVERVIEW');
            create_flagstable();
            create_portstable();
            create_geocctable();
            create_geoiptable();
            drop_lgrequesttable();
            drop_exectable();
            break;
        case '1.3.0':
        case '1.3.1':
            xarModSetVar('netquery', 'querytype_default', 'whois');
            xarModSetVar('netquery', 'exec_timer_enabled', 1);
            xarModSetVar('netquery', 'capture_log_allowuser', 0);
            xarModSetVar('netquery', 'capture_log_filepath', 'var/logs/nq_log.txt');
            xarModSetVar('netquery', 'capture_log_dtformat', 'Y-m-d H:i:s');
            xarModSetVar('netquery', 'clientinfo_enabled', 1);
            xarModSetVar('netquery', 'topcountries_limit', 10);
            xarModSetVar('netquery', 'whois_default', '.com');
            xarModSetVar('netquery', 'digexec_local', $digexec);
            xarModSetVar('netquery', 'email_check_enabled', 1);
            xarModSetVar('netquery', 'query_email_server', 0);
            xarModSetVar('netquery', 'user_submissions', 1);
            xarModSetVar('netquery', 'pingexec_local', 'ping.exe');
            xarModSetVar('netquery', 'pingexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'pingexec_remote_t', 'target');
            xarModSetVar('netquery', 'traceexec_local', $traceexec);
            xarModSetVar('netquery', 'traceexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'tracexec_remote_t', 'target');
            if (!xarModAPIFunc('blocks', 'admin', 'register_block_type', array('modName' => 'netquery', 'blockType' => 'netquick'))) return;
            xarRegisterMask('ReadNetqueryBlock', 'All', 'netquery', 'Block', 'All', 'ACCESS_OVERVIEW');
            create_flagstable();
            create_geocctable();
            create_geoiptable();
            drop_lgrequesttable();
            drop_exectable();
            break;
        case '2.0.0':
        case '2.1.0':
        case '2.2.0':
            xarModSetVar('netquery', 'querytype_default', 'whois');
            xarModSetVar('netquery', 'exec_timer_enabled', 1);
            xarModSetVar('netquery', 'capture_log_allowuser', 0);
            xarModSetVar('netquery', 'capture_log_filepath', 'var/logs/nq_log.txt');
            xarModSetVar('netquery', 'capture_log_dtformat', 'Y-m-d H:i:s');
            xarModSetVar('netquery', 'clientinfo_enabled', 1);
            xarModSetVar('netquery', 'topcountries_limit', 10);
            xarModSetVar('netquery', 'whois_default', '.com');
            xarModSetVar('netquery', 'digexec_local', $digexec);
            xarModSetVar('netquery', 'email_check_enabled', 1);
            xarModSetVar('netquery', 'query_email_server', 0);
            xarModSetVar('netquery', 'pingexec_local', 'ping.exe');
            xarModSetVar('netquery', 'pingexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'pingexec_remote_t', 'target');
            xarModSetVar('netquery', 'traceexec_local', $traceexec);
            xarModSetVar('netquery', 'traceexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'tracexec_remote_t', 'target');
            if (!xarModAPIFunc('blocks', 'admin', 'register_block_type', array('modName' => 'netquery', 'blockType' => 'netquick'))) return;
            xarRegisterMask('ReadNetqueryBlock', 'All', 'netquery', 'Block', 'All', 'ACCESS_OVERVIEW');
            create_geocctable();
            create_geoiptable();
            drop_lgrequesttable();
            drop_exectable();
            break;
        case '2.3.0':
        case '2.3.5':
            xarModSetVar('netquery', 'querytype_default', 'whois');
            xarModSetVar('netquery', 'exec_timer_enabled', 1);
            xarModSetVar('netquery', 'capture_log_allowuser', 0);
            xarModSetVar('netquery', 'capture_log_filepath', 'var/logs/nq_log.txt');
            xarModSetVar('netquery', 'capture_log_dtformat', 'Y-m-d H:i:s');
            xarModSetVar('netquery', 'clientinfo_enabled', 1);
            xarModSetVar('netquery', 'topcountries_limit', 10);
            xarModSetVar('netquery', 'digexec_local', $digexec);
            xarModSetVar('netquery', 'whois_default', '.com');
            xarModSetVar('netquery', 'pingexec_local', 'ping.exe');
            xarModSetVar('netquery', 'pingexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'pingexec_remote_t', 'target');
            xarModSetVar('netquery', 'traceexec_local', $traceexec);
            xarModSetVar('netquery', 'traceexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'tracexec_remote_t', 'target');
            create_geocctable();
            create_geoiptable();
            drop_lgrequesttable();
            drop_exectable();
            break;
        case '2.4.0':
            xarModSetVar('netquery', 'exec_timer_enabled', 1);
            xarModSetVar('netquery', 'capture_log_allowuser', 0);
            xarModSetVar('netquery', 'topcountries_limit', 10);
            xarModSetVar('netquery', 'digexec_local', $digexec);
            create_geocctable();
            create_geoiptable();
            break;
        case '3.0.0':
        default:
            xarModSetVar('netquery', 'exec_timer_enabled', 1);
            xarModSetVar('netquery', 'capture_log_allowuser', 0);
            xarModSetVar('netquery', 'topcountries_limit', 10);
            xarModSetVar('netquery', 'digexec_local', $digexec);
            break;
    }
    return true;
}
function netquery_delete()
{
    xarModDelVar('netquery', 'looking_glass_enabled');
    xarModDelVar('netquery', 'traceexec_remote_t');
    xarModDelVar('netquery', 'traceexec_remote');
    xarModDelVar('netquery', 'trace_remote_enabled');
    xarModDelVar('netquery', 'traceexec_local');
    xarModDelVar('netquery', 'trace_enabled');
    xarModDelVar('netquery', 'pingexec_remote_t');
    xarModDelVar('netquery', 'pingexec_remote');
    xarModDelVar('netquery', 'ping_remote_enabled');
    xarModDelVar('netquery', 'pingexec_local');
    xarModDelVar('netquery', 'ping_enabled');
    xarModDelVar('netquery', 'http_req_enabled');
    xarModDelVar('netquery', 'user_submissions');
    xarModDelVar('netquery', 'port_check_enabled');
    xarModDelVar('netquery', 'query_email_server');
    xarModDelVar('netquery', 'email_check_enabled');
    xarModDelVar('netquery', 'digexec_local');
    xarModDelVar('netquery', 'dns_dig_enabled');
    xarModDelVar('netquery', 'dns_lookup_enabled');
    xarModDelVar('netquery', 'whoisip_enabled');
    xarModDelVar('netquery', 'whois_default');
    xarModDelVar('netquery', 'whois_max_limit');
    xarModDelVar('netquery', 'whois_enabled');
    xarModDelVar('netquery', 'topcountries_limit');
    xarModDelVar('netquery', 'clientinfo_enabled');
    xarModDelVar('netquery', 'capture_log_dtformat');
    xarModDelVar('netquery', 'capture_log_filepath');
    xarModDelVar('netquery', 'capture_log_allowuser');
    xarModDelVar('netquery', 'capture_log_enabled');
    xarModDelVar('netquery', 'exec_timer_enabled');
    xarModDelVar('netquery', 'querytype_default');
    if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type', array('modName' => 'netquery', 'blockType' => 'netquick'))) return;
    xarRemoveMasks('netquery');
    xarRemoveInstances('netquery');
    drop_geoiptable();
    drop_geocctable();
    drop_portstable();
    drop_flagstable();
    drop_lgroutertable();
    drop_whoistable();
    return true;
}
?>
