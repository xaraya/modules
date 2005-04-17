<?php
function drop_exectable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ExecTable = $xartable['netquery_exec'];
    xarDBLoadTableMaintenanceAPI();
    $query = xarDBDropTable($ExecTable);
    if (empty($query)) return;
    $result = &$dbconn->Execute($query);
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
      xarErrorHandled();
    }
    return;
}
function drop_lgrequesttable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LGRequestTable = $xartable['netquery_lgrequest'];
    xarDBLoadTableMaintenanceAPI();
    $query = xarDBDropTable($LGRequestTable);
    if (empty($query)) return;
    $result = &$dbconn->Execute($query);
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
      xarErrorHandled();
    }
    return;
}
function drop_whoistable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];
    xarDBLoadTableMaintenanceAPI();
    $query = xarDBDropTable($WhoisTable);
    if (empty($query)) return;
    $result = &$dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    }
    return;
}
function create_whoistable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];
    xarDBLoadTableMaintenanceAPI();
    $whoisfields = array(
         'whois_id'     => array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'whois_ext'    => array('type'=>'varchar','size'=>10,'null'=>FALSE,'default'=>'')
        ,'whois_server' => array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>''));
    $query = xarDBCreateTable($WhoisTable,$whoisfields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    $whoisitems=array(
        array(1, '.com', 'whois.crsnic.net'),
        array(2, '.net', 'whois.crsnic.net'),
        array(3, '.edu', 'whois.crsnic.net'),
        array(4, '.org', 'whois.publicinterestregistry.net'),
        array(5, '.ca', 'whois.cira.ca'),
        array(6, '.uk', 'whois.nic.uk'),
        array(7, '.co.uk', 'whois.nic.uk'),
        array(8, '.us', 'whois.nic.us'),
        array(9, '.biz', 'whois.neulevel.biz'),
        array(10, '.info', 'whois.afilias.info'),
        array(11, '.ws', 'whois.website.ws'),
        array(12, '.name', 'whois.nic.name'),
        array(13, '.cc', 'whois.nic.cc'),
        array(14, '.cn', 'whois.cnnic.cn'),
        array(15, '.com.cn', 'whois.cnnic.cn'),
        array(16, '.net.cn', 'whois.cnnic.cn'),
        array(17, '.org.cn', 'whois.cnnic.cn'),
        array(18, '.tm', 'whois.nic.tm'),
        array(19, '.nl', 'whois.domain-registry.nl'));
    foreach ($whoisitems as $whoisitem) {
        list($id, $ext, $server) = $whoisitem;

        $query = "INSERT INTO $WhoisTable
                (whois_id, whois_ext, whois_server)
                VALUES (?,?,?)";
        $bindvars = array((int)$id, (string)$ext, (string)$server);
        $result =& $dbconn->Execute($query,$bindvars);
    }
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    }
    return;
}
function drop_lgroutertable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LGRouterTable = $xartable['netquery_lgrouter'];
    xarDBLoadTableMaintenanceAPI();
    $query = xarDBDropTable($LGRouterTable);
    if (empty($query)) return;
    $result = &$dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    }
    return;
}
function create_lgroutertable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LGRouterTable = $xartable['netquery_lgrouter'];
    xarDBLoadTableMaintenanceAPI();
    $lgrouterfields = array(
             'router_id'       => array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
            ,'router'          => array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>'')
            ,'address'         => array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>'')
            ,'username'        => array('type'=>'varchar','size'=>20,'null'=>FALSE,'default'=>'')
            ,'password'        => array('type'=>'varchar','size'=>20,'null'=>FALSE,'default'=>'')
            ,'zebra'           => array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0')
            ,'zebra_port'      => array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0')
            ,'zebra_password'  => array('type'=>'varchar','size'=>20,'null'=>FALSE,'default'=>'')
            ,'ripd'            => array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0')
            ,'ripd_port'       => array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0')
            ,'ripd_password'   => array('type'=>'varchar','size'=>20,'null'=>FALSE,'default'=>'')
            ,'ripngd'          => array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0')
            ,'ripngd_port'     => array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0')
            ,'ripngd_password' => array('type'=>'varchar','size'=>20,'null'=>FALSE,'default'=>'')
            ,'ospfd'           => array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0')
            ,'ospfd_port'      => array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0')
            ,'ospfd_password'  => array('type'=>'varchar','size'=>20,'null'=>FALSE,'default'=>'')
            ,'bgpd'            => array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0')
            ,'bgpd_port'       => array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0')
            ,'bgpd_password'   => array('type'=>'varchar','size'=>20,'null'=>FALSE,'default'=>'')
            ,'ospf6d'          => array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0')
            ,'ospf6d_port'     => array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0')
            ,'ospf6d_password' => array('type'=>'varchar','size'=>20,'null'=>FALSE,'default'=>'')
            ,'use_argc'        => array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0'));
    $query = xarDBCreateTable($LGRouterTable,$lgrouterfields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    $lgrouters=array(
        array(1, 'default', 'LG Default Settings', '', '', 1, 2601, '', 1, 2602, '', 1, 2603, '', 1, 2604, '', 1, 2605, '', 1, 2606, '', 1),
        array(2, 'ATT Public', 'route-server.ip.att.net', '', '', 1, 23, '', 0, 0, '', 0, 0, '', 1, 23, '', 1, 23, '', 0, 0, '', 1),
        array(3, 'Oregon-ix', 'route-views.oregon-ix.net', 'rviews', '', 1, 23, '', 0, 0, '', 0, 0, '', 1, 23, '', 1, 23, '', 0, 0, '', 1));
    foreach ($lgrouters as $lgrouter) {
        list($router_id, $router, $address, $username, $password,
             $zebra, $zebra_port, $zebra_password,
             $ripd, $ripd_port, $ripd_password,
             $ripngd, $ripngd_port, $ripngd_password,
             $ospfd, $ospfd_port, $ospfd_password,
             $bgpd, $pgpd_port, $pgpd_password,
             $ospf6d, $ospf6d_port, $ospf6d_password, $use_argc) = $lgrouter;
        $query = "INSERT INTO $LGRouterTable
                (router_id, router, address, username, password,
                 zebra, zebra_port, zebra_password,
                 ripd, ripd_port, ripd_password,
                 ripngd, ripngd_port, ripngd_password,
                 ospfd, ospfd_port, ospfd_password,
                 bgpd, bgpd_port, bgpd_password,
                 ospf6d, ospf6d_port, ospf6d_password, use_argc)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $bindvars = array(
              (int)$router_id, (string)$router, (string)$address, (string)$username, (string)$password,
              (int)$zebra, (int)$zebra_port, (string)$zebra_password,
              (int)$ripd, (int)$ripd_port, (string)$ripd_password,
              (int)$ripngd, (int)$ripngd_port, (string)$ripngd_password,
              (int)$ospfd, (int)$ospfd_port, (string)$ospfd_password,
              (int)$bgpd, (int)$pgpd_port, (string)$pgpd_password,
              (int)$ospf6d, (int)$ospf6d_port, (string)$ospf6d_password, (int)$use_argc);
        $result =& $dbconn->Execute($query,$bindvars);
    }
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    }
    return;
}
function drop_geocctable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $GeoccTable = $xartable['netquery_geocc'];
    xarDBLoadTableMaintenanceAPI();
    $query = xarDBDropTable($GeoccTable);
    if (empty($query)) return;
    $result = &$dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    }
    return;
}
function create_geocctable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $GeoccTable = $xartable['netquery_geocc'];
    xarDBLoadTableMaintenanceAPI();
    $geoccfields = array(
         'ci' => array('type'=>'integer','size'=>'tiny','unsigned'=>TRUE,'null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'cc' => array('type'=>'char','size'=>2,'null'=>FALSE,'default'=>'')
        ,'cn'    => array('type'=>'varchar','size'=>50,'null'=>FALSE,'default'=>'')
        ,'lat'   => array('type'=>'float','size'=>'decimal','width'=>'7','decimals'=>'4','null'=>FALSE,'default'=>'0.0000')
        ,'lon'   => array('type'=>'float','size'=>'decimal','width'=>'7','decimals'=>'4','null'=>FALSE,'default'=>'0.0000')
        ,'users' => array('type'=>'integer','size'=>'medium','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'));
    $query = xarDBCreateTable($GeoccTable,$geoccfields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    $geoccitem = array(1, 'XX', '<a href="http://virtech.org/tools/">No GeoIP</a>', '0.0000', '0.0000', '0');
    list($ci,$cc,$cn,$lat,$lon,$users) = $geoccitem;
    $query = "INSERT INTO $GeoccTable
            (ci, cc, cn, lat, lon, users)
            VALUES (?,?,?,?,?,?)";
    $bindvars = array((int)$ci, (string)$cc, (string)$cn, $lat, $lon, (int)$users);
    $result =& $dbconn->Execute($query,$bindvars);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    }
    return;
}
function drop_geoiptable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $GeoipTable = $xartable['netquery_geoip'];
    xarDBLoadTableMaintenanceAPI();
    $query = xarDBDropTable($GeoipTable);
    if (empty($query)) return;
    $result = &$dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    }
    return;
}
function create_geoiptable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $GeoipTable = $xartable['netquery_geoip'];
    xarDBLoadTableMaintenanceAPI();
    $geoipfields = array(
         'start' => array('type'=>'integer','size'=>10,'unsigned'=>TRUE,'null'=>FALSE,'default'=>'0')
        ,'end'   => array('type'=>'integer','size'=>10,'unsigned'=>TRUE,'null'=>FALSE,'default'=>'0')
        ,'ci'    => array('type'=>'integer','size'=>'tiny','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'));
    $query = xarDBCreateTable($GeoipTable,$geoipfields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    $geoipitem = array(0, 1, 1);
    list($start,$end,$ci) = $geoipitem;
    $query = "INSERT INTO $GeoipTable
            (start, end, ci)
            VALUES (?,?,?)";
    $bindvars = array($start, $end, (int)$ci);
    $result =& $dbconn->Execute($query,$bindvars);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    }
    return;
}
function drop_flagstable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $FlagsTable = $xartable['netquery_flags'];
    xarDBLoadTableMaintenanceAPI();
    $query = xarDBDropTable($FlagsTable);
    if (empty($query)) return;
    $result = &$dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    }
    return;
}
function create_flagstable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $FlagsTable = $xartable['netquery_flags'];
    xarDBLoadTableMaintenanceAPI();
    $flagfields = array(
         'flag_id'  => array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'flagnum'  => array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0')
        ,'keyword'  => array('type'=>'varchar','size'=>20,'null'=>FALSE,'default'=>'')
        ,'fontclr'  => array('type'=>'varchar','size'=>20,'null'=>FALSE,'default'=>'')
        ,'backclr'  => array('type'=>'varchar','size'=>20,'null'=>FALSE,'default'=>'')
        ,'lookup_1' => array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>'')
        ,'lookup_2' => array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>''));
    $query = xarDBCreateTable($FlagsTable,$flagfields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    $flagitems =array(
        array(1, 0, 'service', 'black', 'white', 'http://www.virtech.org/tools/#', ''),
        array(2, 99, 'pending', 'green', 'white', '', ''));
    foreach ($flagitems as $flagitem) {
        list($id,$flagnum,$keyword,$fontclr,$backclr,$lookup_1, $lookup_2) = $flagitem;
        $query = "INSERT INTO $FlagsTable
                (flag_id, flagnum, keyword, fontclr, backclr, lookup_1, lookup_2)
                VALUES (?,?,?,?,?,?,?)";
        $bindvars = array((int)$id, (int)$flagnum, (string)$keyword, (string)$fontclr, (string)$backclr, (string)$lookup_1,(string)$lookup_2);
        $result =& $dbconn->Execute($query,$bindvars);
    }
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    }
    return;
}
function drop_portstable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $PortsTable = $xartable['netquery_ports'];
    xarDBLoadTableMaintenanceAPI();
    $query = xarDBDropTable($PortsTable);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    }
    return;
}
function create_portstable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $PortsTable = $xartable['netquery_ports'];
    xarDBLoadTableMaintenanceAPI();
    $portfields = array(
         'port_id'  => array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'port'     => array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0')
        ,'protocol' => array('type'=>'varchar','size'=>3,'null'=>FALSE,'default'=>'')
        ,'service'  => array('type'=>'varchar','size'=>35,'null'=>FALSE,'default'=>'')
        ,'comment'  => array('type'=>'varchar','size'=>50,'null'=>FALSE,'default'=>'')
        ,'flag'     => array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0'));
    $query = xarDBCreateTable($PortsTable,$portfields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    $portitem = array(1, 0, 'xxx', 'Unknown', 'Port services data not installed', 0);
    list($port_id, $port, $protocol, $service, $comment, $pflag) = $portitem;
    $query = "INSERT INTO $PortsTable (
              port_id, port, protocol, service, comment, flag)
              VALUES (?,?,?,?,?,?)";
    $bindvars = array((int)$port_id, (int)$port, (string)$protocol, (string)$service, (string)$comment, (int)$pflag);
    $result =& $dbconn->Execute($query,$bindvars);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
            new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
    }
    return;
}
?>