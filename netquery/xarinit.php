<?php
/**
 * File: $Id:
 */

function netquery_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];
    $ExecTable = $xartable['netquery_exec'];
    xarDBLoadTableMaintenanceAPI();

    $execfields = array(
         'exec_id'       =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'exec_type'     =>  array('type'=>'varchar','size'=>10,'null'=>FALSE,'default'=>'')
        ,'exec_local'    =>  array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>'')
        ,'exec_winsys'   =>  array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0')
        ,'exec_remote'   =>  array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>'')
        ,'exec_remote_t' =>  array('type'=>'varchar','size'=>10,'null'=>FALSE,'default'=>'')
    );
    $query = xarDBCreateTable($ExecTable,$execfields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $whoisfields = array(
         'whois_id'     =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'whois_ext'    =>  array('type'=>'varchar','size'=>10,'null'=>FALSE,'default'=>'')
        ,'whois_server' =>  array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>'')
    );
    $query = xarDBCreateTable($WhoisTable,$whoisfields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $id = $dbconn->GenId($ExecTable);
    $query = "INSERT INTO $ExecTable (exec_id, exec_type, exec_local, exec_winsys, exec_remote, exec_remote_t) VALUES ($id, 'ping', 'ping.exe', '0', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl', 'target');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($ExecTable);
    $query = "INSERT INTO $ExecTable (exec_id, exec_type, exec_local, exec_winsys, exec_remote, exec_remote_t) VALUES ($id, 'trace', 'traceroute.exe', '0', 'http://noc.thunderworx.net/cgi-bin/public/traceroute.pl', 'target');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($ExecTable);
    $query = "INSERT INTO $ExecTable (exec_id, exec_type, exec_local, exec_winsys, exec_remote, exec_remote_t) VALUES ($id, 'log', 'var/logs/netquery.log', '0', 'var/logs/netquery.log', 'log');";
    $result =& $dbconn->Execute($query);

    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.com', 'whois.crsnic.net');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.net', 'whois.crsnic.net');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.edu', 'whois.crsnic.net');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.org', 'whois.publicinterestregistry.net');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.ca', 'whois.cira.ca');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.co.uk', 'whois.nic.uk');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.org.uk', 'whois.nic.uk');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.us', 'whois.nic.us');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.biz', 'whois.neulevel.biz');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.info', 'whois.afilias.info');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.ws', 'whois.website.ws');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.name', 'whois.nic.name');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.cc', 'whois.nic.cc');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.cn', 'whois.cnnic.cn');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.com.cn', 'whois.cnnic.cn');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.net.cn', 'whois.cnnic.cn');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.org.cn', 'whois.cnnic.cn');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.tm', 'whois.nic.tm');";
    $result =& $dbconn->Execute($query);
    $id = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_ext, whois_server) VALUES ($id, '.nl', 'whois.domain-registry.nl');";
    $result =& $dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }


    xarModSetVar('netquery', 'whois_enabled', 1);
    xarModSetVar('netquery', 'whoisip_enabled', 1);
    xarModSetVar('netquery', 'dns_lookup_enabled', 1);
    xarModSetVar('netquery', 'dns_dig_enabled', 1);
    xarModSetVar('netquery', 'ping_enabled', 1);
    xarModSetVar('netquery', 'ping_remote_enabled', 1);
    xarModSetVar('netquery', 'trace_enabled', 1);
    xarModSetVar('netquery', 'trace_remote_enabled', 1);
    xarModSetVar('netquery', 'port_check_enabled', 1);
    xarModSetVar('netquery', 'capture_log_enabled', 0);

    xarRegisterMask('OverviewNetquery','All','netquery','All','All','ACCESS_READ');
    xarRegisterMask('ReadNetquery','All','netquery','All','All','ACCESS_READ');
    xarRegisterMask('EditNetquery','All','netquery','All','All','ACCESS_EDIT');
    xarRegisterMask('AddNetquery','All','netquery','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteNetquery','All','netquery','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminNetquery','All','netquery','All','All','ACCESS_ADMIN');

    return true;
}

function netquery_upgrade($oldversion)
{
    switch ($oldversion) {
        case '0.3.1':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $ExecTable = $xartable['netquery_exec'];
            xarDBLoadTableMaintenanceAPI();
            $execfields = array(
                 'exec_id'       =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
                ,'exec_type'     =>  array('type'=>'varchar','size'=>10,'null'=>FALSE,'default'=>'')
                ,'exec_local'    =>  array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>'')
                ,'exec_winsys'   =>  array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0')
                ,'exec_remote'   =>  array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>'')
                ,'exec_remote_t' =>  array('type'=>'varchar','size'=>10,'null'=>FALSE,'default'=>'')
            );
            $query = xarDBCreateTable($ExecTable,$execfields);
            $result =& $dbconn->Execute($query);
            $id = $dbconn->GenId($ExecTable);
            $query = "INSERT INTO $ExecTable (exec_id, exec_type, exec_local, exec_winsys, exec_remote, exec_remote_t) VALUES ($id, 'ping', 'ping.exe', '0', 'http://noc.thunderworx.net/cgi-bin/public/ping.pl', 'target');";
            $result =& $dbconn->Execute($query);
            $id = $dbconn->GenId($ExecTable);
            $query = "INSERT INTO $ExecTable (exec_id, exec_type, exec_local, exec_winsys, exec_remote, exec_remote_t) VALUES ($id, 'trace', 'traceroute.exe', '0', 'http://noc.thunderworx.net/cgi-bin/public/traceroute.pl', 'target');";
            $result =& $dbconn->Execute($query);
            $id = $dbconn->GenId($ExecTable);
            $query = "INSERT INTO $ExecTable (exec_id, exec_type, exec_local, exec_winsys, exec_remote, exec_remote_t) VALUES ($id, 'log', 'var/logs/netquery.log', '0', 'var/logs/netquery.log', 'log');";
            $result =& $dbconn->Execute($query);
            xarModSetVar('netquery', 'ping_remote_enabled', 1);
            xarModSetVar('netquery', 'trace_remote_enabled', 1);
            xarModDelVar('netquery', 'localexec_enabled');
            xarModDelVar('netquery', 'windows_system');
            break;
        case '1.0.0':
        default:
            break;
    }
    return true;
}

function netquery_delete()
{
    xarModDelVar('netquery', 'capture_log_enabled');
    xarModDelVar('netquery', 'port_check_enabled');
    xarModDelVar('netquery', 'trace_remote_enabled');
    xarModDelVar('netquery', 'trace_enabled');
    xarModDelVar('netquery', 'ping_remote_enabled');
    xarModDelVar('netquery', 'ping_enabled');
    xarModDelVar('netquery', 'dns_dig_enabled');
    xarModDelVar('netquery', 'dns_lookup_enabled');
    xarModDelVar('netquery', 'whoisip_enabled');
    xarModDelVar('netquery', 'whois_enabled');

    xarRemoveMasks('netquery');
    xarRemoveInstances('netquery');

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ExecTable = $xartable['netquery_exec'];
    $WhoisTable = $xartable['netquery_whois'];
    xarDBLoadTableMaintenanceAPI();

    $query = xarDBDropTable($ExecTable);
    if (empty($query)) return;
    $result = &$dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $query = xarDBDropTable($WhoisTable);
    if (empty($query)) return;
    $result = &$dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    return true;
}

?>
