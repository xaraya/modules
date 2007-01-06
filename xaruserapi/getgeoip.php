<?php
function netquery_userapi_getgeoip($args)
{
    extract($args);
    if (!isset($ip))
    {
      if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      }
      elseif (isset($_SERVER['HTTP_CLIENT_IP']))
      {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      }
      else
      {
        $ip = $_SERVER['REMOTE_ADDR'];
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
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) xarErrorHandled();
    if (!$result) return;
    list($cc, $cn, $lat, $lon) = $result->fields;
    $geoflag = "modules/netquery/xarimages/geoflags/".$cc.".gif";
    if (!file_exists($geoflag)) $geoflag = "modules/netquery/xarimages/geoflags/blank.gif";
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