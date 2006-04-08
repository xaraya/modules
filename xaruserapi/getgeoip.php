<?php
function netquery_userapi_getgeoip($args)
{
    extract($args);
    if (!isset($ip)) {
      if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
      } elseif ($_SERVER['REMOTE_ADDR']) {
        $ip = $_SERVER['REMOTE_ADDR'];
      } else {
        $ip = getenv('REMOTE_ADDR');
      }
    }
    $ipnum = sprintf("%u", ip2long($ip));
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $GeoipTable = $xartable['netquery_geoip'];
    $GeoccTable = $xartable['netquery_geocc'];
    $query = "SELECT cc, cn, lat, lon FROM ".$GeoipTable." NATURAL JOIN ".$GeoccTable." WHERE ? BETWEEN ipstart AND ipend";
    $bindvars = array($ipnum);
    $result =& $dbconn->Execute($query,$bindvars);
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        xarErrorHandled();
    }
    if (!$result) return;
    list($cc, $cn, $lat, $lon) = $result->fields;
    $geoflag = "modules/netquery/xarimages/geoflags/".$cc.".gif";
    if (!file_exists($geoflag)) $geoflag = "";
    if (!xarSecurityCheck('OverviewNetquery')) return;
    $geoip = array('ip'      => $ip,
                   'cc'      => $cc,
                   'cn'      => $cn,
                   'lat'     => $lat,
                   'lon'     => $lon,
                   'geoflag' => $geoflag);
    $result->Close();
    return $geoip;
}
?>