<?php
include_once ("modules/netquery/xarinitdata.php");
function netquery_init()
{
    xarModSetVar('netquery', 'capture_log_enabled', 0);
    xarModSetVar('netquery', 'whois_enabled', 1);
    xarModSetVar('netquery', 'whoisip_enabled', 1);
    xarModSetVar('netquery', 'dns_lookup_enabled', 1);
    xarModSetVar('netquery', 'dns_dig_enabled', 1);
    xarModSetVar('netquery', 'port_check_enabled', 1);
    xarModSetVar('netquery', 'http_req_enabled', 1);
    xarModSetVar('netquery', 'ping_enabled', 1);
    xarModSetVar('netquery', 'ping_remote_enabled', 1);
    xarModSetVar('netquery', 'trace_enabled', 1);
    xarModSetVar('netquery', 'trace_remote_enabled', 1);
    xarModSetVar('netquery', 'looking_glass_enabled', 1);
    xarModSetVar('netquery', 'whois_max_limit', 3);
    xarModSetVar('netquery', 'user_submissions', 1);
    xarRegisterMask('OverviewNetquery','All','netquery','All','All','ACCESS_READ');
    xarRegisterMask('ReadNetquery','All','netquery','All','All','ACCESS_READ');
    xarRegisterMask('EditNetquery','All','netquery','All','All','ACCESS_EDIT');
    xarRegisterMask('AddNetquery','All','netquery','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteNetquery','All','netquery','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminNetquery','All','netquery','All','All','ACCESS_ADMIN');
    create_exectable();
    create_flagstable();
    create_whoistable();
    create_lgrequesttable();
    create_lgroutertable();
    create_portstable();
    return true;
}
function netquery_upgrade($oldversion)
{
    switch ($oldversion) {
        case '1.0.0':
            xarModSetVar('netquery', 'looking_glass_enabled', 1);
            xarModSetVar('netquery', 'http_req_enabled', 1);
            xarModSetVar('netquery', 'whois_max_limit', 3);
            xarModSetVar('netquery', 'user_submissions', 1);
            create_flagstable();
            create_lgrequesttable();
            create_lgroutertable();
            create_portstable();
            break;
        case '1.1.0':
            xarModSetVar('netquery', 'http_req_enabled', 1);
            xarModSetVar('netquery', 'whois_max_limit', 3);
            xarModSetVar('netquery', 'user_submissions', 1);
            create_flagstable();
            create_portstable();
            break;
        case '1.2.0':
            xarModSetVar('netquery', 'whois_max_limit', 3);
            xarModSetVar('netquery', 'user_submissions', 1);
            create_flagstable();
            create_portstable();
            break;
        case '1.3.0':
            create_flagstable();
            xarModSetVar('netquery', 'user_submissions', 1);
            break;
        case '1.3.1':
            create_flagstable();
            xarModSetVar('netquery', 'user_submissions', 1);
            break;
        case '2.0.0':
            break;
        case '2.1.0':
            break;
        case '2.2.0':
        default:
            break;
    }
    return true;
}
function netquery_delete()
{
    xarModDelVar('netquery', 'user_submissions');
    xarModDelVar('netquery', 'whois_max_limit');
    xarModDelVar('netquery', 'looking_glass_enabled');
    xarModDelVar('netquery', 'trace_remote_enabled');
    xarModDelVar('netquery', 'trace_enabled');
    xarModDelVar('netquery', 'ping_remote_enabled');
    xarModDelVar('netquery', 'ping_enabled');
    xarModDelVar('netquery', 'http_req_enabled');
    xarModDelVar('netquery', 'port_check_enabled');
    xarModDelVar('netquery', 'dns_dig_enabled');
    xarModDelVar('netquery', 'dns_lookup_enabled');
    xarModDelVar('netquery', 'whoisip_enabled');
    xarModDelVar('netquery', 'whois_enabled');
    xarModDelVar('netquery', 'capture_log_enabled');
    xarRemoveMasks('netquery');
    xarRemoveInstances('netquery');
    drop_portstable();
    drop_lgroutertable();
    drop_lgrequesttable();
    drop_whoistable();
    drop_flagstable();
    drop_exectable();
    return true;
}
?>
