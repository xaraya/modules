<?php
function netquery_userapi_getcountries($args)
{
    extract($args);
    if ((!isset($startnum)) || (!is_numeric($startnum))) $startnum = 1;
    if ((!isset($numitems)) || (!is_numeric($numitems))) $numitems = -1;
    $countries = array();
    if (!xarSecurityCheck('OverviewNetquery')) return $countries;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $GeoccTable = $xartable['netquery_geocc'];
    $query = "SELECT * FROM $GeoccTable WHERE users > 0 ORDER BY users DESC";
    $result =& $dbconn->SelectLimit($query, (int)$numitems, (int)$startnum-1);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext())
    {
        list($ci, $cc, $cn, $lat, $lon, $users) = $result->fields;
        $geoflag = "modules/netquery/xarimages/geoflags/".$cc.".gif";
        if (!file_exists($geoflag)) $geoflag = "modules/netquery/xarimages/geoflags/blank.gif";
        $countries[] = array('ci'      => $ci,
                             'cc'      => $cc,
                             'cn'      => $cn,
                             'lat'     => $lat,
                             'lon'     => $lon,
                             'users'   => $users,
                             'geoflag' => $geoflag);
    }
    $result->Close();
    return $countries;
}
?>