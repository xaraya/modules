<?php
function drop_whoistable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $WhoisTable = $xartable['netquery_whois'];
    $result = $datadict->dropTable($WhoisTable);
    if (!$result) return;
    return true;
}
function create_whoistable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $taboptarray = array('REPLACE');
    $idxoptarray = array('UNIQUE');
    $WhoisTable = $xartable['netquery_whois'];
    $WhoisFields = "
        whois_id          I          AUTO        PRIMARY,
        whois_tld         C(10)      NOTNULL     DEFAULT '',
        whois_server      C(50)      NOTNULL     DEFAULT '',
        whois_prefix      C(20)      NOTNULL     DEFAULT '',
        whois_suffix      C(20)      NOTNULL     DEFAULT '',
        whois_unfound     C(30)      NOTNULL     DEFAULT ''
    ";
    $result = $datadict->changeTable($WhoisTable, $WhoisFields);
    if (!$result) return;
    $result = $datadict->createIndex('i_' . xarDBGetSiteTablePrefix() . '_netquery_whois_1', $WhoisTable, 'whois_tld', $idxoptarray);
    if (!$result) return;
    $WhoisItems=array(
        array(1, 'ac', 'whois.nic.ac', '', '', 'No match'),
        array(2, 'ad', 'whois.ripe.net', '', '', 'no entries found'),
        array(3, 'ag', 'whois.nic.ag', '', '', 'NOT FOUND'),
        array(4, 'al', 'whois.ripe.net', '', '', 'no entries found'),
        array(5, 'am', 'amnic.net', '', '', 'No match'),
        array(6, 'as', 'whois.nic.as', '', '', 'Domain Not Found'),
        array(7, 'at', 'whois.nic.at', '', '', 'nothing found'),
        array(8, 'au', 'whois.ausregistry.com.au', '', '', 'No Data Found'),
        array(9, 'az', 'whois.ripe.net', '', '', 'no entries found'),
        array(10, 'ba', 'whois.ripe.net', '', '', 'no entries found'),
        array(11, 'be', 'whois.dns.be', '', '', ' FREE\n'),
        array(12, 'bg', 'whois.ripe.net', '', '', 'no entries found'),
        array(13, 'biz', 'whois.neulevel.biz', '', '', 'Not found'),
        array(14, 'br', 'whois.nic.br', '', '', 'No match for domain'),
        array(15, 'by', 'whois.ripe.net', '', '', 'no entries found'),
        array(16, 'bz', 'whois.belizenic.bz', '', '', 'NOMATCH'),
        array(17, 'ca', 'whois.cira.ca', '', '', ' AVAIL\n'),
        array(18, 'cat', 'whois.cat', '', '', 'NOT FOUND'),
        array(19, 'cc', 'whois.nic.cc', '', '', 'No match'),
        array(20, 'ch', 'whois.nic.ch', '', '', 'We do not have an entry'),
        array(21, 'cl', 'nic.cl', '', '', 'no existe'),
        array(22, 'cn', 'whois.cnnic.net.cn', '', '', 'no matching record'),
        array(23, 'com', 'whois.crsnic.net', '', '', 'No match'),
        array(24, 'coop', 'whois.nic.coop', '', '', 'No domain records were found'),
        array(25, 'cx', 'whois.nic.cx', '', '', 'Not Registered'),
        array(26, 'cy', 'whois.ripe.net', '', '', 'no entries found'),
        array(27, 'cz', 'whois.nic.cz', '', '', 'No data found'),
        array(28, 'de', 'whois.denic.de', '-T ace,dn', '', 'not found in database'),
        array(29, 'dk', 'whois.dk-hostmaster.dk', '', '', 'No entries found'),
        array(30, 'dz', 'whois.ripe.net', '', '', 'no entries found'),
        array(31, 'edu', 'whois.educause.net', '', '', 'No Match'),
        array(32, 'ee', 'whois.eenet.ee', '', '', 'NOT FOUND'),
        array(33, 'eg', 'whois.ripe.net', '', '', 'no entries found'),
        array(34, 'es', 'whois.ripe.net', '', '', 'no entries found'),
        array(35, 'eu', 'whois.eu', '', '', ' FREE\n'),
        array(36, 'fi', 'whois.ficora.fi', '', '', 'Domain not found'),
        array(37, 'fo', 'whois.ripe.net', '', '', 'no entries found'),
        array(38, 'fr', 'whois.nic.fr', '', '', 'no entries found'),
        array(39, 'ga', 'whois.ripe.net', '', '', 'no entries found'),
        array(40, 'gb', 'whois.ripe.net', '', '', 'no entries found'),
        array(41, 'ge', 'whois.ripe.net', '', '', 'no entries found'),
        array(42, 'gg', 'whois.isles.net', '', '', 'Domain not found'),
        array(43, 'gl', 'whois.ripe.net', '', '', 'no entries found'),
        array(44, 'gm', 'whois.ripe.net', '', '', 'no entries found'),
        array(45, 'gr', 'whois.ripe.net', '', '', 'no entries found'),
        array(46, 'gs', 'whois.adamsnames.tc', '', '', 'is not registered'),
        array(47, 'hk', 'ns1.hkdnr.net.hk', '', '', 'Domain name not found'),
        array(48, 'hm', 'whois.registry.hm', '', '', 'null'),
        array(49, 'hr', 'whois.ripe.net', '', '', 'no entries found'),
        array(50, 'ie', 'whois.domainregistry.ie', '', '', 'Not Registered'),
        array(51, 'il', 'whois.isoc.org.il', '', '', 'No data was found'),
        array(52, 'in', 'whois.inregistry.net', '', '', 'NOT FOUND'),
        array(53, 'info', 'whois.afilias.net', '', '', 'NOT FOUND'),
        array(54, 'int', 'whois.iana.org', '', '', 'not found'),
        array(55, 'io', 'whois.nic.io', '', '', 'No match'),
        array(56, 'ir', 'whois.nic.ir', '', '', 'no entries found'),
        array(57, 'is', 'whois.isnet.is', '', '', 'No entries found'),
        array(58, 'it', 'whois.nic.it', '', '', 'No entries found'),
        array(59, 'je', 'whois.isles.net', '', '', 'Domain not found'),
        array(60, 'jo', 'whois.ripe.net', '', '', 'no entries found'),
        array(61, 'jp', 'whois.jprs.jp', '', '/e', 'No match'),
        array(62, 'kr', 'whois.krnic.net', '', '', 'is not registered'),
        array(63, 'la', 'whois2.afilias-grs.net', '', '', 'NOT FOUND'),
        array(64, 'li', 'whois.nic.li', '', '', 'do not have an entry'),
        array(65, 'lt', 'whois.domreg.lt', '', '', 'No matches found'),
        array(66, 'lu', 'whois.dns.lu', '', '', 'No such domain'),
        array(67, 'lv', 'whois.ripe.net', '', '', 'Nothing found'),
        array(68, 'ma', 'whois.ripe.net', '', '', 'no entries found'),
        array(69, 'mc', 'whois.ripe.net', '', '', 'no entries found'),
        array(70, 'md', 'whois.ripe.net', '', '', 'no entries found'),
        array(71, 'mk', 'whois.ripe.net', '', '', 'no entries found'),
        array(72, 'ms', 'whois.adamsnames.tc', '', '', 'is not registered'),
        array(73, 'mt', 'whois.ripe.net', '', '', 'no entries found'),
        array(74, 'museum', 'whois.museum', '', '', 'has not been delegated'),
        array(75, 'mx', 'whois.nic.mx', '', '', 'No Encontradas'),
        array(76, 'my', 'whois2.mynic.net.my', '', '', 'does not Exist'),
        array(77, 'name', 'whois.nic.name', '', '', 'No Match'),
        array(78, 'net', 'whois.crsnic.net', '', '', 'No Match'),
        array(79, 'nl', 'whois.domain-registry.nl', '', '', 'is free'),
        array(80, 'no', 'whois.norid.no', '', '', 'no matches'),
        array(81, 'nu', 'whois.nic.nu', '', '', 'NO MATCH'),
        array(82, 'nz', 'whois.srs.net.nz', '', '', 'not managed by this register'),
        array(83, 'org', 'whois.publicinterestregistry.net', '', '', 'NOT FOUND'),
        array(84, 'pl', 'whois.dns.pl', '', '', 'No information'),
        array(85, 'pt', 'hercules.dns.pt', '', '', 'no match'),
        array(86, 're', 'winter.nic.fr', '', '', 'No entries found'),
        array(87, 'ro', 'whois.rotld.ro', '', '', 'No entries found'),
        array(88, 'ru', 'whois.ripn.net', '', '', 'No entries found'),
        array(89, 'sa', 'arabic-domains.org.sa', '', '', 'No match'),
        array(90, 'se', 'whois.nic-se.se', '', '', 'No data found'),
        array(91, 'sg', 'whois.nic.net.sg', '', '', 'NOMATCH'),
        array(92, 'sh', 'whois.nic.sh', '', '', 'No match'),
        array(93, 'si', 'whois.arnes.si', '', '', 'No entries found'),
        array(94, 'sk', 'whois.sk-nic.sk', '', '', 'Not found'),
        array(95, 'sm', 'whois.ripe.net', '', '', 'No entries found'),
        array(96, 'st', 'whois.nic.st', '', '', 'No entries found'),
        array(97, 'su', 'whois.ripn.net', '', '', 'No entries found'),
        array(98, 'tc', 'whois.adamsnames.tc', '', '', 'is not registered'),
        array(99, 'tf', 'winter.nic.fr', '', '', 'No entries found'),
        array(100, 'th', 'whois.thnic.net', '', '', 'No entries found'),
        array(101, 'tk', 'whois.dot.tk', '', '', 'domain name not known'),
        array(102, 'tn', 'whois.ripe.net', '', '', 'No entries found'),
        array(103, 'to', 'monarch.tonic.to', '', '', 'No match'),
        array(104, 'tr', 'whois.metu.edu.tr', '', '', 'No match'),
        array(105, 'tv', 'whois.nic.tv', '', '', 'No match'),
        array(106, 'tw', 'whois.twnic.net.tw', '', '', 'No found'),
        array(107, 'ua', 'whois.net.ua', '', '', 'No entries found'),
        array(108, 'uk', 'whois.nic.uk', '', '', 'No match'),
        array(109, 'us', 'whois.nic.us', '', '', 'Not found'),
        array(110, 'va', 'whois.ripe.net', '', '', 'No entries found'),
        array(111, 'vg', 'whois.adamsnames.tc', '', '', 'is not registered'),
        array(112, 'ws', 'whois.worldsite.ws', '', '', 'No match'),
        array(113, 'yu', 'whois.ripe.net', '', '', 'No entries found'));
    foreach ($WhoisItems as $WhoisItem) {
        list($id, $tld, $server, $prefix, $suffix, $unfound) = $WhoisItem;
        $query = "INSERT INTO $WhoisTable
                (whois_id, whois_tld, whois_server, whois_prefix, whois_suffix, whois_unfound)
                VALUES (?,?,?,?,?,?)";
        $bindvars = array((int)$id, (string)$tld, (string)$server, (string)$prefix, (string)$suffix, (string)$unfound);
        $result =& $dbconn->Execute($query,$bindvars);
    }
    if ($dbconn->ErrorNo() != 0) return;
    return true;
}
function drop_lgroutertable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $LGRouterTable = $xartable['netquery_lgrouter'];
    $result = $datadict->dropTable($LGRouterTable);
    if (!$result) return;
    return true;
}
function create_lgroutertable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $taboptarray = array('REPLACE');
    $idxoptarray = array('UNIQUE');
    $LGRouterTable = $xartable['netquery_lgrouter'];
    $LGRouterFields = "
        router_id         I          AUTO        PRIMARY,
        router            C(100)     NOTNULL     DEFAULT '',
        address           C(100)     NOTNULL     DEFAULT '',
        username          C(20)      NOTNULL     DEFAULT '',
        password          C(20)      NOTNULL     DEFAULT '',
        zebra             L          NOTNULL     DEFAULT 0,
        zebra_port        I          NOTNULL     DEFAULT 0,
        zebra_password    C(20)      NOTNULL     DEFAULT '',
        ripd              L          NOTNULL     DEFAULT 0,
        ripd_port         I          NOTNULL     DEFAULT 0,
        ripd_password     C(20)      NOTNULL     DEFAULT '',
        ripngd            L          NOTNULL     DEFAULT 0,
        ripngd_port       I          NOTNULL     DEFAULT 0,
        ripngd_password   C(20)      NOTNULL     DEFAULT '',
        ospfd             L          NOTNULL     DEFAULT 0,
        ospfd_port        I          NOTNULL     DEFAULT 0,
        ospfd_password    C(20)      NOTNULL     DEFAULT '',
        bgpd              L          NOTNULL     DEFAULT 0,
        bgpd_port         I          NOTNULL     DEFAULT 0,
        bgpd_password     C(20)      NOTNULL     DEFAULT '',
        ospf6d            L          NOTNULL     DEFAULT 0,
        ospf6d_port       I          NOTNULL     DEFAULT 0,
        ospf6d_password   C(20)      NOTNULL     DEFAULT '',
        use_argc          L          NOTNULL     DEFAULT 0
    ";
    $result = $datadict->changeTable($LGRouterTable, $LGRouterFields);
    if (!$result) return;
    $result = $datadict->createIndex('i_' . xarDBGetSiteTablePrefix() . '_netquery_lgrouter_1', $LGRouterTable, 'router', $idxoptarray);
    if (!$result) return;
    $LGRouters=array(
        array(1, 'default', 'LG Default Settings', '', '', 1, 2601, '', 1, 2602, '', 1, 2603, '', 1, 2604, '', 1, 2605, '', 1, 2606, '', 1),
        array(2, 'ATT Public', 'route-server.ip.att.net', '', '', 1, 23, '', 0, 0, '', 0, 0, '', 1, 23, '', 1, 23, '', 0, 0, '', 1),
        array(3, 'Oregon-ix', 'route-views.oregon-ix.net', 'rviews', '', 1, 23, '', 0, 0, '', 0, 0, '', 1, 23, '', 1, 23, '', 0, 0, '', 1));
    foreach ($LGRouters as $LGRouter) {
        list($router_id, $router, $address, $username, $password,
             $zebra, $zebra_port, $zebra_password,
             $ripd, $ripd_port, $ripd_password,
             $ripngd, $ripngd_port, $ripngd_password,
             $ospfd, $ospfd_port, $ospfd_password,
             $bgpd, $pgpd_port, $pgpd_password,
             $ospf6d, $ospf6d_port, $ospf6d_password, $use_argc) = $LGRouter;
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
    if ($dbconn->ErrorNo() != 0) return;
    return true;
}
function drop_geocctable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $GeoccTable = $xartable['netquery_geocc'];
    $result = $datadict->dropTable($GeoccTable);
    if (!$result) return;
    return true;
}
function create_geocctable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $taboptarray = array('REPLACE');
    $idxoptarray = array('UNIQUE');
    $GeoccTable = $xartable['netquery_geocc'];
    $GeoccFields = "
        ci                I1         AUTO        PRIMARY         UNSIGNED,
        cc                C(2)       NOTNULL     DEFAULT '',
        cn                C(50)      NOTNULL     DEFAULT '',
        lat               N(7.4)     NOTNULL     DEFAULT 0.0000,
        lon               N(7.4)     NOTNULL     DEFAULT 0.0000,
        users             I          NOTNULL     DEFAULT 0       UNSIGNED
    ";
    $result = $datadict->changeTable($GeoccTable, $GeoccFields);
    if (!$result) return;
    $GeoccItem = array(1, 'XX', '<a href="http://virtech.org/tools/">No GeoIP</a>', '0.0000', '0.0000', '0');
    list($ci,$cc,$cn,$lat,$lon,$users) = $GeoccItem;
    $query = "INSERT INTO $GeoccTable
            (ci, cc, cn, lat, lon, users)
            VALUES (?,?,?,?,?,?)";
    $bindvars = array((int)$ci, (string)$cc, (string)$cn, $lat, $lon, (int)$users);
    $result =& $dbconn->Execute($query,$bindvars);
    if ($dbconn->ErrorNo() != 0) return;
    return true;
}
function drop_geoiptable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $GeoipTable = $xartable['netquery_geoip'];
    $result = $datadict->dropTable($GeoipTable);
    if (!$result) return;
    return true;
}
function revise_geoiptable()
{
    $database_type = xarCore_getSystemvar('DB.Type');
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $GeoipTable = $xartable['netquery_geoip'];
    $result = $datadict->addColumn($GeoipTable, 'ipstart I8 NOTNULL DEFAULT 0 UNSIGNED');
    if (!$result) return;
    $result = $datadict->addColumn($GeoipTable, 'ipend I8 NOTNULL DEFAULT 0 UNSIGNED');
    if (!$result) return;
    $sql = 'UPDATE '.$GeoipTable.' SET ipstart = start';
    $result =& $dbconn->Execute($sql);
    if (!$result) return;
    $sql = 'UPDATE '.$GeoipTable.' SET ipend = end';
    $result =& $dbconn->Execute($sql);
    if (!$result) return;
    if ($database_type != 'sqlite') {
        $result = $datadict->dropColumn($GeoipTable, 'start');
        if (!$result) return;
        $result = $datadict->dropColumn($GeoipTable, 'end');
        if (!$result) return;
    }
    return true;
}
function create_geoiptable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $taboptarray = array('REPLACE');
    $idxoptarray = array('UNIQUE');
    $GeoipTable = $xartable['netquery_geoip'];
    $GeoipFields = "
        ipstart           I8         NOTNULL     DEFAULT 0       UNSIGNED,
        ipend             I8         NOTNULL     DEFAULT 0       UNSIGNED,
        ci                I1         NOTNULL     DEFAULT 0       UNSIGNED
    ";
    $result = $datadict->changeTable($GeoipTable, $GeoipFields);
    if (!$result) return;
    $GeoipItem = array(0, 1, 1);
    list($start,$end,$ci) = $GeoipItem;
    $query = "INSERT INTO $GeoipTable
            (ipstart, ipend, ci)
            VALUES (?,?,?)";
    $bindvars = array($start, $end, (int)$ci);
    $result =& $dbconn->Execute($query,$bindvars);
    if ($dbconn->ErrorNo() != 0) return;
    return true;
}
function drop_flagstable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $FlagsTable = $xartable['netquery_flags'];
    $result = $datadict->dropTable($FlagsTable);
    if (!$result) return;
    return true;
}
function create_flagstable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $taboptarray = array('REPLACE');
    $idxoptarray = array('UNIQUE');
    $FlagsTable = $xartable['netquery_flags'];
    $FlagsFields = "
        flag_id           I          AUTO        PRIMARY,
        flagnum           I          NOTNULL     DEFAULT 0,
        keyword           C(20)      NOTNULL     DEFAULT '',
        fontclr           C(20)      NOTNULL     DEFAULT '',
        backclr           C(20)      NOTNULL     DEFAULT '',
        lookup_1          C(100)     NOTNULL     DEFAULT '',
        lookup_2          C(100)     NOTNULL     DEFAULT ''
    ";
    $result = $datadict->changeTable($FlagsTable, $FlagsFields);
    if (!$result) return;
    $result = $datadict->createIndex('i_' . xarDBGetSiteTablePrefix() . '_netquery_flags_1', $FlagsTable, 'flagnum', $idxoptarray);
    if (!$result) return;
    $FlagItems =array(
        array(1, 0, 'service', 'black', 'white', 'http://www.virtech.org/tools/#', ''),
        array(2, 99, 'pending', 'green', 'white', '', ''));
    foreach ($FlagItems as $FlagItem) {
        list($id,$flagnum,$keyword,$fontclr,$backclr,$lookup_1, $lookup_2) = $FlagItem;
        $query = "INSERT INTO $FlagsTable
                (flag_id, flagnum, keyword, fontclr, backclr, lookup_1, lookup_2)
                VALUES (?,?,?,?,?,?,?)";
        $bindvars = array((int)$id, (int)$flagnum, (string)$keyword, (string)$fontclr, (string)$backclr, (string)$lookup_1,(string)$lookup_2);
        $result =& $dbconn->Execute($query,$bindvars);
    }
    if ($dbconn->ErrorNo() != 0) return;
    return true;
}
function drop_portstable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $PortsTable = $xartable['netquery_ports'];
    $result = $datadict->dropTable($PortsTable);
    if (!$result) return;
    return true;
}
function create_portstable()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $taboptarray = array('REPLACE');
    $idxoptarray = array('UNIQUE');
    $PortsTable = $xartable['netquery_ports'];
    $PortsFields = "
        port_id           I          AUTO        PRIMARY,
        port              I          NOTNULL     DEFAULT 0,
        protocol          C(3)       NOTNULL     DEFAULT '',
        service           C(35)      NOTNULL     DEFAULT '',
        comment           C(50)      NOTNULL     DEFAULT '',
        flag              I1         NOTNULL     DEFAULT 0
    ";
    $result = $datadict->changeTable($PortsTable, $PortsFields);
    if (!$result) return;
    $PortItem = array(1, 0, 'xxx', 'Unknown', 'Port services data not installed', 0);
    list($port_id, $port, $protocol, $service, $comment, $pflag) = $PortItem;
    $query = "INSERT INTO $PortsTable (
              port_id, port, protocol, service, comment, flag)
              VALUES (?,?,?,?,?,?)";
    $bindvars = array((int)$port_id, (int)$port, (string)$protocol, (string)$service, (string)$comment, (int)$pflag);
    $result =& $dbconn->Execute($query,$bindvars);
    if ($dbconn->ErrorNo() != 0) return;
    return true;
}
function drop_netquery_tables()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $GeoccTable = $xartable['netquery_geoip'];
    $GeoipTable = $xartable['netquery_geocc'];
    $PortsTable = $xartable['netquery_ports'];
    $FlagsTable = $xartable['netquery_flags'];
    $LGRouterTable = $xartable['netquery_lgrouter'];
    $WhoisTable = $xartable['netquery_whois'];
    $result = $datadict->dropTable($GeoipTable);
    if (!$result) return;
    $result = $datadict->dropTable($GeoccTable);
    if (!$result) return;
    $result = $datadict->dropTable($PortsTable);
    if (!$result) return;
    $result = $datadict->dropTable($FlagsTable);
    if (!$result) return;
    $result = $datadict->dropTable($LGRouterTable);
    if (!$result) return;
    $result = $datadict->dropTable($WhoisTable);
    if (!$result) return;
    return true;
}
?>