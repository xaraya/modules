<?php
include_once ("modules/netquery/xarinitdata.php");
function netquery_init()
{
    if (DIRECTORY_SEPARATOR == '\\')
    {
      $digexec = 'nslookup.exe';
      $pingexec = 'ping.exe';
      $traceexec = 'tracert.exe';
    }
    else
    {
      $digexec = 'dig';
      $pingexec = 'ping';
      $traceexec = 'traceroute';
    }
    xarModSetVar('netquery', 'querytype_default', 'whois');
    xarModSetVar('netquery', 'exec_timer_enabled', 1);
    xarModSetVar('netquery', 'stylesheet', 'blbuttons_xaraya');
    xarModSetVar('netquery', 'bb_enabled', 1);
    xarModSetVar('netquery', 'bb_retention', 7);
    xarModSetVar('netquery', 'bb_visible', 1);
    xarModSetVar('netquery', 'bb_display_stats', 'session');
    xarModSetVar('netquery', 'bb_strict', 0);
    xarModSetVar('netquery', 'bb_verbose', 0);
    xarModSetVar('netquery', 'bb_logging', 1);
    xarModSetVar('netquery', 'bb_httpbl_key', '');
    xarModSetVar('netquery', 'bb_httpbl_threat', 25);
    xarModSetVar('netquery', 'bb_httpbl_maxage', 10);
    xarModSetVar('netquery', 'clientinfo_enabled', 1);
    xarModSetVar('netquery', 'mapping_site', 1);
    xarModSetVar('netquery', 'topcountries_limit', 10);
    xarModSetVar('netquery', 'whois_enabled', 1);
    xarModSetVar('netquery', 'whois_max_limit', 3);
    xarModSetVar('netquery', 'whois_default', 'com');
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
    xarModSetVar('netquery', 'pingexec_local', $pingexec);
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
    create_spamblockertable();
    return true;
}
function netquery_upgrade($oldversion)
{
    if (DIRECTORY_SEPARATOR == '\\')
    {
      $digexec = 'nslookup.exe';
      $pingexec = 'ping.exe';
      $traceexec = 'tracert.exe';
    }
    else
    {
      $digexec = 'dig';
      $pingexec = 'ping';
      $traceexec = 'traceroute';
    }
    switch ($oldversion)
    {
        case '1.0.0':
            xarModSetVar('netquery', 'looking_glass_enabled', 1);
            return netquery_upgrade('1.1.0');
        case '1.1.0':
            xarModSetVar('netquery', 'http_req_enabled', 1);
            return netquery_upgrade('1.2.0');
        case '1.2.0':
            xarModSetVar('netquery', 'whois_max_limit', 3);
            create_portstable();
            return netquery_upgrade('1.3.1');
        case '1.3.0':
        case '1.3.1':
            xarModSetVar('netquery', 'user_submissions', 1);
            create_flagstable();
            return netquery_upgrade('2.2.0');
        case '2.0.0':
        case '2.1.0':
        case '2.2.0':
            xarModSetVar('netquery', 'email_check_enabled', 1);
            xarModSetVar('netquery', 'query_email_server', 0);
            if (!xarModAPIFunc('blocks', 'admin', 'register_block_type', array('modName' => 'netquery', 'blockType' => 'netquick'))) return;
            xarRegisterMask('ReadNetqueryBlock', 'All', 'netquery', 'Block', 'All', 'ACCESS_OVERVIEW');
            return netquery_upgrade('2.3.5');
        case '2.3.0':
        case '2.3.5':
            xarModSetVar('netquery', 'querytype_default', 'whois');
            xarModSetVar('netquery', 'capture_log_enabled', 0);
            xarModSetVar('netquery', 'capture_log_filepath', 'var/logs/nq_log.txt');
            xarModSetVar('netquery', 'capture_log_dtformat', 'Y-m-d H:i:s');
            xarModSetVar('netquery', 'clientinfo_enabled', 1);
            xarModSetVar('netquery', 'pingexec_local', $pingexec);
            xarModSetVar('netquery', 'pingexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'pingexec_remote_t', 'target');
            xarModSetVar('netquery', 'traceexec_local', $traceexec);
            xarModSetVar('netquery', 'traceexec_remote', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl');
            xarModSetVar('netquery', 'tracexec_remote_t', 'target');
            return netquery_upgrade('2.4.0');
        case '2.4.0':
            create_geocctable();
            create_geoiptable();
            return netquery_upgrade('3.1.0');
        case '3.0.0':
        case '3.1.0':
            xarModSetVar('netquery', 'exec_timer_enabled', 1);
            xarModSetVar('netquery', 'capture_log_allowuser', 0);
            xarModSetVar('netquery', 'topcountries_limit', 10);
            xarModSetVar('netquery', 'digexec_local', $digexec);
            return netquery_upgrade('3.1.2');
        case '3.1.1':
        case '3.1.2':
            xarModSetVar('netquery', 'mapping_site', 1);
            return netquery_upgrade('3.2.0');
        case '3.2.0':
            xarModSetVar('netquery', 'stylesheet', 'blbuttons_xaraya');
            return netquery_upgrade('3.3.0');
        case '3.3.0':
            xarModSetVar('netquery', 'whois_default', 'com');
            drop_whoistable();
            create_whoistable();
            return netquery_upgrade('3.3.2');
        case '3.3.1':
        case '3.3.2':
            revise_geoiptable();
            return netquery_upgrade('3.3.3');
        case '3.3.3':
            xarModSetVar('netquery', 'bb_visible', 1);
            xarModSetVar('netquery', 'bb_display_stats', 'session');
            xarModSetVar('netquery', 'bb_strict', 0);
            xarModSetVar('netquery', 'bb_verbose', 0);
            create_spamblockertable();
            return netquery_upgrade('4.0.2');
        case '4.0.2':
            xarModDelVar('netquery', 'capture_log_enabled');
            xarModDelVar('netquery', 'capture_log_allowuser');
            xarModDelVar('netquery', 'capture_log_filepath');
            xarModDelVar('netquery', 'capture_log_dtformat');
            xarModSetVar('netquery', 'bb_enabled', 1);
            xarModSetVar('netquery', 'bb_retention', 7);
            xarModAPIFunc('blocks', 'admin', 'register_block_type', array('modName' => 'netquery', 'blockType' => 'nqmonitor'));
            return netquery_upgrade('4.0.5');
        case '4.0.5':
            xarModSetVar('netquery', 'bb_display_stats', 'session');
            xarModAPIFunc('blocks', 'admin', 'unregister_block_type', array('modName' => 'netquery', 'blockType' => 'nqmonitor'));
            return netquery_upgrade('4.1.2');
        case '4.1.0':
        case '4.1.1':
        case '4.1.2':
            xarModSetVar('netquery', 'bb_logging', 1);
            xarModSetVar('netquery', 'bb_httpbl_key', '');
            xarModSetVar('netquery', 'bb_httpbl_threat', 25);
            xarModSetVar('netquery', 'bb_httpbl_maxage', 10);
            return netquery_upgrade('4.1.3');
        case '4.1.3':
        default:
            break;
    }
    return true;
}
function netquery_delete()
{
    xarModDelAllVars('netquery');
    if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type', array('modName' => 'netquery', 'blockType' => 'netquick'))) return;
    xarRemoveMasks('netquery');
    xarRemoveInstances('netquery');
    drop_netquery_tables();
    return true;
}
?>