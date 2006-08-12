<?
function gmaps_admin_new(){

    $data = array();
/*
    $data['mapwidth']   = xarModVars::get('gmaps', 'mapwidth');
    $data['mapheight']  = xarModVars::get('gmaps', 'mapheight');
    $data['zoomlevel']  = xarModVars::get('gmaps', 'zoomlevel');
    $data['latitude']   = xarModVars::get('gmaps', 'latitude');
    $data['longitude']  = xarModVars::get('gmaps', 'longitude');
    $data['gmapskey']   = xarModVars::get('gmaps', 'gmapskey');
*/
//Temporary, 'cause I'm not workin' with defaults here
    $data['mapwidth']   = 800;
    $data['mapheight']  = 500;
    $data['zoomlevel']  = 1;
    $data['latitude']   = 0;
    $data['longitude']  = 30;
    $data['gmapskey']   = 'ABQIAAAA2Hq15yqt9tT8KVZMaBUu_hQSuE5V-KYDuBj6FSVq9WK3KljTSxQ8m73D5C9_VEpBBC4WY7r23GXhQw';

    return $data;
}
?>