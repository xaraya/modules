<?
function maps_admin_new(){

    $data = array();
/*
    $data['mapwidth']   = xarModVars::get('maps', 'mapwidth');
    $data['mapheight']  = xarModVars::get('maps', 'mapheight');
    $data['zoomlevel']  = xarModVars::get('maps', 'zoomlevel');
    $data['latitude']   = xarModVars::get('maps', 'latitude');
    $data['longitude']  = xarModVars::get('maps', 'longitude');
    $data['mapskey']   = xarModVars::get('maps', 'mapskey');
*/
//Temporary, 'cause I'm not workin' with defaults here
    $data['mapwidth']   = 800;
    $data['mapheight']  = 500;
    $data['zoomlevel']  = 1;
    $data['latitude']   = 0;
    $data['longitude']  = 30;
    $data['mapskey']   = 'ABQIAAAA2Hq15yqt9tT8KVZMaBUu_hQSuE5V-KYDuBj6FSVq9WK3KljTSxQ8m73D5C9_VEpBBC4WY7r23GXhQw';

    return $data;
}
?>